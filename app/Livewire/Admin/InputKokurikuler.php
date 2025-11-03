<?php

namespace App\Livewire\Admin;

use App\Models\Rombel;
use Livewire\Component;
use App\Models\TahunAjaran;
use Livewire\WithPagination;
use App\Models\Kokurikuler;
use App\Models\RombelPelajar;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class InputKokurikuler extends Component
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

    // 🔹 Predikat options
    public $predikatOptions = [
        'A' => 'Sangat Baik',
        'B' => 'Baik',
        'C' => 'Cukup',
        'D' => 'Kurang'
    ];

    // 🔹 Input data kokurikuler
    public $kokurikulerInput = [];

    // 🔹 Cache untuk optimasi
    private $cachedKokurikulerExist = null;

    // 🔹 Query string
    protected $queryString = [
        'tahunAjaranId' => ['except' => null],
        'semesterId' => ['except' => null],
        'rombelId' => ['except' => null],
        'searchPelajar' => ['except' => ''],
    ];

    // 🔹 Event listener
    protected $listeners = [
        'resetKokurikulerConfirmed' => 'resetKokurikuler',
        'deleteKokurikuler' => 'deleteKokurikuler', // ✅ NEW
    ];

    // 🔹 Validation rules
    protected $rules = [
        'kokurikulerInput.*.predikat' => 'nullable|string|in:A,B,C,D',
        'kokurikulerInput.*.capaian' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'kokurikulerInput.*.predikat.in' => 'Predikat harus salah satu dari: A, B, C, D',
        'kokurikulerInput.*.capaian.string' => 'Capaian harus berupa teks',
        'kokurikulerInput.*.capaian.max' => 'Capaian maksimal 1000 karakter',
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

        // Load data rombel dan kokurikuler jika rombel sudah dipilih
        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
            $this->loadKokurikulerPelajar();
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
        $this->kokurikulerInput = [];
        $this->rombel = null;
        $this->cachedKokurikulerExist = null;

        $this->loadRombel();
        $this->resetPage();
    }

    // 🔹 Handler saat rombel berubah
    public function updatedRombelId(): void
    {
        $this->kokurikulerInput = [];
        $this->cachedKokurikulerExist = null;

        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
            $this->loadKokurikulerPelajar();
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
        $this->kokurikulerInput = [];
        $this->rombel = null;
        $this->cachedKokurikulerExist = null;
    }

    // 🔹 Load data kokurikuler pelajar
    private function loadKokurikulerPelajar(): void
    {
        if (!$this->rombelId || !$this->semesterId) {
            $this->kokurikulerInput = [];
            $this->cachedKokurikulerExist = null;
            return;
        }

        // Cache kokurikuler untuk menghindari query duplikat
        $this->cachedKokurikulerExist = Kokurikuler::where('tahun_ajaran_semester_id', $this->semesterId)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombelId);
            })
            ->get()
            ->keyBy('pelajar_id');

        // Reset array input kokurikuler
        $this->kokurikulerInput = [];

        // Populate kokurikuler input dengan data existing
        foreach ($this->cachedKokurikulerExist as $pelajarId => $pelajarKokurikuler) {
            $this->kokurikulerInput[$pelajarId] = [
                'predikat' => $pelajarKokurikuler->predikat ?? null,
                'capaian' => $pelajarKokurikuler->capaian ?? '',
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

    // // 🔹 Konfirmasi simpan kokurikuler
    // public function confirmSaveKokurikuler(): void
    // {
    //     if (!$this->rombelId || !$this->semesterId) {
    //         $this->dispatch('swal:error', [
    //             'title' => 'Error!',
    //             'text' => 'Silakan pilih rombel terlebih dahulu.',
    //         ]);
    //         return;
    //     }

    //     // Hitung jumlah kokurikuler yang akan disimpan
    //     $count = collect($this->kokurikulerInput)
    //         ->filter(fn($input) => !empty($input['predikat']))
    //         ->count();

    //     if ($count === 0) {
    //         $this->dispatch('swal:warning', [
    //             'title' => 'Perhatian!',
    //             'text' => 'Tidak ada data kokurikuler yang akan disimpan.',
    //         ]);
    //         return;
    //     }

    //     // Validasi input
    //     $this->validate();

    //     $this->dispatch('swal:confirm', [
    //         'title' => 'Simpan Kokurikuler?',
    //         'text' => "Anda akan menyimpan data kokurikuler untuk {$count} pelajar. Lanjutkan?",
    //         'confirmButtonText' => 'Ya, Simpan',
    //         'nextEvent' => 'saveKokurikulerConfirmed',
    //     ]);
    // }

    // 🔹 Simpan kokurikuler
    public function saveKokurikuler(): void
    {
        $this->validate();

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
            $guruId = Auth::id();
            $tanggalInput = now();

            foreach ($this->kokurikulerInput as $pelajarId => $input) {
                // Security check: pastikan pelajar ada di rombel ini
                if (!in_array($pelajarId, $validPelajarIds)) {
                    continue;
                }

                $predikat = trim($input['predikat'] ?? '');
                $capaian = trim($input['capaian'] ?? '');

                // Cek apakah ada data yang sudah ada
                $existingData = Kokurikuler::where('pelajar_id', $pelajarId)
                    ->where('tahun_ajaran_semester_id', $this->semesterId)
                    ->first();

                if (empty($predikat)) {
                    // Jika predikat tidak dipilih dan ada data sebelumnya, hapus
                    if ($existingData) {
                        $existingData->delete();
                        $deletedCount++;
                    }
                    continue;
                }

                // Validasi predikat
                if (!in_array($predikat, array_keys($this->predikatOptions))) {
                    continue;
                }

                if ($existingData) {
                    // Update data yang sudah ada
                    $existingData->update([
                        'predikat' => $predikat,
                        'capaian' => $capaian,
                        'guru_id' => $guruId,
                        'tanggal_input' => $tanggalInput,
                    ]);
                    $updatedCount++;
                } else {
                    // Buat data baru
                    Kokurikuler::create([
                        'pelajar_id' => $pelajarId,
                        'guru_id' => $guruId,
                        'tahun_ajaran_semester_id' => $this->semesterId,
                        'predikat' => $predikat,
                        'capaian' => $capaian,
                        'tanggal_input' => $tanggalInput,
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

                $message = "Berhasil menyimpan data kokurikuler untuk {$rombel->nama}: " . implode(", ", $messages);

                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text' => $message,
                ]);

                // Clear cache dan reload data
                $this->cachedKokurikulerExist = null;
                $this->loadKokurikulerPelajar();
            } else {
                $this->dispatch('swal:warning', [
                    'title' => 'Perhatian!',
                    'text' => 'Tidak ada data yang disimpan.',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving kokurikuler: ' . $e->getMessage(), [
                'rombel_id' => $this->rombelId,
                'semester_id' => $this->semesterId,
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Gagal menyimpan data kokurikuler: ' . $e->getMessage(),
            ]);
        }
    }

    // 🔹 Konfirmasi reset kokurikuler
    public function confirmResetKokurikuler(): void
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Reset Input Kokurikuler?',
            'text' => 'Semua input kokurikuler akan dikosongkan (belum disimpan).',
            'confirmButtonText' => 'Ya, Reset',
            'nextEvent' => 'resetKokurikulerConfirmed',
        ]);
    }

    // 🔹 Reset semua input kokurikuler
    public function resetKokurikuler(): void
    {
        foreach ($this->kokurikulerInput as $pelajarId => $data) {
            $this->kokurikulerInput[$pelajarId] = [
                'predikat' => null,
                'capaian' => ''
            ];
        }

        $this->dispatch('swal:success', [
            'title' => 'Direset!',
            'text' => 'Semua kolom input kokurikuler telah dikosongkan.',
        ]);
    }

    // 🔹 ✅ NEW: Delete kokurikuler
    public function deleteKokurikuler($pelajarId = null): void
    {
        // Handle array parameter dari JavaScript
        if (is_array($pelajarId) && isset($pelajarId[0])) {
            $pelajarId = $pelajarId[0];
        }

        if (!$pelajarId || !$this->semesterId) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'ID Pelajar tidak ditemukan atau data tidak valid.',
            ]);
            return;
        }

        try {
            // Validasi rombel
            $rombel = Rombel::find($this->rombelId);
            if (!$rombel) {
                throw new \Exception('Data rombel tidak ditemukan');
            }

            // Security: Validasi pelajar ada di rombel
            $validPelajar = RombelPelajar::where('rombel_id', $this->rombelId)
                ->where('pelajar_id', $pelajarId)
                ->exists();

            if (!$validPelajar) {
                throw new \Exception('Pelajar tidak ditemukan di rombel ini');
            }

            // Delete data kokurikuler
            $deleted = Kokurikuler::where('pelajar_id', $pelajarId)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->delete();

            if ($deleted) {
                // Reset input kokurikuler untuk pelajar yang dihapus
                if (isset($this->kokurikulerInput[$pelajarId])) {
                    $this->kokurikulerInput[$pelajarId] = [
                        'predikat' => null,
                        'capaian' => ''
                    ];
                }

                // Reload cache dan data
                $this->cachedKokurikulerExist = null;
                $this->loadKokurikulerPelajar();

                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text' => 'Data kokurikuler berhasil dihapus.',
                ]);
            } else {
                $this->dispatch('swal:info', [
                    'title' => 'Info',
                    'text' => 'Data kokurikuler tidak ditemukan.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting kokurikuler: ' . $e->getMessage(), [
                'pelajar_id' => $pelajarId,
                'semester_id' => $this->semesterId,
                'user_id' => Auth::id(),
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat menghapus data kokurikuler.',
            ]);
        }
    }

    // 🔹 Render view dengan data pelajar
    public function render()
    {
        $pelajarData = collect();

        if ($this->rombelId && $this->semesterId) {
            // Use cached kokurikuler instead of querying again
            $kokurikulerExist = $this->cachedKokurikulerExist ?? collect();

            // Query pelajar dengan pagination
            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            // Map data pelajar dengan kokurikuler mereka
            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($kokurikulerExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                // Inisialisasi input kokurikuler jika belum ada
                if (!isset($this->kokurikulerInput[$pelajarId])) {
                    $existingKokurikuler = $kokurikulerExist->get($pelajarId);
                    $this->kokurikulerInput[$pelajarId] = [
                        'predikat' => $existingKokurikuler->predikat ?? null,
                        'capaian' => $existingKokurikuler->capaian ?? '',
                    ];
                }

                $existingKokurikulerData = $kokurikulerExist->get($pelajarId);

                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id' => $pelajarId,
                    'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                    'nisn' => $rombelPelajar->pelajar->nisn,
                    'kokurikuler_existing' => $existingKokurikulerData ? (object) [
                        'id' => $existingKokurikulerData->id,
                        'predikat' => $existingKokurikulerData->predikat,
                        'capaian' => $existingKokurikulerData->capaian,
                        'tanggal_input' => $existingKokurikulerData->tanggal_input,
                    ] : null,
                ];
            });
        }

        return view('livewire.admin.input-kokurikuler', [
            'pelajarData' => $pelajarData,
        ]);
    }
}
