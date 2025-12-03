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
use App\Models\EkskulPelajar;
use App\Models\RombelPelajar;
use App\Models\CatatanWaliKelas;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Auth; // Tambahkan Import Facade Auth
use Illuminate\Support\Facades\Cache;

class PreviewRapor extends Component
{
    // Filter Properties
    public $tahunAjaranId = null;
    public $semesterId = null;
    public $rombelId = null;
    public $dataSekolah = null;

    // Data Collections (Ringan)
    public $tahunAjaranList = [];
    public $semesterList = [];
    public $rombelList = [];

    // List Siswa (Hanya ID dan Nama agar ringan)
    public $studentsList = [];

    // Main Data
    public $rombel;

    // Navigation properties
    public $currentIndex = 0;

    // VARIABEL UTAMA (Sesuai dengan Blade View Anda)
    public $currentStudent = null;

    public $pdfUrl;

    // View selector
    public $selectedPage = 'cover';

    protected $queryString = [
        'tahunAjaranId' => ['except' => null],
        'semesterId' => ['except' => null],
        'rombelId' => ['except' => null],
        'selectedPage' => ['except' => 'cover'],
    ];

    public function mount()
    {
        $this->initializeFilters();
        $this->loadDataSekolah();
    }

    private function loadDataSekolah(): void
    {
        $this->dataSekolah = DataSekolah::first();
    }

