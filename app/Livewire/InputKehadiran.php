<?php

namespace App\Livewire;

use App\Models\Rombel;
use Livewire\Component;
use App\Models\Kehadiran;
use App\Models\TahunAjaran;
use Livewire\WithPagination;
use App\Models\RombelPelajar;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class InputKehadiran extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Properti filter
    public $tahunAjaranId = null;
    public $semesterId = null;
    public $rombelId = null;

    // 🔹 Properti utama
    public $rombel;

    // 🔹 Properti pencarian & pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // 🔹 Data dropdown
    public $tahunAjaranList = [];
    public $semesterList = [];
    public $rombelList = [];

    // 🔹 Input data kehadiran
    public $kehadiranInput = [];

    // 🔹 Cache untuk optimasi
    private $cachedKehadiranExist = null;

    // 🔹 Query string untuk persistensi state
    protected $queryString = [
        'tahunAjaranId' => ['except' => null],
        'semesterId' => ['except' => null],
        'rombelId' => ['except' => null],
        'searchPelajar' => ['except' => ''],
    ];

    // 🔹 Event listener
    protected $listeners = [
        'saveKehadiranConfirmed' => 'saveKehadiran',
        'resetKehadiranConfirmed' => 'resetKehadiran',
    ];

    // 🔹 Validation rules
    protected $rules = [
        'kehadiranInput.*.sakit' => 'nullable|integer|min:0|max:999',
        'kehadiranInput.*.izin' => 'nullable|integer|min:0|max:999',
        'kehadiranInput.*.tanpa_keterangan' => 'nullable|integer|min:0|max:999',
    ];

    protected $messages = [
        'kehadiranInput.*.sakit.integer' => 'Jumlah sakit harus berupa angka',
        'kehadiranInput.*.sakit.min' => 'Jumlah sakit minimal adalah 0',
        'kehadiranInput.*.sakit.max' => 'Jumlah sakit maksimal adalah 999',
        'kehadiranInput.*.izin.integer' => 'Jumlah izin harus berupa angka',
        'kehadiranInput.*.izin.min' => 'Jumlah izin minimal adalah 0',
        'kehadiranInput.*.izin.max' => 'Jumlah izin maksimal adalah 999',
        'kehadiranInput.*.tanpa_keterangan.integer' => 'Jumlah tanpa keterangan harus berupa angka',
        'kehadiranInput.*.tanpa_keterangan.min' => 'Jumlah tanpa keterangan minimal adalah 0',
        'kehadiranInput.*.tanpa_keterangan.max' => 'Jumlah tanpa keterangan maksimal adalah 999',
    ];

    public function mount()
    {
        // Load tahun ajaran yang aktif
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

        // Load data rombel dan kehadiran jika rombel sudah dipilih
        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
            $this->loadKehadiranPelajar();
        }
    }

    // 🔹 Load data tahun ajaran
    private function loadTahunAjaran(): void
    {
        $this->tahunAjaranList = TahunAjaran::orderBy('tgl_mulai', 'desc')->get();
    }

    // 🔹 Load data semester
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

    // 🔹 Load data rombel berdasarkan tahun ajaran
    private function loadRombel(): void
    {
        if (!$this->tahunAjaranId) {
            $this->rombelList = [];
            return;
        }

        $this->rombelList = Rombel::whereHas('tahunAjaranKurikulum', function ($q) {
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
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Rombel tidak ditemukan.',
            ]);
        }
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

        $this->resetPage();
    }

    // 🔹 Handler saat semester berubah
    public function updatedSemesterId(): void
    {
        $this->rombelId = null;
        $this->rombelList = [];
        $this->kehadiranInput = [];
        $this->rombel = null;
        $this->cachedKehadiranExist = null;

        $this->loadRombel();
        $this->resetPage();
    }

    // 🔹 Handler saat rombel berubah
    public function updatedRombelId(): void
    {
        $this->kehadiranInput = [];
        $this->cachedKehadiranExist = null;

        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
            $this->loadKehadiranPelajar();
        } else {
            $this->rombel = null;
        }

        $this->resetPage();
    }

    // 🔹 Reset pagination saat search berubah
    public function updatingSearchPelajar(): void
    {
        $this->resetPage();
    }

    // 🔹 Helper method untuk reset filters
    private function resetFilters(): void
    {
        $this->semesterId = null;
        $this->rombelId = null;
        $this->semesterList = [];
        $this->rombelList = [];
        $this->kehadiranInput = [];
        $this->rombel = null;
        $this->cachedKehadiranExist = null;
    }

    // 🔹 Load data kehadiran pelajar
    private function loadKehadiranPelajar(): void
    {
        if (!$this->rombelId || !$this->semesterId) {
            $this->kehadiranInput = [];
            $this->cachedKehadiranExist = null;
            return;
        }

        // Cache kehadiran untuk menghindari query duplikat
        $this->cachedKehadiranExist = Kehadiran::where('rombel_id', $this->rombelId)
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->get()
            ->keyBy('pelajar_id');

        // Reset array input kehadiran
        $this->kehadiranInput = [];

        // Populate kehadiran input dengan data existing
        foreach ($this->cachedKehadiranExist as $pelajarId => $kehadiran) {
            $this->kehadiranInput[$pelajarId] = [
                'sakit' => $kehadiran->jumlah_sakit ?? 0,
                'izin' => $kehadiran->jumlah_izin ?? 0,
                'tanpa_keterangan' => $kehadiran->jumlah_tanpa_keterangan ?? 0,
            ];
        }
    }

    // 🔹 Get query data pelajar dengan filter
    private function getPelajarQuery(): Builder
    {
        if (!$this->rombelId) {
            return RombelPelajar::whereNull('id'); // Return empty query
        }

        $query = RombelPelajar::where('rombel_id', $this->rombelId)
            ->with(['pelajar']);

        // Filter pencarian
        if (!empty($this->searchPelajar)) {
            $search = $this->searchPelajar;
            $query->whereHas('pelajar', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    // 🔹 Konfirmasi simpan kehadiran
    public function confirmSaveKehadiran(): void
    {
        if (!$this->rombelId || !$this->semesterId) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Silakan pilih rombel terlebih dahulu.',
            ]);
            return;
        }

        // Validasi input
        $this->validate();

        $this->dispatch('swal:confirm', [
            'title' => 'Simpan Kehadiran?',
            'text' => 'Semua data kehadiran yang diinput akan disimpan.',
            'nextEvent' => 'saveKehadiranConfirmed',
        ]);
    }

    // 🔹 Simpan kehadiran
    public function saveKehadiran(): void
    {
        if (!$this->rombelId || !$this->semesterId) {
            return;
        }

        // Validasi rombel
        $rombel = Rombel::find($this->rombelId);
        if (!$rombel) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Data rombel tidak ditemukan.',
            ]);
            return;
        }

        // Security: Validasi pelajar_id yang valid
        $validPelajarIds = RombelPelajar::where('rombel_id', $this->rombelId)
            ->pluck('pelajar_id')
            ->toArray();

        DB::beginTransaction();
        try {
            $savedCount = 0;

            foreach ($this->kehadiranInput as $pelajarId => $kehadiran) {
                // Security check: pastikan pelajar ada di rombel ini
                if (!in_array($pelajarId, $validPelajarIds)) {
                    continue;
                }

                // Skip jika semua nilai kosong atau null
                if (empty($kehadiran['sakit']) && empty($kehadiran['izin']) && empty($kehadiran['tanpa_keterangan'])) {
                    continue;
                }

                // Validasi dan bersihkan data
                $sakit = is_numeric($kehadiran['sakit']) ? intval($kehadiran['sakit']) : 0;
                $izin = is_numeric($kehadiran['izin']) ? intval($kehadiran['izin']) : 0;
                $tanpaKeterangan = is_numeric($kehadiran['tanpa_keterangan']) ? intval($kehadiran['tanpa_keterangan']) : 0;

                // Validasi range
                if ($sakit < 0 || $sakit > 999 || $izin < 0 || $izin > 999 || $tanpaKeterangan < 0 || $tanpaKeterangan > 999) {
                    continue;
                }

                // Use updateOrCreate for cleaner code
                Kehadiran::updateOrCreate(
                    [
                        'pelajar_id' => $pelajarId,
                        'rombel_id' => $this->rombelId,
                        'tahun_ajaran_semester_id' => $this->semesterId,
                    ],
                    [
                        'jumlah_sakit' => $sakit,
                        'jumlah_izin' => $izin,
                        'jumlah_tanpa_keterangan' => $tanpaKeterangan,
                        'updated_by' => Auth::id(),
                        'created_by' => Auth::id(), // Only set on creation
                    ]
                );

                $savedCount++;
            }

            DB::commit();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => "Berhasil menyimpan {$savedCount} data kehadiran untuk {$rombel->nama}.",
            ]);

            // Clear cache dan reload data
            $this->cachedKehadiranExist = null;
            $this->loadKehadiranPelajar();
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving kehadiran: ' . $e->getMessage(), [
                'rombel_id' => $this->rombelId,
                'semester_id' => $this->semesterId,
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Gagal menyimpan kehadiran. Silakan coba lagi.',
            ]);
        }
    }

    // 🔹 Konfirmasi reset kehadiran
    public function confirmResetKehadiran(): void
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Reset Input Kehadiran?',
            'text' => 'Semua input kehadiran akan dikosongkan (belum disimpan).',
            'nextEvent' => 'resetKehadiranConfirmed',
        ]);
    }

    // 🔹 Reset semua input kehadiran
    public function resetKehadiran(): void
    {
        foreach ($this->kehadiranInput as $pelajarId => $data) {
            $this->kehadiranInput[$pelajarId] = [
                'sakit' => 0,
                'izin' => 0,
                'tanpa_keterangan' => 0,
            ];
        }

        $this->dispatch('swal:info', [
            'title' => 'Direset!',
            'text' => 'Semua kolom input kehadiran telah dikosongkan.',
        ]);
    }

    // 🔹 Helper untuk menghitung total ketidakhadiran
    public function getTotalKetidakhadiran($pelajarId): int
    {
        if (!isset($this->kehadiranInput[$pelajarId])) {
            return 0;
        }

        $data = $this->kehadiranInput[$pelajarId];
        return ($data['sakit'] ?? 0) + ($data['izin'] ?? 0) + ($data['tanpa_keterangan'] ?? 0);
    }

    // 🔹 Render view dengan data pelajar
    public function render()
    {
        $pelajarData = collect();

        if ($this->rombelId && $this->semesterId) {
            // Use cached kehadiran instead of querying again
            $kehadiranExist = $this->cachedKehadiranExist ?? collect();

            // Query pelajar dengan pagination
            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            // Map data pelajar dengan kehadiran mereka
            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($kehadiranExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                // Inisialisasi input kehadiran jika belum ada
                if (!isset($this->kehadiranInput[$pelajarId])) {
                    $existingKehadiran = $kehadiranExist->get($pelajarId);
                    $this->kehadiranInput[$pelajarId] = [
                        'sakit' => $existingKehadiran->jumlah_sakit ?? 0,
                        'izin' => $existingKehadiran->jumlah_izin ?? 0,
                        'tanpa_keterangan' => $existingKehadiran->jumlah_tanpa_keterangan ?? 0,
                    ];
                }

                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id' => $pelajarId,
                    'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                    'nisn' => $rombelPelajar->pelajar->nisn,
                    'kehadiran_sekarang' => $kehadiranExist->get($pelajarId),
                    'total_ketidakhadiran' => $this->getTotalKetidakhadiran($pelajarId),
                ];
            });
        }

        return view('livewire.input-kehadiran', [
            'pelajarData' => $pelajarData,
        ]);
    }
}
