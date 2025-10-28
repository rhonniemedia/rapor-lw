<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Rombel;
use Livewire\Component;
use App\Models\TahunAjaran;
use Livewire\WithPagination;
use App\Models\RombelPelajar;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\CatatanWaliKelas;
use Illuminate\Database\Eloquent\Builder;

class InputCatatanWalas extends Component
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

    // 🔹 Properti pencarian
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // 🔹 Data dropdown
    public $tahunAjaranList = [];
    public $semesterList = [];
    public $rombelList = [];

    // 🔹 Input data catatan
    public $catatanInput = [];

    // 🔹 Opsi jenis catatan
    public $jenisCatatanOptions = [
        'perkembangan' => 'Perkembangan',
        'prestasi' => 'Prestasi',
        'perilaku' => 'Perilaku',
        'akademik' => 'Akademik',
        'non_akademik' => 'Non-Akademik',
        'umum' => 'Umum',
    ];

    // 🔹 Cache untuk optimasi
    private $cachedCatatanExist = null;

    // 🔹 Query string untuk persistensi state
    protected $queryString = [
        'tahunAjaranId' => ['except' => null],
        'semesterId' => ['except' => null],
        'rombelId' => ['except' => null],
        'searchPelajar' => ['except' => ''],
    ];

    // 🔹 Event listener
    protected $listeners = [
        'saveCatatanConfirmed' => 'saveCatatan',
        'resetCatatanConfirmed' => 'resetCatatan',
    ];

    // 🔹 Validation rules
    protected $rules = [
        'catatanInput.*.jenis_catatan' => 'nullable|string|in:perkembangan,prestasi,perilaku,akademik,non_akademik,umum',
        'catatanInput.*.catatan' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'catatanInput.*.jenis_catatan.in' => 'Jenis catatan tidak valid',
        'catatanInput.*.catatan.max' => 'Catatan maksimal 1000 karakter',
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

        // Load data rombel dan catatan jika rombel sudah dipilih
        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
            $this->loadCatatanPelajar();
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
        $this->catatanInput = [];
        $this->rombel = null;
        $this->cachedCatatanExist = null;

        $this->loadRombel();
        $this->resetPage();
    }

    // 🔹 Handler saat rombel berubah
    public function updatedRombelId(): void
    {
        $this->catatanInput = [];
        $this->cachedCatatanExist = null;

        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
            $this->loadCatatanPelajar();
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
        $this->catatanInput = [];
        $this->rombel = null;
        $this->cachedCatatanExist = null;
    }

    // 🔹 Load catatan terakhir untuk setiap pelajar
    private function loadCatatanPelajar(): void
    {
        if (!$this->rombelId || !$this->semesterId) {
            $this->catatanInput = [];
            $this->cachedCatatanExist = null;
            return;
        }

        // Cache catatan terakhir untuk setiap pelajar
        $this->cachedCatatanExist = CatatanWaliKelas::where('rombel_id', $this->rombelId)
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombelId);
            })
            ->orderBy('tanggal_input', 'desc')
            ->get()
            ->groupBy('pelajar_id')
            ->map(function ($catatanGroup) {
                return $catatanGroup->first(); // Ambil catatan terbaru
            });

        // Reset array input catatan
        $this->catatanInput = [];
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

    // 🔹 Konfirmasi simpan catatan
    public function confirmSaveCatatan(): void
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

        // Cek apakah ada catatan yang diisi
        $hasInput = false;
        foreach ($this->catatanInput as $input) {
            if (!empty($input['catatan']) && !empty($input['jenis_catatan'])) {
                $hasInput = true;
                break;
            }
        }

        if (!$hasInput) {
            $this->dispatch('swal:warning', [
                'title' => 'Peringatan!',
                'text' => 'Tidak ada catatan yang diisi.',
            ]);
            return;
        }

        $this->dispatch('swal:confirm', [
            'title' => 'Simpan Catatan?',
            'text' => 'Catatan akan tersimpan sebagai history dan tidak menimpa catatan sebelumnya.',
            'nextEvent' => 'saveCatatanConfirmed',
        ]);
    }

    // 🔹 Simpan catatan
    public function saveCatatan(): void
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
            $currentDate = Carbon::now();

            foreach ($this->catatanInput as $pelajarId => $catatan) {
                // Security check: pastikan pelajar ada di rombel ini
                if (!in_array($pelajarId, $validPelajarIds)) {
                    continue;
                }

                // Skip jika catatan atau jenis catatan kosong
                if (empty($catatan['catatan']) || empty($catatan['jenis_catatan'])) {
                    continue;
                }

                // Validasi jenis catatan
                if (!array_key_exists($catatan['jenis_catatan'], $this->jenisCatatanOptions)) {
                    continue;
                }

                // Simpan catatan baru (tidak update, tapi create baru sebagai history)
                CatatanWaliKelas::create([
                    'pelajar_id' => $pelajarId,
                    'rombel_id' => $this->rombelId,
                    'tahun_ajaran_semester_id' => $this->semesterId,
                    'jenis_catatan' => $catatan['jenis_catatan'],
                    'catatan' => trim($catatan['catatan']),
                    'tanggal_input' => $currentDate,
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                $savedCount++;
            }

            DB::commit();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => "Berhasil menyimpan {$savedCount} catatan untuk {$rombel->nama}.",
            ]);

            // Clear cache dan reload data
            $this->cachedCatatanExist = null;
            $this->catatanInput = [];
            $this->loadCatatanPelajar();
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving catatan wali kelas: ' . $e->getMessage(), [
                'rombel_id' => $this->rombelId,
                'semester_id' => $this->semesterId,
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Gagal menyimpan catatan. Silakan coba lagi.',
            ]);
        }
    }

    // 🔹 Konfirmasi reset catatan
    public function confirmResetCatatan(): void
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Reset Input Catatan?',
            'text' => 'Semua input catatan akan dikosongkan (belum disimpan).',
            'nextEvent' => 'resetCatatanConfirmed',
        ]);
    }

    // 🔹 Reset semua input catatan
    public function resetCatatan(): void
    {
        $this->catatanInput = [];

        $this->dispatch('swal:info', [
            'title' => 'Direset!',
            'text' => 'Semua kolom input catatan telah dikosongkan.',
        ]);
    }

    // 🔹 Render view dengan data pelajar
    public function render()
    {
        $pelajarData = collect();

        if ($this->rombelId && $this->semesterId) {
            // Use cached catatan instead of querying again
            $catatanExist = $this->cachedCatatanExist ?? collect();

            // Query pelajar dengan pagination
            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            // Map data pelajar dengan catatan terakhir mereka
            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($catatanExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id' => $pelajarId,
                    'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                    'nisn' => $rombelPelajar->pelajar->nisn,
                    'catatan_terakhir' => $catatanExist->get($pelajarId),
                ];
            });
        }

        return view('livewire.input-catatan-walas', [
            'pelajarData' => $pelajarData,
        ]);
    }
}
