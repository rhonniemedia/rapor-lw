<?php

namespace App\Livewire;

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
    public $selectedRombelPengajarId = null;

    // 🔹 Properti utama
    public $rombel;

    // 🔹 Properti pencarian & pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // 🔹 Data dropdown
    public $tahunAjaranList = [];
    public $semesterList = [];
    public $rombelList = [];
    public $ekstrakurikulerList = [];

    // 🔹 Input data ekstrakurikuler
    public $ekstrakurikulerInput = [];

    // 🔹 Cache untuk optimasi
    private $cachedEkstrakurikulerExist = null;

    // 🔹 Query string
    protected $queryString = [
        'tahunAjaranId' => ['except' => null],
        'semesterId' => ['except' => null],
        'rombelId' => ['except' => null],
        'searchPelajar' => ['except' => ''],
    ];

    // 🔹 Event listener
    protected $listeners = [
        'saveEkstrakurikulerConfirmed' => 'saveEkstrakurikuler',
        'resetEkstrakurikulerConfirmed' => 'resetEkstrakurikuler',
    ];

    // 🔹 Validation rules
    protected $rules = [
        'ekstrakurikulerInput.*.ekstrakurikuler_id' => 'nullable|exists:ekstrakurikulers,id',
        'ekstrakurikulerInput.*.nilai' => 'nullable|integer|min:0|max:100',
        'ekstrakurikulerInput.*.keterangan' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'ekstrakurikulerInput.*.ekstrakurikuler_id.exists' => 'Ekstrakurikuler tidak valid',
        'ekstrakurikulerInput.*.nilai.integer' => 'Nilai harus berupa angka',
        'ekstrakurikulerInput.*.nilai.min' => 'Nilai minimal adalah 0',
        'ekstrakurikulerInput.*.nilai.max' => 'Nilai maksimal adalah 100',
        'ekstrakurikulerInput.*.keterangan.string' => 'Keterangan harus berupa teks',
        'ekstrakurikulerInput.*.keterangan.max' => 'Keterangan maksimal 500 karakter',
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

        // Load data rombel dan ekstrakurikuler jika rombel sudah dipilih
        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
            $this->loadEkstrakurikulerList();
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
        $this->ekstrakurikulerList = Ekstrakurikuler::where('status', 'aktif')
            ->orderBy('nama', 'asc')
            ->get();
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
        $this->selectedRombelPengajarId = null;
        $this->rombelList = [];
        $this->ekstrakurikulerInput = [];
        $this->rombel = null;
        $this->cachedEkstrakurikulerExist = null;

        $this->loadRombel();
        $this->resetPage();
    }

    // 🔹 Handler saat rombel berubah
    public function updatedRombelId(): void
    {
        $this->ekstrakurikulerInput = [];
        $this->cachedEkstrakurikulerExist = null;

        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
            $this->loadEkstrakurikulerList();
            $this->loadEkstrakurikulerPelajar();
        } else {
            $this->rombel = null;
            $this->selectedRombelPengajarId = null;
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
        $this->selectedRombelPengajarId = null;
        $this->semesterList = [];
        $this->rombelList = [];
        $this->ekstrakurikulerInput = [];
        $this->rombel = null;
        $this->cachedEkstrakurikulerExist = null;
    }

    // 🔹 Load data ekstrakurikuler pelajar
    private function loadEkstrakurikulerPelajar(): void
    {
        if (!$this->rombelId || !$this->semesterId) {
            $this->ekstrakurikulerInput = [];
            $this->cachedEkstrakurikulerExist = null;
            return;
        }

        // Cache ekstrakurikuler untuk menghindari query duplikat
        $this->cachedEkstrakurikulerExist = EkskulPelajar::where('tahun_ajaran_semester_id', $this->semesterId)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombelId);
            })
            ->with('ekstrakurikuler')
            ->get()
            ->keyBy('pelajar_id');

        // Reset array input ekstrakurikuler
        $this->ekstrakurikulerInput = [];

        // Populate ekstrakurikuler input dengan data existing
        foreach ($this->cachedEkstrakurikulerExist as $pelajarId => $pelajarEkskul) {
            $this->ekstrakurikulerInput[$pelajarId] = [
                'ekstrakurikuler_id' => $pelajarEkskul->ekstrakurikuler_id ?? null,
                'nilai' => $pelajarEkskul->nilai ?? null,
                'keterangan' => $pelajarEkskul->keterangan ?? '',
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
        if (!$this->rombelId || !$this->semesterId) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Silakan pilih rombel terlebih dahulu.',
            ]);
            return;
        }

        // Hitung jumlah ekstrakurikuler yang akan disimpan
        $count = collect($this->ekstrakurikulerInput)
            ->filter(fn($input) => !empty($input['ekstrakurikuler_id']))
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
            $updatedCount = 0;
            $deletedCount = 0;
            $userId = Auth::id();

            foreach ($this->ekstrakurikulerInput as $pelajarId => $input) {
                // Security check: pastikan pelajar ada di rombel ini
                if (!in_array($pelajarId, $validPelajarIds)) {
                    continue;
                }

                $ekstrakurikulerId = $input['ekstrakurikuler_id'] ?? null;
                $nilai = !empty($input['nilai']) ? intval($input['nilai']) : null;
                $keterangan = trim($input['keterangan'] ?? '');

                // Cek apakah ada data yang sudah ada
                $existingData = EkskulPelajar::where('pelajar_id', $pelajarId)
                    ->where('tahun_ajaran_semester_id', $this->semesterId)
                    ->first();

                if (empty($ekstrakurikulerId)) {
                    // Jika ekstrakurikuler tidak dipilih dan ada data sebelumnya, hapus
                    if ($existingData) {
                        $existingData->delete();
                        $deletedCount++;
                    }
                    continue;
                }

                // Validasi nilai range
                if ($nilai !== null && ($nilai < 0 || $nilai > 100)) {
                    continue;
                }

                if ($existingData) {
                    // Update data yang sudah ada
                    $existingData->update([
                        'ekstrakurikuler_id' => $ekstrakurikulerId,
                        'nilai' => $nilai,
                        'keterangan' => $keterangan,
                        'updated_by' => $userId,
                    ]);
                    $updatedCount++;
                } else {
                    // Buat data baru
                    EkskulPelajar::create([
                        'pelajar_id' => $pelajarId,
                        'ekstrakurikuler_id' => $ekstrakurikulerId,
                        'tahun_ajaran_semester_id' => $this->semesterId,
                        'nilai' => $nilai,
                        'keterangan' => $keterangan,
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

                $message = "Berhasil menyimpan data ekstrakurikuler untuk {$rombel->nama}: " . implode(", ", $messages);

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
                'ekstrakurikuler_id' => null,
                'nilai' => null,
                'keterangan' => ''
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

        if ($this->rombelId && $this->semesterId) {
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
                        'ekstrakurikuler_id' => $existingEkskul->ekstrakurikuler_id ?? null,
                        'nilai' => $existingEkskul->nilai ?? null,
                        'keterangan' => $existingEkskul->keterangan ?? '',
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
                        'ekstrakurikuler' => $existingEkskulData->ekstrakurikuler,
                        'nilai' => $existingEkskulData->nilai,
                        'keterangan' => $existingEkskulData->keterangan,
                    ] : null,
                ];
            });
        }

        return view('livewire.input-ekstrakurikuler', [
            'pelajarData' => $pelajarData,
        ]);
    }
}