    private function initializeFilters(): void
    {
        $this->loadTahunAjaran();

        if (!$this->tahunAjaranId) $this->setActiveTahunAjaran();

        if ($this->tahunAjaranId) {
            $this->loadSemester();
            if (!$this->semesterId) $this->setActiveSemester();
        }

        if ($this->tahunAjaranId && $this->semesterId) {
            $this->loadRombel();
        }

        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
            $this->loadStudentsListOnly();
        }
    }

    // --- Helper Filter Methods ---
    private function setActiveTahunAjaran(): void
    {
        $a = TahunAjaran::where('status', 'aktif')->first();
        if ($a) $this->tahunAjaranId = $a->id;
    }
    private function setActiveSemester(): void
    {
        $a = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahunAjaranId)->where('status', 'aktif')->first();
        if ($a) $this->semesterId = $a->id;
    }
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
        $this->semesterList = TahunAjaranSemester::with('semester')->where('tahun_ajaran_id', $this->tahunAjaranId)->get();
    }
    private function loadRombel(): void
    {
        if (!$this->tahunAjaranId) {
            $this->rombelList = [];
            return;
        }
        $this->rombelList = Rombel::whereHas('tahunAjaran', function ($q) {
            $q->where('tahun_ajaran_id', $this->tahunAjaranId);
        })->orderBy('tingkat', 'asc')->orderBy('nama', 'asc')->get();
    }
    private function loadRombelData(): void
    {
        if (!$this->rombelId) {
            $this->rombel = null;
            return;
        }
        $this->rombel = Rombel::with(['tahunAjaranKurikulum.tahunAjaran', 'tahunAjaranKurikulum.kurikulum', 'waliKelas', 'jurusan'])->find($this->rombelId);
        if (!$this->rombel) $this->rombelId = null;
    }

    // ========================================
    // LOGIKA UTAMA (OPTIMIZED)
    // ========================================

    // 1. Ambil List Siswa (Versi Ringan)
    private function loadStudentsListOnly(): void
    {
        if (!$this->rombelId || !$this->semesterId) {
            $this->studentsList = [];
            $this->currentStudent = null;
            $this->currentIndex = 0;
            return;
        }

        // Hanya ambil ID dan Nama. JANGAN load nilai di sini agar cepat.
        $this->studentsList = RombelPelajar::with('pelajar:id,nama_lengkap,nomor_induk')
            ->where('rombel_id', $this->rombelId)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->pelajar->id,
                    'nama' => $item->pelajar->nama_lengkap,
                    'nis' => $item->pelajar->nomor_induk
                ];
            })->toArray();

        // Otomatis load detail siswa pertama
        if (!empty($this->studentsList)) {
            if (!isset($this->studentsList[$this->currentIndex])) {
                $this->currentIndex = 0;
            }
            $this->loadCurrentStudent(); // Method Load Detail
        }
    }

    // 2. Ambil Detail Lengkap (Hanya untuk 1 Siswa yang dipilih)
    public function loadCurrentStudent()
    {
        if (!isset($this->studentsList[$this->currentIndex])) {
            $this->currentStudent = null;
            $this->pdfUrl = '';
            return;
        }

        $simpleStudent = $this->studentsList[$this->currentIndex];
        $pelajarId = $simpleStudent['id'];

        // Ambil Data Lengkap Pelajar (Ortu, dll)
        $pelajar = \App\Models\Pelajar::with(['orangTuaWalis' => function ($q) {
            $q->orderBy('hubungan', 'asc');
        }])->find($pelajarId);

        if (!$pelajar) return;

        $orangTuaWalis = $pelajar->orangTuaWalis ?? collect();
        $tingkat = $this->rombel->tingkat ?? 0;
        $fase = ((int)$tingkat === 10) ? 'E' : 'F';

        // Load Nilai & Data Pendukung (Hanya untuk 1 siswa ini)
        $nilaiData = $this->loadNilaiPelajar($pelajarId);
        $kokurikulerData = $this->loadKokurikuler($pelajarId);
        $ekskulData = $this->loadEkstrakurikuler($pelajarId);
        $kehadiranData = $this->loadKehadiran($pelajarId);
        $catatanData = $this->loadCatatanWali($pelajarId);

        // Susun Data Detail untuk View & PDF
        $this->currentStudent = [
            'id' => $pelajar->id,
            'nis' => $pelajar->nomor_induk,
            'nisn' => $pelajar->nisn,
            'nama' => $pelajar->nama_lengkap,
            'tempat_lahir' => $pelajar->tempat_lahir,
            'tanggal_lahir' => $pelajar->tanggal_lahir,
            'jenis_kelamin' => $pelajar->jenis_kelamin,
            'agama' => $pelajar->agama,
            'status_dalam_keluarga' => $pelajar->status_dalam_keluarga,
            'anak_ke' => $pelajar->anak_ke,
            'alamat' => $pelajar->alamat,
            'telepon' => $pelajar->telepon,
            'sekolah_asal' => $pelajar->sekolah_asal,
            'diterima_di_kelas' => $pelajar->diterima_di_kelas,
            'pada_tanggal' => $pelajar->pada_tanggal,
            'kelas' => $this->rombel->nama ?? '-',
            'fase' => $fase,
            'ayah' => $this->formatOrangTua($orangTuaWalis->firstWhere('hubungan', 'ayah')),
            'ibu' => $this->formatOrangTua($orangTuaWalis->firstWhere('hubungan', 'ibu')),
            'wali' => $this->formatOrangTua($orangTuaWalis->firstWhere('hubungan', 'wali')),
            'nilai' => $nilaiData['nilai'],
            'nilai_grouped' => $nilaiData['nilai_grouped'],
            'kokurikuler' => $kokurikulerData,
            'ekstrakurikuler' => $ekskulData,
            'ketidakhadiran' => $kehadiranData,
            'catatan_wali' => $catatanData,
            'tanggapan_ortu' => '',
        ];

        // Generate Link PDF
        $this->generatePdfUrl();
    }

    // 3. Generate URL PDF menggunakan CACHE
    public function generatePdfUrl()
    {
        if (!$this->currentStudent) {
            $this->pdfUrl = '';
            return;
        }

        $selectedTahunAjaran = TahunAjaran::find($this->tahunAjaranId);
        $selectedSemester = TahunAjaranSemester::find($this->semesterId);
        $pengaturan = Pengaturan::with('kepalaSekolah')->where('tahun_ajaran_semester_id', $this->semesterId)->first();

        // Susun Data untuk PDF (Array Lengkap)
        $pdfData = array_merge($this->currentStudent, [
            'sekolah' => [
                'nama_sekolah' => $this->dataSekolah->nama_sekolah ?? 'N/A',
                'npsn' => $this->dataSekolah->npsn ?? 'N/A',
                'nss' => $this->dataSekolah->nss ?? '',
                'alamat' => $this->dataSekolah->alamat ?? 'N/A',
                'kode_pos' => $this->dataSekolah->kode_pos ?? 'N/A',
                'kelurahan' => $this->dataSekolah->kelurahan ?? 'N/A',
                'kecamatan' => $this->dataSekolah->kecamatan ?? 'N/A',
                'kota_kabupaten' => $this->dataSekolah->kota_kabupaten ?? 'N/A',
                'provinsi' => $this->dataSekolah->provinsi ?? 'N/A',
                'telepon' => $this->dataSekolah->telepon ?? 'N/A',
                'website' => $this->dataSekolah->website ?? 'N/A',
                'email' => $this->dataSekolah->email ?? 'N/A',
            ],
            'semester_nama' => $selectedSemester->semester->nama ?? 'N/A',
            'semester_urutan' => $selectedSemester->semester->urutan ?? 'N/A',
            'tahun_ajaran' => $selectedTahunAjaran->nama ?? 'N/A',
            'wali_kelas' => ['nama' => $this->rombel->waliKelas->name ?? 'N/A', 'nip' => $this->rombel->waliKelas->nip ?? '~'],
            'kepala_sekolah' => ['nama' => $pengaturan->kepalaSekolah->name ?? 'N/A', 'nip' => $pengaturan->kepalaSekolah->nip ?? 'N/A'],
            'tanggal_rapor' => $pengaturan->tanggal_rapor ?? null,
        ]);

        // --- SOLUSI INTI: SIMPAN KE CACHE ---
        // Refactor: Menggunakan Facade Auth::id() agar lebih aman dari error helper
        $userId = Auth::id() ?? 'guest';
        $studentId = $this->currentStudent['id'] ?? 'unknown';

        $cacheKey = "rapor_print_{$userId}_{$studentId}";

        Cache::put($cacheKey, $pdfData, 600); // 10 menit

        $this->pdfUrl = route('pdf.generate') . '?key=' . $cacheKey . '&view=' . $this->selectedPage;
    }

    // --- Query Methods ---
    private function loadNilaiPelajar($pelajarId): array
    {
        if (!$this->rombelId || !$this->rombel->tahunAjaranKurikulum) return ['nilai' => [], 'nilai_grouped' => []];

        $kurikulumId = $this->rombel->tahunAjaranKurikulum->kurikulum_id;
        $tingkatRombel = $this->rombel->tingkat;

        $pelajar = \App\Models\Pelajar::select('id', 'agama_hash')->find($pelajarId);
        $agamaPelajarHash = $pelajar ? $pelajar->agama_hash : null;

        $dataMapel = \App\Models\RombelPengajar::query()
            ->join('mata_pelajarans as mp', 'rombel_pengajars.mata_pelajaran_id', '=', 'mp.id')
            ->join('kurikulum_mata_pelajarans as kmp', function ($join) use ($kurikulumId, $tingkatRombel) {
                $join->on('mp.id', '=', 'kmp.mata_pelajaran_id')
                    ->where('kmp.kurikulum_id', '=', $kurikulumId)
                    ->where('kmp.tingkat', '=', $tingkatRombel);
            })
            ->join('mata_pelajaran_kelompoks as mpk', 'kmp.kelompok_id', '=', 'mpk.id')
            ->leftJoin('nilais', function ($join) use ($pelajarId) {
                $join->on('mp.id', '=', 'nilais.mata_pelajaran_id')
                    ->where('nilais.pelajar_id', '=', $pelajarId)
                    ->where('nilais.tahun_ajaran_semester_id', '=', $this->semesterId);
            })
            ->where('rombel_pengajars.rombel_id', $this->rombelId)
            ->where(function ($query) use ($agamaPelajarHash) {
                $query->where('mp.is_mapel_agama', false)
                    ->orWhere(function ($q) use ($agamaPelajarHash) {
                        $q->where('mp.is_mapel_agama', true);
                        if ($agamaPelajarHash) $q->where('mp.agama_terkait_hash', $agamaPelajarHash);
                        else $q->whereNull('mp.id');
                    });
            })
            ->select('mp.id', 'mp.nama as mapel_nama', 'mpk.nama as kelompok_nama', 'mpk.kode as kelompok_kode', 'kmp.urutan', 'nilais.nilai_angka', 'nilais.predikat', 'nilais.capaian_kompetensi')
            ->orderBy('kmp.urutan', 'asc')
            ->get();

        $nilaiArray = [];
        $nilaiGrouped = [];
        $counter = 1;

        foreach ($dataMapel as $row) {
            $nilaiAngka = $row->nilai_angka ? round($row->nilai_angka) : 0;
            $item = [
                'no' => $counter++,
                'mapel' => $row->mapel_nama,
                'kelompok' => $row->kelompok_nama,
                'kelompok_kode' => $row->kelompok_kode,
                'nilai' => $nilaiAngka,
                'predikat' => $row->predikat ?? '-',
                'capaian' => $row->capaian_kompetensi ?? '',
            ];
            $nilaiArray[] = $item;
            $kelompokNama = $row->kelompok_nama;
            if (!isset($nilaiGrouped[$kelompokNama])) {
                $nilaiGrouped[$kelompokNama] = ['kode' => $row->kelompok_kode, 'items' => []];
            }
            $nilaiGrouped[$kelompokNama]['items'][] = $item;
        }

        return ['nilai' => $nilaiArray, 'nilai_grouped' => $nilaiGrouped];
    }

    private function loadKokurikuler($pelajarId): string
    {
        $k = Kokurikuler::where('pelajar_id', $pelajarId)->where('tahun_ajaran_semester_id', $this->semesterId)->first();
        return $k ? ($k->capaian ?? '') : '';
    }
    private function loadEkstrakurikuler($pelajarId): array
    {
        $e = EkskulPelajar::with('ekstrakurikuler')->where('pelajar_id', $pelajarId)->where('tahun_ajaran_semester_id', $this->semesterId)->get();
        return $e->map(function ($x) {
            return ['nama' => $x->ekstrakurikuler->nama ?? 'N/A', 'keterangan' => $x->deskripsi ?? 'Tidak ada keterangan.'];
        })->toArray();
    }
    private function loadKehadiran($pelajarId): array
    {
        $k = Kehadiran::where('pelajar_id', $pelajarId)->where('rombel_id', $this->rombelId)->where('tahun_ajaran_semester_id', $this->semesterId)->first();
        return ['sakit' => $k->jumlah_sakit ?? 0, 'izin' => $k->jumlah_izin ?? 0, 'tanpa_keterangan' => $k->jumlah_tanpa_keterangan ?? 0];
    }
    private function loadCatatanWali($pelajarId): string
    {
        $c = CatatanWaliKelas::where('pelajar_id', $pelajarId)->where('tahun_ajaran_semester_id', $this->semesterId)->first();
        return $c ? ($c->catatan ?? '') : '';
    }

    private function formatOrangTua($orangTua): array
    {
        if (!$orangTua) return ['nama' => '-', 'pekerjaan' => '-', 'telepon' => '-', 'alamat' => '-', 'status' => '-'];
        $kodePekerjaan = $orangTua->pekerjaan ?? null;
        $labelPekerjaan = config("enums.pekerjaan.$kodePekerjaan") ?? $kodePekerjaan ?? '-';
        return ['nama' => $orangTua->nama ?? '-', 'pekerjaan' => $labelPekerjaan, 'telepon' => $orangTua->telepon ?? '-', 'alamat' => $orangTua->alamat ?? '-', 'status' => $orangTua->status ?? 'Masih Hidup'];
    }

    // --- Update Handlers ---
    public function updatedTahunAjaranId(): void
    {
        $this->resetFilters();
        $this->loadSemester();
        $this->setActiveSemester();
        if ($this->semesterId) $this->updatedSemesterId();
    }
    public function updatedSemesterId(): void
    {
        $this->rombelId = null;
        $this->rombel = null;
        $this->rombelList = [];
        $this->studentsList = [];
        $this->currentStudent = null;
        $this->currentIndex = 0;
        $this->loadRombel();
    }

    public function updatedRombelId(): void
    {
        $this->studentsList = [];
        $this->currentStudent = null;
        $this->currentIndex = 0;
        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
            $this->loadStudentsListOnly();
        } else {
            $this->rombel = null;
        }
    }

    public function updatedSelectedPage(): void
    {
        $this->generatePdfUrl();
    }

    // --- Navigation (Updated) ---
    public function nextStudent()
    {
        if ($this->currentIndex < count($this->studentsList) - 1) {
            $this->currentIndex++;
            $this->loadCurrentStudent();
        }
    }

    public function previousStudent()
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
            $this->loadCurrentStudent();
        }
    }

    public function selectStudent($index)
    {
        $this->currentIndex = (int)$index;
        $this->loadCurrentStudent();
    }

    private function resetFilters(): void
    {
        $this->semesterId = null;
        $this->rombelId = null;
        $this->semesterList = [];
        $this->rombelList = [];
        $this->studentsList = [];
        $this->rombel = null;
        $this->currentStudent = null;
        $this->currentIndex = 0;
    }

    public function render()
    {
        return view('livewire.admin.preview-rapor', [
            'totalStudents' => count($this->studentsList),
        ]);
    }
}
