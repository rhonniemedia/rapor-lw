<?php

namespace App\Livewire;

use App\Models\Rombel;
use Livewire\Component;
use App\Models\TahunAjaran;
use App\Models\TahunAjaranSemester;

class Template extends Component
{
    // 🔹 Properti filter
    public $tahunAjaranId = null;
    public $semesterId = null;
    public $rombelId = null;
    public $selectedRombelPengajarId = null; // Untuk kompatibilitas dengan blade

    // 🔹 Properti utama
    public $rombel;

    // 🔹 Data dropdown
    public $tahunAjaranList = [];
    public $semesterList = [];
    public $rombelList = [];

    // 🔹 Query string untuk persistensi state (hanya yang relevan dengan filter)
    protected $queryString = [
        'tahunAjaranId' => ['except' => null],
        'semesterId' => ['except' => null],
        'rombelId' => ['except' => null],
    ];

    public function mount()
    {
        // Load tahun ajaran
        $this->loadTahunAjaran();

        // Set default tahun ajaran aktif
        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        if ($activeTahunAjaran && !$this->tahunAjaranId) {
            $this->tahunAjaranId = $activeTahunAjaran->id;
        }

        // Load semester jika tahun ajaran sudah dipilih
        if ($this->tahunAjaranId) {
            $this->loadSemester();

            // Set default semester aktif
            $activeSemester = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahunAjaranId)
                ->where('status', 'aktif')
                ->first();
            if ($activeSemester && !$this->semesterId) {
                $this->semesterId = $activeSemester->id;
            }
        }

        // Load rombel jika tahun ajaran dan semester sudah dipilih
        if ($this->tahunAjaranId && $this->semesterId) {
            $this->loadRombel();
        }

        // Load data rombel jika rombel sudah dipilih
        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
        }
    }

    // 🔹 Load data tahun ajaran
    private function loadTahunAjaran(): void
    {
        // Ambil semua tahun ajaran, diurutkan berdasarkan tanggal mulai terbaru
        $this->tahunAjaranList = TahunAjaran::orderBy('tgl_mulai', 'desc')->get();
    }

    // 🔹 Load data semester
    private function loadSemester(): void
    {
        if (!$this->tahunAjaranId) {
            $this->semesterList = [];
            return;
        }

        // Ambil semester berdasarkan tahun ajaran yang dipilih
        $this->semesterList = TahunAjaranSemester::with('semester')
            ->where('tahun_ajaran_id', $this->tahunAjaranId)
            ->get();
    }

    // 🔹 Load data rombel berdasarkan tahun ajaran
    private function loadRombel(): void
    {
        if (!$this->tahunAjaranId) {
            $this->rombelList = [];
            return;
        }

        // Ambil rombel berdasarkan tahun ajaran yang dipilih
        $this->rombelList = Rombel::whereHas('tahunAjaran', function ($q) {
            $q->where('tahun_ajaran_id', $this->tahunAjaranId);
        })
            ->orderBy('tingkat', 'asc')
            ->orderBy('nama', 'asc')
            ->get();
    }

    // 🔹 Load data rombel yang dipilih
    private function loadRombelData(): void
    {
        if (!$this->rombelId) {
            $this->rombel = null;
            $this->selectedRombelPengajarId = null;
            return;
        }

        // Ambil data rombel lengkap dengan relasi yang dibutuhkan di view (kurikulum, wali kelas, jurusan)
        $this->rombel = Rombel::with([
            'tahunAjaranKurikulum.tahunAjaran',
            'tahunAjaranKurikulum.kurikulum',
            'waliKelas',
            'jurusan'
        ])->find($this->rombelId);

        if (!$this->rombel) {
            // Reset jika rombel tidak ditemukan
            $this->rombelId = null;
            $this->selectedRombelPengajarId = null;
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Rombel tidak ditemukan.',
            ]);
            return;
        }

        // Set selectedRombelPengajarId untuk kompatibilitas dengan blade
        $this->selectedRombelPengajarId = $this->rombelId;
    }

    // 🔹 Handler saat tahun ajaran berubah
    public function updatedTahunAjaranId(): void
    {
        $this->resetFilters();
        $this->loadSemester();

        // Auto-select semester aktif
        $activeSemester = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahunAjaranId)
            ->where('status', 'aktif')
            ->first();
        if ($activeSemester) {
            $this->semesterId = $activeSemester->id;
            $this->updatedSemesterId();
        }
    }

    // 🔹 Handler saat semester berubah
    public function updatedSemesterId(): void
    {
        $this->rombelId = null;
        $this->selectedRombelPengajarId = null;
        $this->rombelList = [];
        $this->rombel = null;

        $this->loadRombel();
    }

    // 🔹 Handler saat rombel berubah
    public function updatedRombelId(): void
    {
        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
        } else {
            $this->rombel = null;
            $this->selectedRombelPengajarId = null;
        }
    }

    // 🔹 Helper method untuk reset filters
    private function resetFilters(): void
    {
        $this->semesterId = null;
        $this->rombelId = null;
        $this->selectedRombelPengajarId = null;
        $this->semesterList = [];
        $this->rombelList = [];
        $this->rombel = null;
    }

    // 🔹 Render view
    public function render()
    {
        return view('livewire.template');
    }
}
