<?php

namespace App\Livewire\Wali;

use App\Models\Nilai;
use App\Models\Rombel;
use Livewire\Component;
use App\Models\Kehadiran;
use App\Models\Pengaturan;
use App\Models\DataSekolah;
use App\Models\Kokurikuler;
use App\Models\RombelPelajar;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\KurikulumMataPelajaran;

class PreviewLeger extends Component
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
    public $pdfUrl = '';

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
        // 1. Validasi
        $kurikulumId = $this->rombel->tahunAjaranKurikulum->kurikulum_id ?? null;
        $tingkatRombel = $this->rombel->tingkat ?? null;

        if (!$this->rombel || !$kurikulumId || !$tingkatRombel) {
            $this->mataPelajaranList = [];
            return;
        }

        // 2. QUERY UTAMA: Ambil Mapel dari RombelPengajar dengan FILTER TINGKAT
        $allMapel = \App\Models\RombelPengajar::query()
            ->where('rombel_pengajars.rombel_id', $this->rombel->id)
            // Join ke Mata Pelajaran
            ->join('mata_pelajarans as mp', 'rombel_pengajars.mata_pelajaran_id', '=', 'mp.id')
            // Join ke Kurikulum Mapel dengan FILTER TINGKAT
            ->join('kurikulum_mata_pelajarans as kmp', function ($join) use ($kurikulumId, $tingkatRombel) {
                $join->on('mp.id', '=', 'kmp.mata_pelajaran_id')
                    ->where('kmp.kurikulum_id', '=', $kurikulumId)
                    ->where('kmp.tingkat', '=', $tingkatRombel);
            })
            // Join ke Kelompok
            ->join('mata_pelajaran_kelompoks as mpk', 'kmp.kelompok_id', '=', 'mpk.id')
            ->select(
                'mp.id',
                'mp.nama',
                'mp.kode',
                'mp.is_mapel_agama',
                'mpk.kode as kelompok_kode',
                'mpk.nama as kelompok_nama',
                'kmp.urutan'
            )
            ->orderBy('kmp.urutan', 'asc')
            ->get();

        // 3. LOGIKA AGAMA: Gabungkan semua mapel Agama menjadi satu kolom "PABP"
        $agamaMapels = $allMapel->filter(fn($item) => $item->is_mapel_agama == true || $item->is_mapel_agama == 1);
        $nonAgamaMapels = $allMapel->filter(fn($item) => !$item->is_mapel_agama || $item->is_mapel_agama == 0);

        $combined = collect();

        // Jika ada mapel agama, ambil satu saja sebagai perwakilan kolom
        if ($agamaMapels->isNotEmpty()) {
            $combined->push($agamaMapels->first());
        }

        // Gabungkan dengan mapel umum
        $combined = $combined->merge($nonAgamaMapels);

        // 4. MAPPING FINAL - KONSISTEN DENGAN ADMIN (RETURN ARRAY, BUKAN OBJECT)
        $mataPelajarans = $combined
            ->sortBy(fn($item) => [$item->kelompok_kode, $item->urutan])
            ->map(function ($item) {
                $isAgama = $item->is_mapel_agama == true || $item->is_mapel_agama == 1;
                return [
                    'id'             => $isAgama ? 'agama' : $item->id,
                    'nama'           => $isAgama ? 'Pendidikan Agama dan Budi Pekerti' : $item->nama,
                    'kode'           => $isAgama ? 'PABP' : $item->kode,
                    'kelompok_kode'  => $item->kelompok_kode,
                    'kelompok_nama'  => $item->kelompok_nama,
                    'urutan'         => $item->urutan,
                    'is_agama'       => $isAgama,
                ];
            });

        $this->mataPelajaranList = $mataPelajarans->values()->toArray();
    }

    // ========================================
    // LOAD STUDENTS WITH NILAI
    // ========================================

    private function loadStudentsWithNilai(): void
    {
        // 1. VALIDASI AWAL
        if (!$this->rombel || !$this->semesterAktif || empty($this->mataPelajaranList)) {
            $this->studentsList = [];
            return;
        }

        $kurikulumId = $this->rombel->tahunAjaranKurikulum->kurikulum_id ?? null;
        if (!$kurikulumId) {
            $this->studentsList = [];
            return;
        }

        // 2. LOAD SISWA DALAM ROMBEL
        $rombelPelajars = RombelPelajar::with('pelajar')
            ->where('rombel_id', $this->rombel->id)
            ->get();

        $totalMapelWajib = count($this->mataPelajaranList);
        // Ambil urutan ID mapel untuk digunakan sebagai tie-breaker peringkat nanti
        $mapelOrderIds = array_column($this->mataPelajaranList, 'id');

        // 3. MAPPING DATA SISWA & PERHITUNGAN NILAI
        $studentsData = $rombelPelajars->map(function ($rombelPelajar, $index) use ($totalMapelWajib) {
            $pelajar = $rombelPelajar->pelajar;
            $nilaiPerMapel = [];
            $totalNilai = 0;

            foreach ($this->mataPelajaranList as $mapel) {
                // Logika pengambilan nilai (Agama vs Umum)
                if (isset($mapel['is_agama']) && $mapel['is_agama'] === true) {
                    $nilai = Nilai::where('pelajar_id', $pelajar->id)
                        ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                        ->whereHas('mataPelajaran', fn($q) => $q->where('is_mapel_agama', true))
                        ->latest('updated_at')
                        ->first();
                } else {
                    $nilai = Nilai::where('pelajar_id', $pelajar->id)
                        ->where('mata_pelajaran_id', $mapel['id'])
                        ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                        ->first();
                }

                $nilaiAngka = $nilai ? round($nilai->nilai_angka ?? 0) : 0;
                
                // Simpan nilai per mapel menggunakan ID mapel sebagai key
                $nilaiPerMapel[$mapel['id']] = $nilaiAngka;
                $totalNilai += $nilaiAngka;
            }

            $rataRata = $totalMapelWajib > 0 ? round($totalNilai / $totalMapelWajib, 1) : 0;

            // Load Data Penunjang
            $kokurikuler = Kokurikuler::where('pelajar_id', $pelajar->id)
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                ->first();

            $kehadiran = Kehadiran::where('pelajar_id', $pelajar->id)
                ->where('rombel_id', $this->rombel->id)
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                ->first();

            return [
                'id'                => $pelajar->id,
                'nis'               => $pelajar->nomor_induk ?? '-',
                'nisn'              => $pelajar->nisn ?? '-',
                'nama'              => $pelajar->nama_lengkap ?? 'N/A',
                'jenis_kelamin'     => $pelajar->jenis_kelamin ?? 'L',
                'nilai_per_mapel'   => $nilaiPerMapel,
                'kokurikuler'       => $kokurikuler->predikat ?? '-',
                'jumlah_nilai'      => $totalNilai,
                'rata_rata'         => $rataRata,
                'peringkat'         => 0,
                'sakit'             => $kehadiran->jumlah_sakit ?? 0,
                'izin'              => $kehadiran->jumlah_izin ?? 0,
                'tanpa_keterangan'  => $kehadiran->jumlah_tanpa_keterangan ?? 0,
            ];
        })->toArray();

        // 4. LOGIKA PERINGKAT UNIK (TIE-BREAKER BERLAPIS)
        usort($studentsData, function ($a, $b) use ($mapelOrderIds) {
            // Kriteria 1: Total Nilai (Terbesar ke Terkecil)
            if ($b['jumlah_nilai'] !== $a['jumlah_nilai']) {
                return $b['jumlah_nilai'] <=> $a['jumlah_nilai'];
            }

            // Kriteria 2: Tie-breaker Nilai per Mata Pelajaran (berdasarkan urutan kmp.urutan)
            foreach ($mapelOrderIds as $mapelId) {
                $nilaiA = $a['nilai_per_mapel'][$mapelId] ?? 0;
                $nilaiB = $b['nilai_per_mapel'][$mapelId] ?? 0;

                if ($nilaiB !== $nilaiA) {
                    return $nilaiB <=> $nilaiA;
                }
            }

            // Kriteria 3: Nama Siswa (A-Z) jika semua nilai identik
            return strcmp($a['nama'], $b['nama']);
        });

        // 5. PENETAPAN NOMOR PERINGKAT SEKUANSIAL
        $rankCounter = 1;
        foreach ($studentsData as &$student) {
            if ($student['jumlah_nilai'] === 0) {
                $student['peringkat'] = '-';
            } else {
                $student['peringkat'] = $rankCounter++;
            }
        }
        unset($student);

        // 6. FINALISASI: URUTKAN BERDASARKAN NAMA UNTUK TAMPILAN TABEL
        usort($studentsData, fn($a, $b) => strcmp($a['nama'], $b['nama']));

        // 7. PENOMORAN ULANG KOLOM 'NO'
        foreach ($studentsData as $key => &$val) {
            $val['no'] = $key + 1;
        }

        $this->studentsList = $studentsData;
    }

    // ========================================
    // PDF GENERATION
    // ========================================

    public function generatePdfUrl()
    {
        if (!$this->rombel || empty($this->studentsList)) {
            $this->pdfUrl = '';
            return;
        }

        // FIX: Gunakan $this->semesterAktif bukan $this->semesterId
        $semesterObj = $this->semesterAktif;

        // Data yang akan dikirim ke PDF
        $pdfData = [
            'sekolah' => [
                'nama_sekolah' => $this->dataSekolah->nama_sekolah ?? 'N/A',
                'kota_kabupaten' => $this->dataSekolah->kota_kabupaten ?? 'Kota',
            ],
            'tahun_ajaran' => $semesterObj->tahunAjaran->nama ?? 'N/A',
            'semester_nama' => $semesterObj->semester->nama ?? 'N/A',
            'kelas' => $this->rombel->nama ?? 'N/A',
            'wali_kelas' => [
                'nama' => $this->rombel->waliKelas->name ?? 'N/A',
                'nip' => $this->rombel->waliKelas->nip ?? '-'
            ],
            'kepala_sekolah' => [
                'nama' => $this->pengaturan->kepalaSekolah->name ?? 'N/A',
                'nip' => $this->pengaturan->kepalaSekolah->nip ?? '-'
            ],
            'tanggal_rapor' => $this->pengaturan?->tanggal_rapor ?? date('Y-m-d'),
            'mata_pelajaran' => $this->mataPelajaranList,
            'students' => $this->studentsList,
        ];

        // 1. Buat Key Unik (User ID + Rombel ID)
        $userId = Auth::id() ?? 'guest';
        // FIX: Gunakan $this->rombel->id bukan $this->rombelId
        $cacheKey = "leger_print_{$userId}_{$this->rombel->id}";

        // 2. Simpan Data Besar ke Cache (Durasi 1 Jam)
        Cache::put($cacheKey, $pdfData, 3600);

        // 3. Generate URL Pendek (Hanya kirim key)
        $this->pdfUrl = route('pdf.leger', ['key' => $cacheKey]);
    }

    // ========================================
    // RENDER METHOD
    // ========================================

    public function render()
    {
        return view('livewire.wali.preview-leger', [
            'totalStudents' => count($this->studentsList),
            'totalMataPelajaran' => count($this->mataPelajaranList),
            'hasData' => !empty($this->studentsList),
        ]);
    }
}
