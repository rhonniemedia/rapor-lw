<?php

namespace App\Livewire\Admin;

use App\Models\Nilai;
use App\Models\Rombel;
use Livewire\Component;
use App\Models\TahunAjaran;
use Livewire\WithPagination;
use App\Models\RombelPelajar;
use App\Models\RombelPengajar;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class InputNilaiAkhir extends Component
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
    public $perPagePelajar = 50; // ✅ Reduced from 1000

    // 🔹 Data dropdown & display
    public $tahunAjaranList = [];
    public $semesterList = [];
    public $rombelList = [];
    public $mataPelajaranList = [];
    public $selectedRombelPengajarId = null;
    public $guruName = null;

    // 🔹 Input data nilai
    public $nilaiInput = [];

    // 🔹 Cache untuk optimasi
    private $cachedNilaiExist = null;

    // 🔹 Query string untuk persistensi state
    protected $queryString = [
        'tahunAjaranId' => ['except' => null], // ✅ Fixed
        'semesterId' => ['except' => null], // ✅ Fixed
        'rombelId' => ['except' => null], // ✅ Fixed
        'selectedRombelPengajarId' => ['except' => null], // ✅ Fixed
        'searchPelajar' => ['except' => ''],
    ];

    // 🔹 Event listener
    protected $listeners = [
        'saveNilaiConfirmed' => 'saveNilai',
        'resetNilaiConfirmed' => 'resetNilai',
        'deleteNilai' => 'deleteNilai', // ✅ NEW
    ];

    // 🔹 Validation rules
    protected $rules = [
        'nilaiInput.*' => 'nullable|numeric|min:0|max:100',
    ];

    protected $messages = [
        'nilaiInput.*.numeric' => 'Nilai harus berupa angka',
        'nilaiInput.*.min' => 'Nilai minimal adalah 0',
        'nilaiInput.*.max' => 'Nilai maksimal adalah 100',
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

        // Load mata pelajaran jika rombel sudah dipilih
        if ($this->rombelId) {
            $this->loadRombelData();
            $this->loadMataPelajaran();
        }

        // Load data nilai jika mata pelajaran sudah terpilih
        if ($this->selectedRombelPengajarId) {
            $this->loadNilaiPelajar();
        }
    }

    // 🔹 Load data tahun ajaran
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
            return;
        }

        $this->rombel = Rombel::with([
            'tahunAjaranKurikulum.tahunAjaran',
            'tahunAjaranKurikulum.kurikulum',
            'waliKelas',
            'jurusan'
        ])->find($this->rombelId); // ✅ Changed from findOrFail

        // ✅ Handle jika rombel tidak ditemukan
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
        $this->selectedRombelPengajarId = null;
        $this->rombelList = [];
        $this->mataPelajaranList = [];
        $this->nilaiInput = [];
        $this->rombel = null;
        $this->cachedNilaiExist = null; // ✅ Clear cache

        $this->loadRombel();
        $this->resetPage();
    }

    // 🔹 Handler saat rombel berubah
    public function updatedRombelId(): void
    {
        $this->selectedRombelPengajarId = null;
        $this->mataPelajaranList = [];
        $this->nilaiInput = [];
        $this->cachedNilaiExist = null; // ✅ Clear cache

        if ($this->rombelId) {
            $this->loadRombelData();
            $this->loadMataPelajaran();
        } else {
            $this->rombel = null;
        }

        $this->resetPage();
    }

    // 🔹 Handler saat mata pelajaran berubah
    public function updatedSelectedRombelPengajarId(): void
    {
        $this->resetPage();
        $this->searchPelajar = '';
        $this->cachedNilaiExist = null; // ✅ Clear cache
        $this->loadNilaiPelajar();
    }

    // 🔹 Reset pagination saat search berubah
    public function updatingSearchPelajar(): void
    {
        $this->resetPage();
    }

    // ✅ Helper method untuk reset filters
    private function resetFilters(): void
    {
        $this->semesterId = null;
        $this->rombelId = null;
        $this->selectedRombelPengajarId = null;
        $this->semesterList = [];
        $this->rombelList = [];
        $this->mataPelajaranList = [];
        $this->nilaiInput = [];
        $this->rombel = null;
        $this->cachedNilaiExist = null;
    }

    // 🔹 Load daftar mata pelajaran yang diajar di rombel
    private function loadMataPelajaran(): void
    {
        if (!$this->rombelId) {
            $this->mataPelajaranList = [];
            return;
        }

        $query = RombelPengajar::with(['mataPelajaran', 'guru'])
            ->where('rombel_id', $this->rombelId);

        // ✅ Otorisasi (uncomment jika diperlukan)
        // if (!Auth::user()->hasAnyRole(['admin', 'kurikulum'])) { 
        //     $query->where('guru_id', Auth::id());
        // }

        $this->mataPelajaranList = $query->orderBy('mata_pelajaran_id')->get();
    }

    // 🔹 Load data pelajar dan nilai mereka
    private function loadNilaiPelajar(): void
    {
        if (!$this->selectedRombelPengajarId || !$this->semesterId) {
            $this->nilaiInput = [];
            $this->guruName = null;
            $this->cachedNilaiExist = null;
            return;
        }

        // ✅ Improved: Use find instead of findOrFail
        $rombelPengajar = RombelPengajar::with('guru', 'mataPelajaran')
            ->find($this->selectedRombelPengajarId);

        if (!$rombelPengajar) {
            $this->nilaiInput = [];
            $this->guruName = null;
            $this->cachedNilaiExist = null;
            $this->selectedRombelPengajarId = null;
            return;
        }

        $this->guruName = $rombelPengajar->guru->name ?? 'N/A';

        // ✅ Cache nilai untuk menghindari query duplikat
        $this->cachedNilaiExist = Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->pluck('nilai_angka', 'pelajar_id');

        // Reset array input nilai
        $this->nilaiInput = [];

        // Populate nilai input dengan data existing
        foreach ($this->cachedNilaiExist as $pelajarId => $nilai) {
            $this->nilaiInput[$pelajarId] = $nilai;
        }
    }

    // 🔹 Get query data pelajar dengan filter
    private function getPelajarQuery(): Builder
    {
        if (!$this->rombelId) {
            return RombelPelajar::whereNull('id'); // Return empty query
        }

        $query = RombelPelajar::where('rombel_id', $this->rombelId)
            ->with(['pelajar']); // ✅ Ensure eager loading

        // Filter pencarian
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

    // 🔹 Konfirmasi simpan nilai
    public function confirmSaveNilai(): void
    {
        if (!$this->selectedRombelPengajarId) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Silakan pilih mata pelajaran terlebih dahulu.',
            ]);
            return;
        }

        // Validasi input
        $this->validate();

        $this->dispatch('swal:confirm', [
            'title' => 'Simpan Nilai?',
            'text' => 'Semua nilai yang diinput akan disimpan.',
            'nextEvent' => 'saveNilaiConfirmed',
        ]);
    }

    // 🔹 Hitung predikat berdasarkan nilai
    private function hitungPredikat(float $nilai): string
    {
        if ($nilai >= 91) {
            return 'A'; // Sangat Baik
        } elseif ($nilai >= 83) {
            return 'B'; // Baik
        } elseif ($nilai >= 75) {
            return 'C'; // Cukup / Tuntas minimal
        } else {
            return 'D'; // Kurang / Belum tuntas
        }
    }

    // 🔹 Simpan nilai
    public function saveNilai(): void
    {
        if (!$this->selectedRombelPengajarId || !$this->semesterId) {
            return;
        }

        // Ambil data rombel pengajar
        $rombelPengajar = RombelPengajar::with('mataPelajaran')
            ->find($this->selectedRombelPengajarId);

        if (!$rombelPengajar) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Data mata pelajaran tidak ditemukan.',
            ]);
            return;
        }

        $mataPelajaran = $rombelPengajar->mataPelajaran->nama;
        $mataPelajaranId = $rombelPengajar->mata_pelajaran_id;
        $guruId = $rombelPengajar->guru_id;

        // ✅ Security: Validasi pelajar_id yang valid
        $validPelajarIds = RombelPelajar::where('rombel_id', $this->rombelId)
            ->pluck('pelajar_id')
            ->toArray();

        DB::beginTransaction();
        try {
            $savedCount = 0;

            foreach ($this->nilaiInput as $pelajarId => $nilai) {
                // ✅ Security check: pastikan pelajar ada di rombel ini
                if (!in_array($pelajarId, $validPelajarIds)) {
                    continue;
                }

                // Skip jika nilai kosong atau null
                if ($nilai === null || $nilai === '') {
                    continue;
                }

                // Validasi dan bersihkan nilai
                $nilaiBersih = is_numeric($nilai) ? floatval($nilai) : null;

                if ($nilaiBersih === null || $nilaiBersih < 0 || $nilaiBersih > 100) {
                    continue; // Skip nilai yang tidak valid
                }

                // Hitung predikat
                $predikat = $this->hitungPredikat($nilaiBersih);

                // ✅ Use updateOrCreate for cleaner code
                Nilai::updateOrCreate(
                    [
                        'pelajar_id' => $pelajarId,
                        'mata_pelajaran_id' => $mataPelajaranId,
                        'rombel_pengajar_id' => $this->selectedRombelPengajarId,
                        'tahun_ajaran_semester_id' => $this->semesterId,
                    ],
                    [
                        'guru_id' => $guruId,
                        'nilai_angka' => $nilaiBersih,
                        'predikat' => $predikat,
                        'updated_by' => Auth::id(),
                        'created_by' => Auth::id(), // Only set on creation
                    ]
                );

                $savedCount++;
            }

            DB::commit();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => "Berhasil menyimpan {$savedCount} nilai untuk mata pelajaran '{$mataPelajaran}'.",
            ]);

            // ✅ Clear cache dan reload data pelajar
            $this->cachedNilaiExist = null;
            $this->loadNilaiPelajar();
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving nilai: ' . $e->getMessage(), [
                'rombel_pengajar_id' => $this->selectedRombelPengajarId,
                'semester_id' => $this->semesterId,
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Gagal menyimpan nilai. Silakan coba lagi.',
            ]);
        }
    }

    // 🔹 Konfirmasi reset nilai
    public function confirmResetNilai(): void
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Reset Input Nilai?',
            'text' => 'Semua input nilai akan dikosongkan (belum disimpan).',
            'nextEvent' => 'resetNilaiConfirmed',
        ]);
    }

    // 🔹 Reset semua input nilai
    public function resetNilai(): void
    {
        $this->nilaiInput = array_map(fn() => null, $this->nilaiInput);

        $this->dispatch('swal:info', [
            'title' => 'Direset!',
            'text' => 'Semua kolom input nilai telah dikosongkan.',
        ]);
    }

    // 🔹 NEW: Delete nilai
    public function deleteNilai($pelajarId = null): void
    {
        // Handle array parameter dari JavaScript
        if (is_array($pelajarId) && isset($pelajarId[0])) {
            $pelajarId = $pelajarId[0];
        }

        if (!$pelajarId || !$this->selectedRombelPengajarId || !$this->semesterId) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'ID Pelajar tidak ditemukan atau data tidak valid.',
            ]);
            return;
        }

        try {
            $rombelPengajar = RombelPengajar::find($this->selectedRombelPengajarId);

            if (!$rombelPengajar) {
                throw new \Exception('Data mata pelajaran tidak ditemukan');
            }

            $deleted = Nilai::where('pelajar_id', $pelajarId)
                ->where('mata_pelajaran_id', $rombelPengajar->mata_pelajaran_id)
                ->where('rombel_pengajar_id', $this->selectedRombelPengajarId)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->delete();

            if ($deleted) {
                // Reset input nilai untuk pelajar yang dihapus
                if (isset($this->nilaiInput[$pelajarId])) {
                    unset($this->nilaiInput[$pelajarId]);
                }

                // Reload cache dan data
                $this->cachedNilaiExist = null;
                $this->loadNilaiPelajar();

                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text' => 'Nilai berhasil dihapus.',
                ]);
            } else {
                $this->dispatch('swal:info', [
                    'title' => 'Info',
                    'text' => 'Nilai tidak ditemukan.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting nilai: ' . $e->getMessage(), [
                'pelajar_id' => $pelajarId,
                'user_id' => Auth::id(),
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat menghapus nilai.',
            ]);
        }
    }

    // 🔹 Render view dengan data pelajar
    public function render()
    {
        $pelajarData = collect();

        if ($this->selectedRombelPengajarId && $this->semesterId) {
            // ✅ Use cached nilai instead of querying again
            $nilaiExist = $this->cachedNilaiExist ?? collect();

            // Query pelajar dengan pagination
            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            // Map data pelajar dengan nilai mereka
            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($nilaiExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                // Inisialisasi input nilai jika belum ada
                if (!isset($this->nilaiInput[$pelajarId])) {
                    $this->nilaiInput[$pelajarId] = $nilaiExist->get($pelajarId);
                }

                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id' => $pelajarId,
                    'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                    'nisn' => $rombelPelajar->pelajar->nisn,
                    'nilai_sekarang' => $nilaiExist->get($pelajarId),
                ];
            });
        }

        return view('livewire.admin.input-nilai-akhir', [
            'pelajarData' => $pelajarData,
        ]);
    }
}
