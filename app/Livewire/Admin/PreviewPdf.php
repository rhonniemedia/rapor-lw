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

class PreviewPdf extends Component
{
    // Filter Properties
    public $tahunAjaranId = null;
    public $semesterId = null;
    public $rombelId = null;
    public $dataSekolah = null;

    // Data Collections
    public $tahunAjaranList = [];
    public $semesterList = [];
    public $rombelList = [];
    public $studentsList = [];

    // Main Data
    public $rombel;

    // Navigation properties
    public $currentIndex = 0;
    public $currentStudent = null;
    public $pdfUrl;

    // View selector
    public $selectedPage = 'cover'; // cover = biodata, content = nilai

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

    // ========================================
    // INITIALIZATION METHODS
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
            $this->loadRombelData();
            $this->loadStudents();
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
    // DATA LOADING METHODS
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

    private function loadRombelData(): void
    {
        if (!$this->rombelId) {
            $this->rombel = null;
            return;
        }

        $this->rombel = Rombel::with([
            'tahunAjaranKurikulum.tahunAjaran',
            'tahunAjaranKurikulum.kurikulum',
            'waliKelas',
            'jurusan'
        ])->find($this->rombelId);

        if (!$this->rombel) {
            $this->rombelId = null;
        }
    }

    private function loadStudents(): void
    {
        if (!$this->rombelId || !$this->semesterId) {
            $this->studentsList = [];
            $this->currentStudent = null;
            $this->currentIndex = 0;
            return;
        }

        // Load students dari rombel dengan relasi pelajar dan orang tua wali
        $rombelPelajars = RombelPelajar::with([
            'pelajar.orangTuaWalis' => function ($q) {
                $q->orderBy('hubungan', 'asc'); // Ayah, Ibu, Wali
            }
        ])
            ->where('rombel_id', $this->rombelId)
            ->orderBy('id', 'asc')
            ->get();

        $this->studentsList = $rombelPelajars->map(function ($rombelPelajar) {
            $pelajar = $rombelPelajar->pelajar;
            $orangTuaWalis = $pelajar->orangTuaWalis ?? collect();

            // >>> START: LOGIKA PENENTUAN FASE DENGAN TERNARY OPERATOR
            $tingkat = $this->rombel->tingkat ?? 0;

            // Jika tingkat adalah 10, maka E. Selain itu (11, 12, atau lainnya), maka F.
            $fase = ($tingkat === 10) ? 'E' : 'F';

            // Load data nilai untuk siswa ini
            $nilaiData = $this->loadNilaiPelajar($pelajar->id);
            $kokurikulerData = $this->loadKokurikuler($pelajar->id);
            $ekskulData = $this->loadEkstrakurikuler($pelajar->id);
            $kehadiranData = $this->loadKehadiran($pelajar->id);
            $catatanData = $this->loadCatatanWali($pelajar->id);

            return [
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
                'kelas' => $this->rombel->nama,
                'fase' => $fase,
                // Orang Tua / Wali
                'ayah' => $orangTuaWalis->firstWhere('hubungan', 'ayah'),
                'ibu' => $orangTuaWalis->firstWhere('hubungan', 'ibu'),
                'wali' => $orangTuaWalis->firstWhere('hubungan', 'wali'),
                // Data Nilai & Kegiatan
                'nilai' => $nilaiData['nilai'],
                'nilai_grouped' => $nilaiData['nilai_grouped'],
                'kokurikuler' => $kokurikulerData,
                'ekstrakurikuler' => $ekskulData,
                'ketidakhadiran' => $kehadiranData,
                'catatan_wali' => $catatanData,
                'tanggapan_ortu' => '', // Kosong untuk diisi manual
            ];
        })->toArray();

        // Set first student
        if (!empty($this->studentsList)) {
            $this->currentIndex = 0;
            $this->loadCurrentStudent();
        }
    }

