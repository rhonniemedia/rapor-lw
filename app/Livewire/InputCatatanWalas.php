<?php

namespace App\Livewire;

use App\Models\Rombel;
use App\Models\Pelajar;
use Livewire\Component;
use App\Models\TahunAjaran;
use App\Models\CatatanWaliKelas;
use Livewire\WithPagination;
use App\Models\RombelPelajar;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class InputCatatanWalas extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $tahunAjaranId = null;
    public $semesterId = null;
    public $rombelId = null;
    public $selectedRombelPengajarId = null;

    public $rombel;
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    public $tahunAjaranList = [];
    public $semesterList = [];
    public $rombelList = [];

    public $catatanInput = []; // hanya untuk input baru
    public $cachedCatatanExist = null;

    protected $queryString = [
        'tahunAjaranId' => ['except' => null],
        'semesterId' => ['except' => null],
        'rombelId' => ['except' => null],
        'searchPelajar' => ['except' => ''],
    ];

    protected $listeners = [
        'saveCatatanConfirmed' => 'saveCatatan',
        'resetCatatanConfirmed' => 'resetCatatan',
    ];

    protected $rules = [
        'catatanInput.*.catatan' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'catatanInput.*.catatan.string' => 'Catatan harus berupa teks',
        'catatanInput.*.catatan.max' => 'Catatan maksimal 1000 karakter',
    ];

    public function mount()
    {
        $this->loadTahunAjaran();

        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        if ($activeTahunAjaran && !$this->tahunAjaranId) {
            $this->tahunAjaranId = $activeTahunAjaran->id;
        }

        if ($this->tahunAjaranId) {
            $this->loadSemester();

            $activeSemester = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahunAjaranId)
                ->where('status', 'aktif')
                ->first();
            if ($activeSemester && !$this->semesterId) {
                $this->semesterId = $activeSemester->id;
            }
        }

        if ($this->tahunAjaranId && $this->semesterId) {
            $this->loadRombel();
        }

        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
            $this->loadCatatanPelajar();
        }

        // ✅ Kosongkan input awal agar tidak langsung terisi
        $this->reset(['catatanInput']);
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
            $this->selectedRombelPengajarId = null;
            return;
        }

        $this->rombel = Rombel::with(['tahunAjaranKurikulum.tahunAjaran', 'tahunAjaranKurikulum.kurikulum', 'waliKelas', 'jurusan'])
            ->find($this->rombelId);

        if (!$this->rombel) {
            $this->rombelId = null;
            $this->selectedRombelPengajarId = null;
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Rombel tidak ditemukan.',
            ]);
            return;
        }

        $this->selectedRombelPengajarId = $this->rombelId;
    }

    public function updatedTahunAjaranId(): void
    {
        $this->resetFilters();
        $this->loadSemester();

        $activeSemester = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahunAjaranId)
            ->where('status', 'aktif')
            ->first();
        if ($activeSemester) {
            $this->semesterId = $activeSemester->id;
            $this->updatedSemesterId();
        }

        $this->resetPage();
    }

    public function updatedSemesterId(): void
    {
        $this->resetFilters();
        $this->loadRombel();
        $this->resetPage();
    }

    public function updatedRombelId(): void
    {
        $this->reset(['catatanInput', 'cachedCatatanExist']);

        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
            $this->loadCatatanPelajar();
        } else {
            $this->rombel = null;
            $this->selectedRombelPengajarId = null;
        }

        $this->resetPage();
    }

    private function resetFilters(): void
    {
        $this->semesterId = null;
        $this->rombelId = null;
        $this->selectedRombelPengajarId = null;
        $this->semesterList = [];
        $this->rombelList = [];
        $this->catatanInput = [];
        $this->rombel = null;
        $this->cachedCatatanExist = null;
    }

    private function loadCatatanPelajar(): void
    {
        if (!$this->rombelId || !$this->semesterId) {
            $this->cachedCatatanExist = null;
            return;
        }

        $this->cachedCatatanExist = CatatanWaliKelas::where('tahun_ajaran_semester_id', $this->semesterId)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombelId);
            })
            ->get()
            ->keyBy('pelajar_id');
    }

    private function getPelajarQuery(): Builder
    {
        if (!$this->rombelId) {
            return RombelPelajar::whereNull('id');
        }

        $query = RombelPelajar::where('rombel_id', $this->rombelId)
            ->with(['pelajar']);

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

    public function confirmSaveCatatan(): void
    {
        if (!$this->rombelId || !$this->semesterId) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Silakan pilih rombel terlebih dahulu.',
            ]);
            return;
        }

        $count = collect($this->catatanInput)
            ->filter(fn($input) => !empty(trim($input['catatan'] ?? '')))
            ->count();

        if ($count === 0) {
            $this->dispatch('swal:warning', [
                'title' => 'Perhatian!',
                'text' => 'Tidak ada catatan yang akan disimpan.',
            ]);
            return;
        }

        $this->validate();

        $this->dispatch('swal:confirm', [
            'title' => 'Simpan Catatan?',
            'text' => "Anda akan menyimpan catatan untuk {$count} pelajar. Lanjutkan?",
            'confirmButtonText' => 'Ya, Simpan',
            'nextEvent' => 'saveCatatanConfirmed',
        ]);
    }

    public function saveCatatan(): void
    {
        if (!$this->rombelId || !$this->semesterId) {
            return;
        }

        $rombel = Rombel::find($this->rombelId);
        if (!$rombel) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Data rombel tidak ditemukan.',
            ]);
            return;
        }

        $isAdmin = Auth::user()->hasRole(['admin', 'superadmin']);
        $guruId = null;

        if ($isAdmin) {
            if (!empty($rombel->wali_kelas_slug)) {
                $guru = \App\Models\User::where('slug', $rombel->wali_kelas_slug)->first();
                if ($guru) {
                    $guruId = $guru->id;
                }
            }
        } else {
            $guruId = Auth::id();
        }

        if (!$guruId) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Gagal menentukan wali kelas (guru_id) untuk penyimpanan catatan.',
            ]);
            return;
        }

        $validPelajarIds = RombelPelajar::where('rombel_id', $this->rombelId)
            ->pluck('pelajar_id')
            ->toArray();

        DB::beginTransaction();
        try {
            $savedCount = 0;
            $updatedCount = 0;
            $deletedCount = 0;

            foreach ($this->catatanInput as $pelajarId => $input) {
                if (!in_array($pelajarId, $validPelajarIds)) continue;
                $catatan = trim($input['catatan'] ?? '');

                $existing = CatatanWaliKelas::where('pelajar_id', $pelajarId)
                    ->where('tahun_ajaran_semester_id', $this->semesterId)
                    ->first();

                if (empty($catatan)) {
                    if ($existing) {
                        $existing->delete();
                        $deletedCount++;
                    }
                    continue;
                }

                if ($existing) {
                    $existing->update([
                        'catatan' => $catatan,
                        'tanggal_input' => now(),
                        'guru_id' => $guruId,
                    ]);
                    $updatedCount++;
                } else {
                    CatatanWaliKelas::create([
                        'pelajar_id' => $pelajarId,
                        'tahun_ajaran_semester_id' => $this->semesterId,
                        'catatan' => $catatan,
                        'tanggal_input' => now(),
                        'guru_id' => $guruId,
                    ]);
                    $savedCount++;
                }
            }

            DB::commit();

            $this->reset(['catatanInput']);
            $this->cachedCatatanExist = null;
            $this->loadCatatanPelajar();

            $msg = "{$savedCount} baru, {$updatedCount} diperbarui, {$deletedCount} dihapus";
            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => "Berhasil menyimpan catatan untuk {$rombel->nama} ({$msg}).",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saving catatan: ' . $e->getMessage());
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Gagal menyimpan catatan: ' . $e->getMessage(),
            ]);
        }
    }

    public function resetCatatan(): void
    {
        $this->reset(['catatanInput']);

        $this->dispatch('swal:success', [
            'title' => 'Direset!',
            'text' => 'Semua kolom input catatan telah dikosongkan.',
        ]);
    }

    public function render()
    {
        $pelajarData = collect();

        if ($this->rombelId && $this->semesterId) {
            $catatanExist = $this->cachedCatatanExist ?? collect();

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($catatanExist) {
                $pelajarId = $rombelPelajar->pelajar_id;
                $existing = $catatanExist->get($pelajarId);

                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id' => $pelajarId,
                    'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                    'nisn' => $rombelPelajar->pelajar->nisn,
                    // ✅ tampilkan tapi jangan isi kembali input
                    'catatan_existing' => $existing ? (object) [
                        'id' => $existing->id,
                        'catatan' => $existing->catatan,
                        'tanggal_input' => $existing->tanggal_input,
                    ] : null,
                ];
            });
        }

        return view('livewire.input-catatan-walas', [
            'pelajarData' => $pelajarData,
        ]);
    }
}
