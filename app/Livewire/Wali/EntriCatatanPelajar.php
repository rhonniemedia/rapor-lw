<?php

namespace App\Livewire\Wali;

use App\Models\Rombel;
use Livewire\Component;
use App\Models\Pelajar;
use Livewire\WithPagination;
use App\Models\CatatanPelajar;
use App\Models\CatatanWaliKelas;
use App\Models\RombelPelajar;
use App\Models\TahunAjaran;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class EntriCatatanPelajar extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filter Properties
    public $tahunAjaranId = null;
    public $semesterId = null;

    // Data Collections
    public $tahunAjaranList = [];
    public $semesterList = [];

    // Main Data
    public $rombel;

    // Search & Pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // Input catatan
    public $catatanInput = [];

    // Cache
    private $cachedCatatanExist = null;

    // Query string
    protected $queryString = [
        'tahunAjaranId' => ['except' => null],
        'semesterId'    => ['except' => null],
        'searchPelajar' => ['except' => ''],
    ];

    // Validation
    protected $rules = [
        'catatanInput.*' => 'nullable|string|max:5000',
    ];

    protected $messages = [
        'catatanInput.*.string' => 'Catatan harus berupa teks',
        'catatanInput.*.max'    => 'Catatan maksimal 5000 karakter',
    ];

    protected $listeners = ['deleteCatatan'];

    public function mount()
    {
        $this->loadRombelWaliKelas();

        if (!$this->rombel) {
            session()->flash('error', 'Anda tidak memiliki kelas binaan.');
            return redirect()->route('walikelas.dashboard');
        }

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
            $this->loadCatatanPelajar();
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

    private function loadCatatanPelajar(): void
    {
        if (!$this->semesterId) {
            $this->catatanInput      = [];
            $this->cachedCatatanExist = null;
            return;
        }

        $userId = Auth::id();

        $catatanData = CatatanWaliKelas::where('guru_id', $userId)
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->orderBy('tanggal_input', 'desc')
            ->get()
            ->groupBy('pelajar_id')
            ->map(function ($group) {
                return $group->first();
            });

        $this->cachedCatatanExist = $catatanData;
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

        $this->resetPage();
    }

    public function updatedSemesterId(): void
    {
        $this->catatanInput      = [];
        $this->cachedCatatanExist = null;

        $this->loadCatatanPelajar();
        $this->resetPage();
    }

    public function updatingSearchPelajar(): void
    {
        $this->resetPage();
    }

    // ========================================
    // QUERY HELPER
    // ========================================

    private function getPelajarQuery(): Builder
    {
        if (!$this->rombel) {
            return RombelPelajar::whereNull('id');
        }

        $query = RombelPelajar::where('rombel_id', $this->rombel->id)
            ->with(['pelajar']);

        if (!empty($this->searchPelajar)) {
            $query->whereHas('pelajar', function ($q) {
                $search = $this->searchPelajar;
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    // ========================================
    // SAVE / RESET / DELETE METHODS
    // ========================================

    public function saveCatatan(): void
    {
        if (!$this->semesterId || !$this->rombel) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text'  => 'Tidak ada semester dipilih atau kelas binaan.',
            ]);
            return;
        }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            $this->dispatch('swal:error', [
                'title' => 'Validasi Gagal!',
                'text'  => 'Periksa input Anda. ' . implode(', ', array_map(fn($err) => implode(', ', $err), $e->errors())),
            ]);
            return;
        }

        $validPelajarIds = RombelPelajar::where('rombel_id', $this->rombel->id)
            ->pluck('pelajar_id')
            ->toArray();

        DB::beginTransaction();
        try {
            $savedCount   = 0;
            $userId       = Auth::id();
            $tanggalInput = now();

            foreach ($this->catatanInput as $pelajarId => $catatan) {
                if (!in_array($pelajarId, $validPelajarIds)) {
                    continue;
                }

                if (empty($catatan)) {
                    continue;
                }

                try {
                    $existingCatatan = CatatanWaliKelas::where([
                        'pelajar_id'               => $pelajarId,
                        'guru_id'                  => $userId,
                        'tahun_ajaran_semester_id' => $this->semesterId,
                    ])->lockForUpdate()->first();

                    $dataToSave = [
                        'catatan'       => $catatan,
                        'tanggal_input' => $tanggalInput,
                        'updated_by'    => $userId,
                    ];

                    if ($existingCatatan) {
                        $existingCatatan->update($dataToSave);
                    } else {
                        $dataToSave['pelajar_id']               = $pelajarId;
                        $dataToSave['guru_id']                  = $userId;
                        $dataToSave['tahun_ajaran_semester_id'] = $this->semesterId;
                        $dataToSave['created_by']               = $userId;

                        CatatanWaliKelas::create($dataToSave);
                    }

                    $savedCount++;
                } catch (\Exception $e) {
                    Log::error('Error saving individual catatan', [
                        'pelajar_id' => $pelajarId,
                        'error'      => $e->getMessage(),
                    ]);
                    throw $e;
                }
            }

            DB::commit();

            $this->catatanInput      = [];
            $this->cachedCatatanExist = null;
            $this->loadCatatanPelajar();

            if ($savedCount > 0) {
                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text'  => "Berhasil menyimpan {$savedCount} catatan pelajar.",
                ]);
            } else {
                $this->dispatch('swal:info', [
                    'title' => 'Info',
                    'text'  => 'Tidak ada catatan baru yang disimpan.',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving catatan', [
                'message'     => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
                'semester_id' => $this->semesterId,
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text'  => 'Gagal menyimpan data: ' . $e->getMessage(),
            ]);
        }
    }

    public function resetCatatan(): void
    {
        if ($this->cachedCatatanExist === null) {
            $this->loadCatatanPelajar();
        }

        foreach ($this->catatanInput as $pelajarId => $catatan) {
            if ($this->cachedCatatanExist && $this->cachedCatatanExist->has($pelajarId)) {
                $this->catatanInput[$pelajarId] = $this->cachedCatatanExist->get($pelajarId)->catatan;
            } else {
                $this->catatanInput[$pelajarId] = null;
            }
        }

        $this->dispatch('swal:info', [
            'title' => 'Direset!',
            'text'  => 'Input telah dikembalikan ke data tersimpan.',
        ]);
    }

    public function deleteCatatan($pelajarId = null): void
    {
        if (is_array($pelajarId) && isset($pelajarId[0])) {
            $pelajarId = $pelajarId[0];
        }

        if (!$pelajarId || !$this->semesterId) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text'  => 'ID Pelajar tidak ditemukan atau data tidak valid.',
            ]);
            return;
        }

        $userId = Auth::id();

        try {
            $deleted = CatatanWaliKelas::where('pelajar_id', $pelajarId)
                ->where('guru_id', $userId)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->delete();

            if ($deleted) {
                unset($this->catatanInput[$pelajarId]);

                $this->cachedCatatanExist = null;
                $this->loadCatatanPelajar();

                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text'  => "Berhasil menghapus {$deleted} catatan pelajar.",
                ]);
            } else {
                $this->dispatch('swal:info', [
                    'title' => 'Info',
                    'text'  => 'Data tidak ditemukan.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting catatan: ' . $e->getMessage(), ['pelajar_id' => $pelajarId]);
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text'  => 'Terjadi kesalahan saat menghapus data.',
            ]);
        }
    }

    // ========================================
    // UTILITY METHODS
    // ========================================

    private function resetFilters(): void
    {
        $this->semesterId        = null;
        $this->semesterList      = [];
        $this->catatanInput      = [];
        $this->cachedCatatanExist = null;
    }

    // ========================================
    // RENDER METHOD
    // ========================================

    public function render()
    {
        $pelajarData = collect();
        $totalSiswa  = 0;

        if ($this->rombel) {
            $totalSiswa = RombelPelajar::where('rombel_id', $this->rombel->id)->count();
        }

        if ($this->rombel && $this->semesterId) {
            if ($this->cachedCatatanExist === null) {
                $this->loadCatatanPelajar();
            }

            $catatanExist = $this->cachedCatatanExist ?? collect();

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy(
                    Pelajar::select('nama_lengkap')
                        ->whereColumn('pelajars.id', 'rombel_pelajars.pelajar_id')
                )
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($catatanExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                if (!array_key_exists($pelajarId, $this->catatanInput)) {
                    $this->catatanInput[$pelajarId] = $catatanExist->get($pelajarId)?->catatan;
                }

                $catatanData = $catatanExist->get($pelajarId);

                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id'        => $pelajarId,
                    'nama_lengkap'      => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk'       => $rombelPelajar->pelajar->nomor_induk,
                    'nisn'              => $rombelPelajar->pelajar->nisn,
                    'catatan_sekarang'  => $catatanData?->catatan,
                    'tanggal_input'     => $catatanData?->tanggal_input,
                ];
            });
        }

        // Resolve selected semester label for display
        $selectedSemesterObj = $this->semesterId
            ? collect($this->semesterList)->firstWhere('id', $this->semesterId)
            : null;

        return view('livewire.wali.entri-catatan-pelajar', [
            'pelajarData'         => $pelajarData,
            'totalSiswa'          => $totalSiswa,
            'selectedSemesterObj' => $selectedSemesterObj,
        ]);
    }
}