    private function loadNilaiPelajar($pelajarId): array
    {
        // Get kurikulum_id dari rombel
        $kurikulumId = $this->rombel->tahunAjaranKurikulum->kurikulum_id ?? null;

        if (!$kurikulumId) {
            return [
                'nilai' => [],
                'nilai_grouped' => []
            ];
        }

        // Query dengan JOIN langsung untuk dapat kelompok, urutan, NAMA, dan KODE
        $nilais = Nilai::with(['mataPelajaran'])
            ->join('kurikulum_mata_pelajarans as kmp', function ($join) use ($kurikulumId) {
                $join->on('nilais.mata_pelajaran_id', '=', 'kmp.mata_pelajaran_id')
                    ->where('kmp.kurikulum_id', '=', $kurikulumId);
            })
            // Pastikan Anda memilih KODE di klausa select
            ->join('mata_pelajaran_kelompoks as mpk', 'kmp.kelompok_id', '=', 'mpk.id')
            ->where('nilais.pelajar_id', $pelajarId)
            ->where('nilais.tahun_ajaran_semester_id', $this->semesterId)
            ->select('nilais.*', 'mpk.nama as kelompok_nama', 'mpk.kode as kelompok_kode', 'kmp.urutan') // <-- TAMBAHKAN mpk.kode
            ->orderBy('kmp.urutan', 'asc')
            ->get();

        $nilaiArray = [];
        $nilaiGrouped = [];
        $counter = 1;

        foreach ($nilais as $nilai) {
            $kelompokNama = $nilai->kelompok_nama ?? 'Lainnya';
            $kelompokKode = $nilai->kelompok_kode ?? 'Z'; // <-- AMBIL KODE DARI HASIL QUERY

            $item = [
                'no' => $counter++,
                'mapel' => $nilai->mataPelajaran->nama ?? 'N/A',
                'kelompok' => $kelompokNama,
                'kelompok_kode' => $kelompokKode, // <-- TAMBAHKAN KODE KE ITEM
                'nilai' => round($nilai->nilai_angka ?? 0),
                'predikat' => $nilai->predikat ?? '-',
                'capaian' => $nilai->capaian_kompetensi ?? '',
            ];

            $nilaiArray[] = $item;

            // Grouping
            if (!isset($nilaiGrouped[$kelompokNama])) {
                $nilaiGrouped[$kelompokNama] = [
                    'kode' => $kelompokKode, // <-- TAMBAHKAN KODE DI SINI UNTUK GROUPING
                    'items' => []
                ];
            }
            $nilaiGrouped[$kelompokNama]['items'][] = $item; // <-- Simpan item di bawah sub-array 'items'
        }

        // Perhatikan struktur baru nilai_grouped: [KelompokNama => ['kode' => 'A', 'items' => [...] ]]

        return [
            'nilai' => $nilaiArray,
            'nilai_grouped' => $nilaiGrouped
        ];
    }

    private function loadKokurikuler($pelajarId): string
    {
        $kokurikuler = Kokurikuler::where('pelajar_id', $pelajarId)
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->first();

        return $kokurikuler ? ($kokurikuler->capaian ?? '') : '';
    }

    private function loadEkstrakurikuler($pelajarId): array
    {
        $ekskuls = EkskulPelajar::with('ekstrakurikuler')
            ->where('pelajar_id', $pelajarId)
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->get();

        return $ekskuls->map(function ($ekskul) {

            // CUKUP AMBIL DESKRIPSI DARI KOLOM 'deskripsi'
            $deskripsiEkskul = $ekskul->deskripsi ?? 'Tidak ada keterangan.';

            return [
                'nama' => $ekskul->ekstrakurikuler->nama ?? 'N/A',
                // Gunakan hanya deskripsi sebagai keterangan
                'keterangan' => $deskripsiEkskul
            ];
        })->toArray();
    }

