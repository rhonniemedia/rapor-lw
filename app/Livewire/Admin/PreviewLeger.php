<?php

namespace App\Livewire\Admin;

use App\Models\Nilai;
use App\Models\Rombel;
use Livewire\Component;
use App\Models\Kehadiran;
use App\Models\Pengaturan;
use App\Models\DataSekolah;
use App\Models\Kokurikuler;
use App\Models\TahunAjaran;
use App\Models\RombelPelajar;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\KurikulumMataPelajaran;

class PreviewLeger extends Component
{
    // Filter Properties (Adopsi dari PreviewRapor)
    public $tahunAjaranId = null;
    public $semesterId = null;
    public $rombelId = null;

    // Data Collections
    public $tahunAjaranList = [];
    public $semesterList = [];
    public $rombelList = [];

    // Data Utama Leger (Adopsi dari LegerKelas)
    public $rombel;
    public $dataSekolah = null;
    public $pengaturan = null;

    public $studentsList = [];
    public $mataPelajaranList = [];
    public $pdfUrl = '';

    protected $queryString = [
        'tahunAjaranId' => ['except' => null],
        'semesterId' => ['except' => null],
        'rombelId' => ['except' => null],
    ];

    public function mount()
    {
        $this->initializeFilters();
        $this->loadDataSekolah();
    }

    // ========================================
    // FILTER & INIT (Adopsi PreviewRapor)
    // ========================================

    private function loadDataSekolah(): void
    {
        $this->dataSekolah = DataSekolah::first();
    }

    private function initializeFilters(): void
    {
        $this->loadTahunAjaran();

        if (!$this->tahunAjaranId) {
            $this->setActiveTahunAjaran();
        }

        if ($this->tahunAjaranId) {
            $this->loadSemester();
            if (!$this->semesterId) {
                $this->setActiveSemester();
            }
        }

        if ($this->tahunAjaranId && $this->semesterId) {
            $this->loadRombel();
        }

        if ($this->rombelId && $this->semesterId) {
            $this->loadFullLegerData();
        }
    }

    private function setActiveTahunAjaran(): void
    {
        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        if ($activeTahunAjaran) {
            $this->tahunAjaranId = $activeTahunAjaran->id;
        }
    }

