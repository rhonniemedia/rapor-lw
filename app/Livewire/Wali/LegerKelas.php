<?php

namespace App\Livewire\Wali;

use App\Models\Nilai;
use App\Models\Rombel;
use Livewire\Component;
use App\Models\Kehadiran;
use App\Models\Pengaturan;
use App\Models\DataSekolah;
use App\Models\TahunAjaranSemester;
use App\Models\RombelPelajar;
use Illuminate\Support\Facades\Auth;

// ✅ TIDAK PERLU Layout attribute karena pakai pattern view → livewire
class LegerKelas extends Component
{
    // ========================================
    // PROPERTIES
    // ========================================

    public $rombel;
    public $semesterAktif;
    public $dataSekolah = null;
    public $pengaturan = null;

    // Data Collections
    public $studentsList = [];
    public $mataPelajaranList = [];
    public $pdfUrl;

    // ========================================
    // MOUNT
    // ========================================

    public function mount()
    {
        $this->loadRombelWaliKelas();

        if (!$this->rombel) {
            session()->flash('error', 'Anda tidak memiliki kelas binaan.');
            return redirect()->route('walikelas.dashboard');
        }

        $this->loadSemesterAktif();

        if (!$this->semesterAktif) {
            session()->flash('warning', 'Tidak ada semester aktif saat ini.');
            return;
        }

        $this->loadDataSekolah();
        $this->loadPengaturan();
        $this->loadMataPelajaranList();
        $this->loadStudentsWithNilai();
        $this->generatePdfUrl();
    }

    // ========================================
    // INITIALIZATION METHODS
    // ========================================

    private function loadRombelWaliKelas(): void
    {
        $user = Auth::user();

        $this->rombel = Rombel::with([
            'tahunAjaranKurikulum.tahunAjaran',
            'tahunAjaranKurikulum.kurikulum',
            'waliKelas',
            'jurusan'
        ])->where('wali_kelas_slug', $user->slug)->first();
    }

    private function loadSemesterAktif(): void
    {
        $this->semesterAktif = TahunAjaranSemester::where('status', 'aktif')
            ->with(['semester', 'tahunAjaran'])
            ->first();
    }

    private function loadDataSekolah(): void
    {
        $this->dataSekolah = DataSekolah::first();
    }

    private function loadPengaturan(): void
    {
        $this->pengaturan = Pengaturan::with('kepalaSekolah')
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->first();
    }

    // ========================================
    // LOAD MATA PELAJARAN LIST
    // ========================================

    private function loadMataPelajaranList(): void
    {
        if (!$this->rombel || !$this->semesterAktif) {
            $this->mataPelajaranList = [];
            return;
        }

        // Get kurikulum_id dari rombel
        $kurikulumId = $this->rombel->tahunAjaranKurikulum->kurikulum_id ?? null;

        if (!$kurikulumId) {
            $this->mataPelajaranList = [];
            return;
        }

        // Get list mata pelajaran dari kurikulum dengan urutan
        $mataPelajarans = \DB::table('kurikulum_mata_pelajarans as kmp')
            ->join('mata_pelajarans as mp', 'kmp.mata_pelajaran_id', '=', 'mp.id')
            ->join('mata_pelajaran_kelompoks as mpk', 'kmp.kelompok_id', '=', 'mpk.id')
            ->where('kmp.kurikulum_id', $kurikulumId)
            ->select(
                'mp.id',
                'mp.nama',
                'mp.kode',
                'mpk.nama as kelompok_nama',
                'mpk.kode as kelompok_kode',
                'kmp.urutan'
            )
            ->orderBy('kmp.urutan', 'asc')
            ->get();

        $this->mataPelajaranList = $mataPelajarans->toArray();
    }

    // ========================================
    // LOAD STUDENTS WITH NILAI
    // ========================================