    private function loadKehadiran($pelajarId): array
    {
        $kehadiran = Kehadiran::where('pelajar_id', $pelajarId)
            ->where('rombel_id', $this->rombelId)
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->first();

        return [
            'sakit' => $kehadiran->jumlah_sakit ?? 0,
            'izin' => $kehadiran->jumlah_izin ?? 0,
            'tanpa_keterangan' => $kehadiran->jumlah_tanpa_keterangan ?? 0,
        ];
    }

    private function loadCatatanWali($pelajarId): string
    {
        $catatan = CatatanWaliKelas::where('pelajar_id', $pelajarId)
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->first();

        return $catatan ? ($catatan->catatan ?? '') : '';
    }

    // ========================================
    // FILTER UPDATE HANDLERS
    // ========================================

    public function updatedTahunAjaranId(): void
    {
        $this->resetFilters();
        $this->loadSemester();
        $this->setActiveSemester();

        if ($this->semesterId) {
            $this->updatedSemesterId();
        }
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
            $this->loadStudents();
        } else {
            $this->rombel = null;
        }
    }

    public function updatedSelectedPage(): void
    {
        $this->generatePdfUrl();
    }

    // ========================================
    // STUDENT NAVIGATION
    // ========================================

    public function loadCurrentStudent()
    {
        if (isset($this->studentsList[$this->currentIndex])) {
            $this->currentStudent = $this->studentsList[$this->currentIndex];
            $this->generatePdfUrl();
        } else {
            $this->currentStudent = null;
            $this->pdfUrl = '';
        }
    }

    public function nextStudent()
    {
        if ($this->currentIndex < count($this->studentsList) - 1) {
            $this->currentIndex++;
            $this->loadCurrentStudent();

            $this->dispatch(
                'student-changed',
                direction: 'next',
                student: $this->currentStudent['nama']
            );
        }
    }

    public function previousStudent()
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
            $this->loadCurrentStudent();

            $this->dispatch(
                'student-changed',
                direction: 'previous',
                student: $this->currentStudent['nama']
            );
        }
    }

    public function selectStudent($index)
    {
        $this->currentIndex = (int)$index;
        $this->loadCurrentStudent();
    }

    // ========================================
    // PDF GENERATION
    // ========================================

    public function generatePdfUrl()
    {
        if (!$this->currentStudent) {
            $this->pdfUrl = '';
            return;
        }

        // Get selected tahun ajaran and semester
        $selectedTahunAjaran = TahunAjaran::find($this->tahunAjaranId);
        $selectedSemester = TahunAjaranSemester::find($this->semesterId);

        // Get kepala sekolah dari pengaturans
        $pengaturan = Pengaturan::with('kepalaSekolah')
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->first();

        // Prepare biodata untuk PDF
        $pdfData = [
            'nama' => $this->currentStudent['nama'],
            'nis' => $this->currentStudent['nis'],
            'nisn' => $this->currentStudent['nisn'],
            'kelas' => $this->currentStudent['kelas'],
            'fase' => $this->currentStudent['fase'],
            'tempat_lahir' => $this->currentStudent['tempat_lahir'],
            'tanggal_lahir' => $this->currentStudent['tanggal_lahir'],
            'jenis_kelamin' => $this->currentStudent['jenis_kelamin'],
            'agama' => $this->currentStudent['agama'],
            'status_dalam_keluarga' => $this->currentStudent['status_dalam_keluarga'],
            'anak_ke' => $this->currentStudent['anak_ke'],
            'alamat' => $this->currentStudent['alamat'],
            'telepon' => $this->currentStudent['telepon'],
            'sekolah_asal' => $this->currentStudent['sekolah_asal'],
            'diterima_di_kelas' => $this->currentStudent['diterima_di_kelas'],
            'pada_tanggal' => $this->currentStudent['pada_tanggal'],
            // Sekolah info
            'sekolah' => [
                'nama_sekolah' => $this->dataSekolah->nama_sekolah ?? 'N/A',
                'npsn' => $this->dataSekolah->npsn ?? 'N/A',
                'nis' => $this->dataSekolah->nis ?? 'N/A',
                'nss' => $this->dataSekolah->nss ?? 'N/A',
                'nds' => $this->dataSekolah->nds ?? 'N/A',
                'alamat' => $this->dataSekolah->alamat ?? 'N/A',
                'kode_pos' => $this->dataSekolah->kode_pos ?? 'N/A',
                'kelurahan' => $this->dataSekolah->kelurahan ?? 'N/A',
                'kecamatan' => $this->dataSekolah->kecamatan ?? 'N/A',
                'kota_kabupaten' => $this->dataSekolah->kota_kabupaten ?? 'N/A',
                'provinsi' => $this->dataSekolah->provinsi ?? 'N/A',
                'telepon' => $this->dataSekolah->telepon ?? 'N/A',
                'website' => $this->dataSekolah->website ?? 'N/A',
                'email' => $this->dataSekolah->email ?? 'N/A',
                'logo_sekolah_path' => $this->dataSekolah->logo_sekolah_path ?? null,
                'logo_pemda_path' => $this->dataSekolah->logo_pemda_path ?? null,
            ],
            'semester_nama' => $selectedSemester->semester->nama ?? 'N/A',
            'semester_urutan' => $selectedSemester->semester->urutan ?? 'N/A',
            'tahun_ajaran' => $selectedTahunAjaran->nama ?? 'N/A',
            // Orang Tua / Wali
            'ayah' => $this->formatOrangTua($this->currentStudent['ayah']),
            'ibu' => $this->formatOrangTua($this->currentStudent['ibu']),
            'wali' => $this->formatOrangTua($this->currentStudent['wali']),
            // Untuk halaman nilai
            'nilai' => $this->currentStudent['nilai'],
            'nilai_grouped' => $this->currentStudent['nilai_grouped'],
            'kokurikuler' => $this->currentStudent['kokurikuler'],
            'ekstrakurikuler' => $this->currentStudent['ekstrakurikuler'],
            'ketidakhadiran' => $this->currentStudent['ketidakhadiran'],
            'catatan_wali' => $this->currentStudent['catatan_wali'],
            'tanggapan_ortu' => $this->currentStudent['tanggapan_ortu'],
            // Wali Kelas & Kepala Sekolah
            'wali_kelas' => [
                'nama' => $this->rombel->waliKelas->name ?? 'N/A',
                'nip' => $this->rombel->waliKelas->nip ?? 'N/A'
            ],
            'kepala_sekolah' => [
                'nama' => $pengaturan->kepalaSekolah->name ?? 'N/A',
                'nip' => $pengaturan->kepalaSekolah->nip ?? 'N/A'
            ],
            'tanggal_rapor' => $pengaturan->tanggal_rapor ?? null,
        ];

        // Encode data
        $encodedData = base64_encode(json_encode($pdfData));

        // Generate URL dengan parameter view
        $this->pdfUrl = route('pdf.generate') .
            '?data=' . $encodedData .
            '&view=' . $this->selectedPage;
    }

    private function formatOrangTua($orangTua): array
    {
        if (!$orangTua) {
            return [
                'nama' => '-',
                'pekerjaan' => '-',
                'telepon' => '-',
                'alamat' => '-',
                'status' => '-'
            ];
        }

        return [
            'nama' => $orangTua->nama ?? '-',
            'pekerjaan' => $orangTua->pekerjaan ?? '-',
            'telepon' => $orangTua->telepon ?? '-',
            'alamat' => $orangTua->alamat ?? '-',
            'status' => $orangTua->status ?? 'masih-hidup'
        ];
    }

    // ========================================
    // UTILITY METHODS
    // ========================================

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

    // ========================================
    // RENDER METHOD
    // ========================================

    public function render()
    {
        return view('livewire.admin.preview-pdf', [
            'totalStudents' => count($this->studentsList),
        ]);
    }
}
