<?php

namespace App\Livewire\Admin;

use App\Models\Rombel;
use Livewire\Component;
use App\Models\TahunAjaran;
use Livewire\WithPagination;
use App\Models\EkskulPelajar;
use App\Models\RombelPelajar;
use App\Models\Ekstrakurikuler;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class InputEkstrakurikuler extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Properti filter
    public $tahunAjaranId = null;
    public $semesterId = null;
    public $rombelId = null;
    public $ekstrakurikulerId = null;
    public $selectedRombelPengajarId = null;

    // 🔹 Properti utama
    public $rombel;
    public $selectedEkstrakurikuler;

    // 🔹 Properti pencarian & pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // 🔹 Data dropdown
    public $tahunAjaranList = [];
    public $semesterList = [];
    public $rombelList = [];
    public $ekstrakurikulerList = [];

    // 🔹 Nilai options
    public $nilaiOptions = [
        'A' => 'Sangat Baik',
        'B' => 'Baik',
        'C' => 'Cukup',
        'D' => 'Kurang'
    ];

    // 🔹 Input data ekstrakurikuler
    public $ekstrakurikulerInput = [];

    // 🔹 Cache untuk optimasi
    private $cachedEkstrakurikulerExist = null;

    // 🔹 Query string
    protected $queryString = [
        'tahunAjaranId' => ['except' => null],
        'semesterId' => ['except' => null],
        'rombelId' => ['except' => null],
        'ekstrakurikulerId' => ['except' => null],
        'searchPelajar' => ['except' => ''],
    ];

    // 🔹 Event listener
    protected $listeners = [
        'saveEkstrakurikulerConfirmed' => 'saveEkstrakurikuler',
        'resetEkstrakurikulerConfirmed' => 'resetEkstrakurikuler',
    ];

    // 🔹 Validation rules
    protected $rules = [
        'ekstrakurikulerInput.*.nilai' => 'nullable|string|in:A,B,C,D',
        'ekstrakurikulerInput.*.deskripsi' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'ekstrakurikulerInput.*.nilai.in' => 'Nilai harus salah satu dari: Sangat Baik, Baik, Cukup, Kurang',
        'ekstrakurikulerInput.*.deskripsi.string' => 'Deskripsi harus berupa teks',
        'ekstrakurikulerInput.*.deskripsi.max' => 'Deskripsi maksimal 500 karakter',
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

        // Load ekstrakurikuler list
        $this->loadEkstrakurikulerList();

        // Load data rombel dan ekstrakurikuler jika semua filter sudah dipilih
        if ($this->rombelId && $this->semesterId && $this->ekstrakurikulerId) {
            $this->loadRombelData();
            $this->loadSelectedEkstrakurikuler();
            $this->loadEkstrakurikulerPelajar();
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

        $this->rombel = Rombel::with([
            'tahunAjaranKurikulum.tahunAjaran',
            'tahunAjaranKurikulum.kurikulum',
            'waliKelas',
            'jurusan'
        ])->find($this->rombelId);

        if (!$this->rombel) {
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

    // 🔹 Load list ekstrakurikuler aktif
    private function loadEkstrakurikulerList(): void
    {
        $this->ekstrakurikulerList = Ekstrakurikuler::with('pembina')
            ->where('status', 'aktif')
            ->orderBy('nama', 'asc')
            ->get();
    }

    // 🔹 Load ekstrakurikuler yang dipilih
    private function loadSelectedEkstrakurikuler(): void
    {
        if (!$this->ekstrakurikulerId) {
            $this->selectedEkstrakurikuler = null;
            return;
        }

        $this->selectedEkstrakurikuler = Ekstrakurikuler::with('pembina')->find($this->ekstrakurikulerId);

        if (!$this->selectedEkstrakurikuler) {
            $this->ekstrakurikulerId = null;
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Ekstrakurikuler tidak ditemukan.',
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
        $this->ekstrakurikulerId = null;
        $this->selectedRombelPengajarId = null;
        $this->rombelList = [];
        $this->ekstrakurikulerInput = [];
        $this->rombel = null;
        $this->selectedEkstrakurikuler = null;
        $this->cachedEkstrakurikulerExist = null;

        $this->loadRombel();
        $this->resetPage();
    }

    // 🔹 Handler saat rombel berubah
    public function updatedRombelId(): void
    {
        $this->ekstrakurikulerId = null;
        $this->ekstrakurikulerInput = [];
        $this->selectedEkstrakurikuler = null;
        $this->cachedEkstrakurikulerExist = null;

        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
        } else {
            $this->rombel = null;
            $this->selectedRombelPengajarId = null;
        }

        $this->resetPage();
    }

    // 🔹 Handler saat ekstrakurikuler berubah
    public function updatedEkstrakurikulerId(): void
    {
        $this->ekstrakurikulerInput = [];
        $this->cachedEkstrakurikulerExist = null;

        if ($this->ekstrakurikulerId && $this->rombelId && $this->semesterId) {
            $this->loadSelectedEkstrakurikuler();
            $this->loadEkstrakurikulerPelajar();
        } else {
            $this->selectedEkstrakurikuler = null;
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
        $this->ekstrakurikulerId = null;
        $this->selectedRombelPengajarId = null;
        $this->semesterList = [];
        $this->rombelList = [];
        $this->ekstrakurikulerInput = [];
        $this->rombel = null;
        $this->selectedEkstrakurikuler = null;
        $this->cachedEkstrakurikulerExist = null;
    }

    // 🔹 Load data ekstrakurikuler pelajar
    private function loadEkstrakurikulerPelajar(): void
    {
        if (!$this->rombelId || !$this->semesterId || !$this->ekstrakurikulerId) {
            $this->ekstrakurikulerInput = [];
            $this->cachedEkstrakurikulerExist = null;
            return;
        }

        // Cache ekstrakurikuler untuk menghindari query duplikat
        $this->cachedEkstrakurikulerExist = EkskulPelajar::where('tahun_ajaran_semester_id', $this->semesterId)
            ->where('ekstrakurikuler_id', $this->ekstrakurikulerId)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombelId);
            })
            ->get()
            ->keyBy('pelajar_id');

        // Reset array input ekstrakurikuler
        $this->ekstrakurikulerInput = [];

        // Populate ekstrakurikuler input dengan data existing
        foreach ($this->cachedEkstrakurikulerExist as $pelajarId => $pelajarEkskul) {
            $this->ekstrakurikulerInput[$pelajarId] = [
                'nilai' => $pelajarEkskul->nilai ?? null,
                'deskripsi' => $pelajarEkskul->deskripsi ?? '',
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

    // 🔹 Konfirmasi simpan ekstrakurikuler
    public function confirmSaveEkstrakurikuler(): void
    {
        if (!$this->rombelId || !$this->semesterId || !$this->ekstrakurikulerId) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Silakan lengkapi semua filter terlebih dahulu.',
            ]);
            return;
        }

        // Hitung jumlah ekstrakurikuler yang akan disimpan
        $count = collect($this->ekstrakurikulerInput)
            ->filter(fn($input) => !empty($input['nilai']))
            ->count();

        if ($count === 0) {
            $this->dispatch('swal:warning', [
                'title' => 'Perhatian!',
                'text' => 'Tidak ada data ekstrakurikuler yang akan disimpan.',
            ]);
            return;
        }

        // Validasi input
        $this->validate();

        $this->dispatch('swal:confirm', [
            'title' => 'Simpan Ekstrakurikuler?',
            'text' => "Anda akan menyimpan data ekstrakurikuler untuk {$count} pelajar. Lanjutkan?",
            'confirmButtonText' => 'Ya, Simpan',
            'nextEvent' => 'saveEkstrakurikulerConfirmed',
        ]);
    }

    // 🔹 Simpan ekstrakurikuler
    public function saveEkstrakurikuler(): void
    {
        if (!$this->rombelId || !$this->semesterId || !$this->ekstrakurikulerId) {
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

        // Validasi ekstrakurikuler
        $ekstrakurikuler = Ekstrakurikuler::find($this->ekstrakurikulerId);
        if (!$ekstrakurikuler) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Data ekstrakurikuler tidak ditemukan.',
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
            $updatedCount = 0;
            $deletedCount = 0;
            $userId = Auth::id();

            foreach ($this->ekstrakurikulerInput as $pelajarId => $input) {
                // Security check: pastikan pelajar ada di rombel ini
                if (!in_array($pelajarId, $validPelajarIds)) {
                    continue;
                }

                $nilai = trim($input['nilai'] ?? '');
                $deskripsi = trim($input['deskripsi'] ?? '');

                // Cek apakah ada data yang sudah ada
                $existingData = EkskulPelajar::where('pelajar_id', $pelajarId)
                    ->where('tahun_ajaran_semester_id', $this->semesterId)
                    ->where('ekstrakurikuler_id', $this->ekstrakurikulerId)
                    ->first();

                if (empty($nilai)) {
                    // Jika nilai tidak dipilih dan ada data sebelumnya, hapus
                    if ($existingData) {
                        $existingData->delete();
                        $deletedCount++;
                    }
                    continue;
                }

                if (!in_array($nilai, array_keys($this->nilaiOptions))) {
                    continue;
                }

                if ($existingData) {
                    // Update data yang sudah ada
                    $existingData->update([
                        'nilai' => $nilai,
                        'deskripsi' => $deskripsi,
                        'updated_by' => $userId,
                    ]);
                    $updatedCount++;
                } else {
                    // Buat data baru
                    EkskulPelajar::create([
                        'pelajar_id' => $pelajarId,
                        'ekstrakurikuler_id' => $this->ekstrakurikulerId,
                        'tahun_ajaran_semester_id' => $this->semesterId,
                        'nilai' => $nilai,
                        'deskripsi' => $deskripsi,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);
                    $savedCount++;
                }
            }

            DB::commit();

            $totalProcessed = $savedCount + $updatedCount + $deletedCount;
            if ($totalProcessed > 0) {
                $messages = [];
                if ($savedCount > 0) $messages[] = "{$savedCount} baru";
                if ($updatedCount > 0) $messages[] = "{$updatedCount} diperbarui";
                if ($deletedCount > 0) $messages[] = "{$deletedCount} dihapus";

                $message = "Berhasil menyimpan data ekstrakurikuler {$ekstrakurikuler->nama} untuk {$rombel->nama}: " . implode(", ", $messages);

                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text' => $message,
                ]);

                // Clear cache dan reload data
                $this->cachedEkstrakurikulerExist = null;
                $this->loadEkstrakurikulerPelajar();
            } else {
                $this->dispatch('swal:warning', [
                    'title' => 'Perhatian!',
                    'text' => 'Tidak ada data yang disimpan.',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving ekstrakurikuler: ' . $e->getMessage(), [
                'rombel_id' => $this->rombelId,
                'semester_id' => $this->semesterId,
                'ekstrakurikuler_id' => $this->ekstrakurikulerId,
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Gagal menyimpan data ekstrakurikuler: ' . $e->getMessage(),
            ]);
        }
    }

    // 🔹 Konfirmasi reset ekstrakurikuler
    public function confirmResetEkstrakurikuler(): void
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Reset Input Ekstrakurikuler?',
            'text' => 'Semua input ekstrakurikuler akan dikosongkan (belum disimpan).',
            'confirmButtonText' => 'Ya, Reset',
            'nextEvent' => 'resetEkstrakurikulerConfirmed',
        ]);
    }

    // 🔹 Reset semua input ekstrakurikuler
    public function resetEkstrakurikuler(): void
    {
        foreach ($this->ekstrakurikulerInput as $pelajarId => $data) {
            $this->ekstrakurikulerInput[$pelajarId] = [
                'nilai' => null,
                'deskripsi' => ''
            ];
        }

        $this->dispatch('swal:success', [
            'title' => 'Direset!',
            'text' => 'Semua kolom input ekstrakurikuler telah dikosongkan.',
        ]);
    }

    // 🔹 Render view dengan data pelajar
    public function render()
    {
        $pelajarData = collect();

        if ($this->rombelId && $this->semesterId && $this->ekstrakurikulerId) {
            // Use cached ekstrakurikuler instead of querying again
            $ekstrakurikulerExist = $this->cachedEkstrakurikulerExist ?? collect();

            // Query pelajar dengan pagination
            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            // Map data pelajar dengan ekstrakurikuler mereka
            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($ekstrakurikulerExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                // Inisialisasi input ekstrakurikuler jika belum ada
                if (!isset($this->ekstrakurikulerInput[$pelajarId])) {
                    $existingEkskul = $ekstrakurikulerExist->get($pelajarId);
                    $this->ekstrakurikulerInput[$pelajarId] = [
                        'nilai' => $existingEkskul->nilai ?? null,
                        'deskripsi' => $existingEkskul->deskripsi ?? '',
                    ];
                }

                $existingEkskulData = $ekstrakurikulerExist->get($pelajarId);

                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id' => $pelajarId,
                    'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                    'nisn' => $rombelPelajar->pelajar->nisn,
                    'ekstrakurikuler_existing' => $existingEkskulData ? (object) [
                        'id' => $existingEkskulData->id,
                        'nilai' => $existingEkskulData->nilai,
                        'deskripsi' => $existingEkskulData->deskripsi,
                    ] : null,
                ];
            });
        }

        return view('livewire.admin.input-ekstrakurikuler', [
            'pelajarData' => $pelajarData,
        ]);
    }
}
