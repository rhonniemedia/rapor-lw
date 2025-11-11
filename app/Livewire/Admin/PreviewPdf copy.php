<?php

namespace App\Livewire\Admin;

use App\Models\Pelajar;
use App\Models\Rombel;
use Livewire\Component;
use App\Models\TahunAjaran;
use App\Models\RombelPelajar;
use App\Models\TahunAjaranSemester;

class PreviewPdf extends Component
{
    // Filter Properties
    public $tahunAjaranId = null;
    public $semesterId = null;
    public $rombelId = null;

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
    }

    // ========================================
    // INITIALIZATION METHODS
    // ========================================

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
        if (!$this->rombelId) {
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
                'fase' => $this->rombel->tahunAjaranKurikulum->kurikulum->nama ?? 'N/A',
                // Orang Tua / Wali
                'ayah' => $orangTuaWalis->firstWhere('hubungan', 'ayah'),
                'ibu' => $orangTuaWalis->firstWhere('hubungan', 'ibu'),
                'wali' => $orangTuaWalis->firstWhere('hubungan', 'wali'),
                // Untuk nilai (akan diisi kemudian)
                'nilai' => [],
                'kokurikuler' => '',
                'ekstrakurikuler' => [],
                'ketidakhadiran' => ['sakit' => 0, 'izin' => 0, 'tanpa_keterangan' => 0],
                'catatan_wali' => '',
                'tanggapan_ortu' => '',
            ];
        })->toArray();

        // Set first student
        if (!empty($this->studentsList)) {
            $this->currentIndex = 0;
            $this->loadCurrentStudent();
        }
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
        $selectedTahunAjaran = $this->tahunAjaranList->firstWhere('id', $this->tahunAjaranId);
        $selectedSemester = $this->semesterList->firstWhere('id', $this->semesterId);

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
            'sekolah' => 'SMK Negeri 1 Rejang Lebong', // Bisa diambil dari config/setting
            'alamat_sekolah' => 'Rejang Lebong',
            'semester' => $selectedSemester->semester->nama ?? 'N/A',
            'tahun_ajaran' => $selectedTahunAjaran->nama ?? 'N/A',
            // Orang Tua / Wali
            'ayah' => $this->formatOrangTua($this->currentStudent['ayah']),
            'ibu' => $this->formatOrangTua($this->currentStudent['ibu']),
            'wali' => $this->formatOrangTua($this->currentStudent['wali']),
            // Untuk halaman nilai (akan diisi kemudian)
            'nilai' => $this->currentStudent['nilai'],
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
                'nama' => 'Dr. ASEP SUPARMAN, S.Pi., M.Pd', // Dari setting
                'nip' => '19791116 200604 1 009'
            ],
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