    private function setActiveSemester(): void
    {
        $activeSemester = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahunAjaranId)
            ->where('status', 'aktif')
            ->first();
        if ($activeSemester) {
            $this->semesterId = $activeSemester->id;
        }
    }

    // ========================================
    // LOADING OPTIONS
    // ========================================

    private function loadTahunAjaran(): void
    {
        $this->tahunAjaranList = TahunAjaran::orderBy('tgl_mulai', 'desc')->get();
    }

    private function loadSemester(): void
    {
        if (!$this->tahunAjaranId) {
            $this->semesterList = [];
            return;
        }
        $this->semesterList = TahunAjaranSemester::with('semester')
            ->where('tahun_ajaran_id', $this->tahunAjaranId)
            ->get();
    }

    private function loadRombel(): void
    {
        if (!$this->tahunAjaranId) {
            $this->rombelList = [];
            return;
        }
        $this->rombelList = Rombel::whereHas('tahunAjaran', function ($q) {
            $q->where('tahun_ajaran_id', $this->tahunAjaranId);
        })
            ->orderBy('tingkat', 'asc')
            ->orderBy('nama', 'asc')
            ->get();
    }

    // ========================================
    // LOGIKA UTAMA LEGER (Gabungan & Modifikasi)
    // ========================================

    public function loadFullLegerData()
    {
        // 1. Load Info Rombel
        $this->rombel = Rombel::with([
            'tahunAjaranKurikulum.tahunAjaran',
            'tahunAjaranKurikulum.kurikulum',
            'waliKelas',
            'jurusan'
        ])->find($this->rombelId);

        if (!$this->rombel) {
            $this->rombelId = null;
            return;
        }

        // 2. Load Pengaturan (Kepala Sekolah dll)
        $this->pengaturan = Pengaturan::with('kepalaSekolah')
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->first();

        // 3. Load Mapel & Siswa (Logika LegerKelas)
        $this->loadMataPelajaranList();
        $this->loadStudentsWithNilai();

        // 4. Generate PDF Link
        $this->generatePdfUrl();
    }

    private function loadMataPelajaranList(): void
    {
        // 1. Validasi
        $kurikulumId = $this->rombel->tahunAjaranKurikulum->kurikulum_id ?? null;
        $tingkatRombel = $this->rombel->tingkat ?? null; // ← TAMBAHKAN INI

        if (!$this->rombelId || !$kurikulumId || !$tingkatRombel) {
            $this->mataPelajaranList = [];
            return;
        }

        // 2. QUERY UTAMA: Ambil Mapel dari RombelPengajar dengan FILTER TINGKAT
        $allMapel = \App\Models\RombelPengajar::query()
            ->where('rombel_pengajars.rombel_id', $this->rombelId)
            // Join ke Mata Pelajaran
            ->join('mata_pelajarans as mp', 'rombel_pengajars.mata_pelajaran_id', '=', 'mp.id')
            // Join ke Kurikulum Mapel dengan FILTER TINGKAT - INI KUNCI SOLUSINYA!
            ->join('kurikulum_mata_pelajarans as kmp', function ($join) use ($kurikulumId, $tingkatRombel) {
                $join->on('mp.id', '=', 'kmp.mata_pelajaran_id')
                    ->where('kmp.kurikulum_id', '=', $kurikulumId)
                    ->where('kmp.tingkat', '=', $tingkatRombel); // ← FILTER TINGKAT
            })
            // Join ke Kelompok (untuk kode kelompok A/B/C)
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

        // Jika ada mapel agama (Islam, Kristen, dll), ambil satu saja sebagai perwakilan kolom
        if ($agamaMapels->isNotEmpty()) {
            // Kita ambil elemen pertama sebagai representasi kolom "Agama"
            $combined->push($agamaMapels->first());
        }

        // Gabungkan dengan mapel umum
        $combined = $combined->merge($nonAgamaMapels);

        // 4. MAPPING FINAL
        $mataPelajarans = $combined
            ->sortBy(fn($item) => [$item->kelompok_kode, $item->urutan])
            ->map(function ($item) {
                $isAgama = $item->is_mapel_agama == true || $item->is_mapel_agama == 1;
                return [
                    // Jika agama, ID diset string 'agama' untuk trigger logika pencarian nilai spesifik di loop siswa
                    'id'             => $isAgama ? 'agama' : $item->id,
                    'nama'           => $isAgama ? 'Pendidikan Agama dan Budi Pekerti' : $item->nama,
                    'kode'           => $isAgama ? 'PABP' : $item->kode,
                    'kelompok_kode'  => $item->kelompok_kode,
                    'is_agama'       => $isAgama,
                ];
            });

        $this->mataPelajaranList = $mataPelajarans->values()->toArray();
    }

    private function loadStudentsWithNilai(): void
    {
        $rombelPelajars = RombelPelajar::with('pelajar')
            ->where('rombel_id', $this->rombelId)
            ->orderBy('id', 'asc') // Bisa diganti order by nama jika perlu
            ->get();

        $studentsData = $rombelPelajars->map(function ($rombelPelajar, $index) {
            $pelajar = $rombelPelajar->pelajar;

            $nilaiPerMapel = [];
            $totalNilai = 0;
            $jumlahMapelDiisi = 0;

            foreach ($this->mataPelajaranList as $mapel) {
                if ($mapel['is_agama'] === true) {
                    // Cari nilai agama spesifik siswa tersebut (misal: jika siswa Islam, cari mapel Agama Islam)
                    $nilai = Nilai::where('pelajar_id', $pelajar->id)
                        ->where('tahun_ajaran_semester_id', $this->semesterId)
                        ->whereHas('mataPelajaran', fn($q) => $q->where('is_mapel_agama', true))
                        ->first();
                } else {
                    $nilai = Nilai::where('pelajar_id', $pelajar->id)
                        ->where('mata_pelajaran_id', $mapel['id'])
                        ->where('tahun_ajaran_semester_id', $this->semesterId)
                        ->first();
                }

                $nilaiAngka = $nilai ? round($nilai->nilai_angka ?? 0) : 0;
                $nilaiPerMapel[$mapel['id']] = $nilaiAngka;

                if ($nilaiAngka > 0) {
                    $totalNilai += $nilaiAngka;
                    $jumlahMapelDiisi++;
                }
            }

            $rataRata = $jumlahMapelDiisi > 0 ? round($totalNilai / $jumlahMapelDiisi, 1) : 0;

            $kokurikuler = Kokurikuler::where('pelajar_id', $pelajar->id)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->first();

            $kehadiran = Kehadiran::where('pelajar_id', $pelajar->id)
                ->where('rombel_id', $this->rombelId)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->first();

            return [
                'no' => $index + 1,
                'nis' => $pelajar->nomor_induk ?? '-',
                'nisn' => $pelajar->nisn ?? '-',
                'nama' => $pelajar->nama_lengkap ?? 'N/A',
                'jenis_kelamin' => $pelajar->jenis_kelamin ?? 'L',
                'nilai_per_mapel' => $nilaiPerMapel,
                'kokurikuler' => $kokurikuler->predikat ?? '-',
                'jumlah_nilai' => $totalNilai,
                'rata_rata' => $rataRata,
                'peringkat' => 0,
                'sakit' => $kehadiran->jumlah_sakit ?? 0,
                'izin' => $kehadiran->jumlah_izin ?? 0,
                'tanpa_keterangan' => $kehadiran->jumlah_tanpa_keterangan ?? 0,
            ];
        })->toArray();

        // --- LOGIKA PERINGKAT ---
        usort($studentsData, fn($a, $b) => $b['jumlah_nilai'] <=> $a['jumlah_nilai']);

        $currentRank = 1;
        $previousNilai = null;
        $sameRankCount = 0;

        foreach ($studentsData as &$student) {
            if ($student['jumlah_nilai'] === 0) {
                $student['peringkat'] = '-';
            } else {
                if ($previousNilai !== null && $student['jumlah_nilai'] === $previousNilai) {
                    $sameRankCount++;
                } else {
                    $currentRank += $sameRankCount;
                    $sameRankCount = 1;
                }
                $student['peringkat'] = $currentRank;
                $previousNilai = $student['jumlah_nilai'];
            }
        }
        unset($student);

        // Sort ulang berdasarkan No Urut awal (opsional, biasanya leger urut nama/absen)
        // Disini kita urutkan berdasarkan nama agar rapi
        usort($studentsData, fn($a, $b) => strcmp($a['nama'], $b['nama']));

        // Re-numbering NO column after name sort
        foreach ($studentsData as $key => &$val) {
            $val['no'] = $key + 1;
        }

        $this->studentsList = $studentsData;
    }

    // ========================================
    // UPDATERS
    // ========================================

    public function updatedTahunAjaranId(): void
    {
        $this->resetData();
        $this->loadSemester();
        $this->setActiveSemester();
        if ($this->semesterId) $this->updatedSemesterId();
    }

    public function updatedSemesterId(): void
    {
        $this->rombelId = null;
        $this->rombelList = [];
        $this->resetData();
        $this->loadRombel();
    }

    public function updatedRombelId(): void
    {
        $this->resetData();
        if ($this->rombelId && $this->semesterId) {
            $this->loadFullLegerData();
        }
    }

    private function resetData(): void
    {
        $this->rombel = null;
        $this->studentsList = [];
        $this->mataPelajaranList = [];
        $this->pdfUrl = '';
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

        // Mengambil nama semester & tahun ajaran untuk header
        $semesterObj = TahunAjaranSemester::with('semester', 'tahunAjaran')->find($this->semesterId);

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

        // --- PERUBAHAN UTAMA DI SINI ---

        // 1. Buat Key Unik (User ID + Rombel ID)
        $userId = Auth::id() ?? 'guest';
        $cacheKey = "leger_print_{$userId}_{$this->rombelId}";

        // 2. Simpan Data Besar ke Cache (Durasi 1 Jam)
        Cache::put($cacheKey, $pdfData, 3600);

        // 3. Generate URL Pendek (Hanya kirim key)
        // Action default kosong (akan jadi stream/view)
        $this->pdfUrl = route('pdf.leger', ['key' => $cacheKey]);
    }

    public function render()
    {
        $semesterAktif = null;

        if ($this->semesterId) {
            $semesterAktif = TahunAjaranSemester::with('semester', 'tahunAjaran')
                ->find($this->semesterId);
        }

        return view('livewire.admin.preview-leger', [
            'hasData' => !empty($this->studentsList),
            'semesterAktif' => $semesterAktif
        ]);
    }
}