    private function loadStudentsWithNilai(): void
    {
        if (!$this->rombel || !$this->semesterAktif || empty($this->mataPelajaranList)) {
            $this->studentsList = [];
            return;
        }

        // Get kurikulum_id
        $kurikulumId = $this->rombel->tahunAjaranKurikulum->kurikulum_id ?? null;

        if (!$kurikulumId) {
            $this->studentsList = [];
            return;
        }

        // Load students dari rombel
        $rombelPelajars = RombelPelajar::with('pelajar')
            ->where('rombel_id', $this->rombel->id)
            ->orderBy('id', 'asc')
            ->get();

        $this->studentsList = $rombelPelajars->map(function ($rombelPelajar, $index) use ($kurikulumId) {
            $pelajar = $rombelPelajar->pelajar;

            // Load nilai untuk semua mata pelajaran
            $nilaiPerMapel = [];
            $totalNilai = 0;
            $jumlahMapelDiisi = 0;
            $ketuntasan = 0; // Jumlah mata pelajaran yang tuntas

            foreach ($this->mataPelajaranList as $mapel) {
                $nilai = Nilai::where('pelajar_id', $pelajar->id)
                    ->where('mata_pelajaran_id', $mapel->id)
                    ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                    ->first();

                $nilaiAngka = $nilai ? round($nilai->nilai_angka ?? 0) : 0;
                $nilaiPerMapel[$mapel->id] = $nilaiAngka;

                if ($nilaiAngka > 0) {
                    $totalNilai += $nilaiAngka;
                    $jumlahMapelDiisi++;

                    // Hitung ketuntasan (asumsi KKM = 75)
                    $kkm = $this->pengaturan->kkm ?? 75;
                    if ($nilaiAngka >= $kkm) {
                        $ketuntasan++;
                    }
                }
            }

            // Hitung rata-rata
            $rataRata = $jumlahMapelDiisi > 0 ? round($totalNilai / $jumlahMapelDiisi, 1) : 0;

            // Tentukan predikat berdasarkan rata-rata
            $predikat = $this->getPredikat($rataRata);

            // Load kehadiran
            $kehadiran = Kehadiran::where('pelajar_id', $pelajar->id)
                ->where('rombel_id', $this->rombel->id)
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                ->first();

            return [
                'no' => $index + 1,
                'id' => $pelajar->id,
                'nis' => $pelajar->nomor_induk ?? '-',
                'nisn' => $pelajar->nisn ?? '-',
                'nama' => $pelajar->nama_lengkap ?? 'N/A',
                'jenis_kelamin' => $pelajar->jenis_kelamin === 'laki-laki' ? 'L' : 'P',
                'nilai_per_mapel' => $nilaiPerMapel,
                'ketuntasan' => $ketuntasan,
                'jumlah_nilai' => $totalNilai,
                'rata_rata' => $rataRata,
                'predikat' => $predikat,
                'sakit' => $kehadiran->jumlah_sakit ?? 0,
                'izin' => $kehadiran->jumlah_izin ?? 0,
                'tanpa_keterangan' => $kehadiran->jumlah_tanpa_keterangan ?? 0,
            ];
        })->toArray();
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    private function getPredikat($nilai): string
    {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        if ($nilai >= 60) return 'D';
        return 'E';
    }

    // ========================================
    // PDF GENERATION
    // ========================================

    public function generatePdfUrl()
    {
        if (!$this->rombel || !$this->semesterAktif) {
            $this->pdfUrl = '';
            return;
        }

        // Prepare data untuk PDF
        $pdfData = [
            'sekolah' => [
                'nama_sekolah' => $this->dataSekolah->nama_sekolah ?? 'N/A',
                'npsn' => $this->dataSekolah->npsn ?? 'N/A',
                'alamat' => $this->dataSekolah->alamat ?? 'N/A',
                'kota_kabupaten' => $this->dataSekolah->kota_kabupaten ?? 'N/A',
                'logo_sekolah_path' => $this->dataSekolah->logo_sekolah_path ?? null,
            ],
            'tahun_ajaran' => $this->semesterAktif->tahunAjaran->nama ?? 'N/A',
            'semester_nama' => $this->semesterAktif->semester->nama ?? 'N/A',
            'kelas' => $this->rombel->nama ?? 'N/A',
            'kkm' => $this->pengaturan->kkm ?? 75,
            'wali_kelas' => [
                'nama' => $this->rombel->waliKelas->name ?? 'N/A',
                'nip' => $this->rombel->waliKelas->nip ?? 'N/A'
            ],
            'kepala_sekolah' => [
                'nama' => $this->pengaturan->kepalaSekolah->name ?? 'N/A',
                'nip' => $this->pengaturan->kepalaSekolah->nip ?? 'N/A'
            ],
            'tanggal_rapor' => $this->pengaturan->tanggal_rapor ?? now()->format('Y-m-d'),
            'mata_pelajaran' => $this->mataPelajaranList,
            'students' => $this->studentsList,
        ];

        // Encode data
        $encodedData = base64_encode(json_encode($pdfData));

        // Generate URL
        $this->pdfUrl = route('pdf.generate.leger') . '?data=' . $encodedData;
    }

    // ========================================
    // RENDER METHOD
    // ========================================

    public function render()
    {
        return view('livewire.wali.leger-kelas', [
            'totalStudents' => count($this->studentsList),
            'totalMataPelajaran' => count($this->mataPelajaranList),
        ]);
    }
}
