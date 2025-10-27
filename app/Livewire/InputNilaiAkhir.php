<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Rombel;
use App\Models\RombelPengajar;
use App\Models\Nilai;
use App\Models\RombelPelajar;
use App\Models\TahunAjaran;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
    public $perPagePelajar = 1000;

    // 🔹 Data dropdown & display
    public $tahunAjaranList = [];
    public $semesterList = [];
    public $rombelList = [];
    public $mataPelajaranList = [];
    public $selectedRombelPengajarId = null;
    public $guruName = null;

    // 🔹 Input data nilai
    public $nilaiInput = [];

    // 🔹 Query string untuk persistensi state
    protected $queryString = [
        'tahunAjaranId' => ['except' => ''],
        'semesterId' => ['except' => ''],
        'rombelId' => ['except' => ''],
        'selectedRombelPengajarId' => ['except' => ''],
        'searchPelajar' => ['except' => ''],
    ];

    // 🔹 Event listener
    protected $listeners = [
        'saveNilaiConfirmed' => 'saveNilai',
        'resetNilaiConfirmed' => 'resetNilai',
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
    private function loadTahunAjaran()
    {
        $this->tahunAjaranList = TahunAjaran::orderBy('tgl_mulai', 'desc')->get();
    }

    private function loadSemester()
    {
        if (!$this->tahunAjaranId) {
            $this->semesterList = [];
            return;
        }

        $this->semesterList = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahunAjaranId)
            ->get(); // Hapus orderBy
    }

    // // 🔹 Load data semester berdasarkan tahun ajaran
    // private function loadSemester()
    // {
    //     if (!$this->tahunAjaranId) {
    //         $this->semesterList = [];
    //         return;
    //     }

    //     $this->semesterList = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahunAjaranId)
    //         ->orderBy('urutan', 'asc')
    //         ->get();
    // }

    // 🔹 Load data rombel berdasarkan tahun ajaran
    private function loadRombel()
    {
        if (!$this->tahunAjaranId) {
            $this->rombelList = [];
            return;
        }

        $this->rombelList = Rombel::whereHas('tahunAjaranKurikulum', function ($q) {
            $q->where('tahun_ajaran_id', $this->tahunAjaranId);
        })
            ->with(['tingkat', 'jurusan'])
            ->orderBy('nama_rombel', 'asc')
            ->get();
    }

    // 🔹 Load data rombel yang dipilih
    private function loadRombelData()
    {
        if (!$this->rombelId) {
            $this->rombel = null;
            return;
        }

        $this->rombel = Rombel::with([
            'tahunAjaranKurikulum.tahunAjaran',
            'tahunAjaranKurikulum.kurikulum',
            'waliKelas',
            'tingkat',
            'jurusan'
        ])->findOrFail($this->rombelId);
    }

    // 🔹 Handler saat tahun ajaran berubah
    public function updatedTahunAjaranId()
    {
        $this->semesterId = null;
        $this->rombelId = null;
        $this->selectedRombelPengajarId = null;
        $this->semesterList = [];
        $this->rombelList = [];
        $this->mataPelajaranList = [];
        $this->nilaiInput = [];
        $this->rombel = null;

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
    public function updatedSemesterId()
    {
        $this->rombelId = null;
        $this->selectedRombelPengajarId = null;
        $this->rombelList = [];
        $this->mataPelajaranList = [];
        $this->nilaiInput = [];
        $this->rombel = null;

        $this->loadRombel();
        $this->resetPage();
    }

    // 🔹 Handler saat rombel berubah
    public function updatedRombelId()
    {
        $this->selectedRombelPengajarId = null;
        $this->mataPelajaranList = [];
        $this->nilaiInput = [];

        if ($this->rombelId) {
            $this->loadRombelData();
            $this->loadMataPelajaran();
        } else {
            $this->rombel = null;
        }

        $this->resetPage();
    }

    // 🔹 Handler saat mata pelajaran berubah
    public function updatedSelectedRombelPengajarId()
    {
        $this->resetPage();
        $this->searchPelajar = '';
        $this->loadNilaiPelajar();
    }

    // 🔹 Reset pagination saat search berubah
    public function updatingSearchPelajar()
    {
        $this->resetPage();
    }

    // 🔹 Load daftar mata pelajaran yang diajar di rombel
    private function loadMataPelajaran()
    {
        if (!$this->rombelId) {
            $this->mataPelajaranList = [];
            return;
        }

        $query = RombelPengajar::with(['mataPelajaran', 'guru'])
            ->where('rombel_id', $this->rombelId);

        // Otorisasi bisa ditambahkan di sini
        // if (!Auth::user()->hasRole(['admin', 'kurikulum'])) { 
        //     $query->where('guru_id', Auth::id());
        // }

        $this->mataPelajaranList = $query->orderBy('mata_pelajaran_id')->get();
    }

    // 🔹 Load data pelajar dan nilai mereka
    private function loadNilaiPelajar()
    {
        if (!$this->selectedRombelPengajarId || !$this->semesterId) {
            $this->nilaiInput = [];
            $this->guruName = null;
            return;
        }

        // Ambil data rombel pengajar dengan relasi
        $rombelPengajar = RombelPengajar::with('guru', 'mataPelajaran')
            ->findOrFail($this->selectedRombelPengajarId);

        $this->guruName = $rombelPengajar->guru->name ?? 'N/A';

        // Ambil nilai yang sudah ada
        $nilaiExist = Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->pluck('nilai_angka', 'pelajar_id');

        // Reset array input nilai
        $this->nilaiInput = [];

        // Populate nilai input dengan data existing
        foreach ($nilaiExist as $pelajarId => $nilai) {
            $this->nilaiInput[$pelajarId] = $nilai;
        }
    }

    // 🔹 Get query data pelajar dengan filter
    private function getPelajarQuery()
    {
        if (!$this->rombelId) {
            return RombelPelajar::whereNull('id'); // Return empty query
        }

        $query = RombelPelajar::where('rombel_id', $this->rombelId)
            ->with(['pelajar']);

        // Filter pencarian
        if (!empty($this->searchPelajar)) {
            $query->whereHas('pelajar', function ($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->searchPelajar . '%')
                    ->orWhere('nisn', 'like', '%' . $this->searchPelajar . '%')
                    ->orWhere('nomor_induk', 'like', '%' . $this->searchPelajar . '%');
            });
        }

        return $query;
    }

    // 🔹 Konfirmasi simpan nilai
    public function confirmSaveNilai()
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
    private function hitungPredikat($nilai)
    {
        if ($nilai >= 90) {
            return 'A';
        } elseif ($nilai >= 75) {
            return 'B';
        } elseif ($nilai >= 60) {
            return 'C';
        } else {
            return 'D';
        }
    }

    // 🔹 Simpan nilai
    public function saveNilai()
    {
        if (!$this->selectedRombelPengajarId || !$this->semesterId) {
            return;
        }

        // Ambil data rombel pengajar
        $rombelPengajar = RombelPengajar::with('mataPelajaran')->findOrFail($this->selectedRombelPengajarId);
        $mataPelajaran = $rombelPengajar->mataPelajaran->nama;
        $mataPelajaranId = $rombelPengajar->mata_pelajaran_id;
        $guruId = $rombelPengajar->guru_id;

        DB::beginTransaction();
        try {
            $savedCount = 0;

            foreach ($this->nilaiInput as $pelajarId => $nilai) {
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

                // Cek apakah nilai sudah ada
                $nilaiExist = Nilai::where('pelajar_id', $pelajarId)
                    ->where('mata_pelajaran_id', $mataPelajaranId)
                    ->where('rombel_pengajar_id', $this->selectedRombelPengajarId)
                    ->where('tahun_ajaran_semester_id', $this->semesterId)
                    ->first();

                if ($nilaiExist) {
                    // Update nilai yang sudah ada
                    $nilaiExist->update([
                        'nilai_angka' => $nilaiBersih,
                        'predikat' => $predikat,
                        'updated_by' => Auth::id(),
                    ]);
                } else {
                    // Buat nilai baru
                    Nilai::create([
                        'id' => Str::uuid(),
                        'pelajar_id' => $pelajarId,
                        'mata_pelajaran_id' => $mataPelajaranId,
                        'rombel_pengajar_id' => $this->selectedRombelPengajarId,
                        'tahun_ajaran_semester_id' => $this->semesterId,
                        'guru_id' => $guruId,
                        'nilai_angka' => $nilaiBersih,
                        'predikat' => $predikat,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }

                $savedCount++;
            }

            DB::commit();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => "Berhasil menyimpan $savedCount nilai untuk mata pelajaran '$mataPelajaran'.",
            ]);

            // Reload data pelajar untuk menampilkan nilai terbaru
            $this->loadNilaiPelajar();
        } catch (\Exception $e) {
            DB::rollback();

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Gagal menyimpan nilai: ' . $e->getMessage(),
            ]);
        }
    }

    // 🔹 Konfirmasi reset nilai
    public function confirmResetNilai()
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Reset Input Nilai?',
            'text' => 'Semua input nilai akan dikosongkan (belum disimpan).',
            'nextEvent' => 'resetNilaiConfirmed',
        ]);
    }

    // 🔹 Reset semua input nilai
    public function resetNilai()
    {
        $this->nilaiInput = array_map(fn() => null, $this->nilaiInput);

        $this->dispatch('swal:info', [
            'title' => 'Direset!',
            'text' => 'Semua kolom input nilai telah dikosongkan.',
        ]);
    }

    // 🔹 Render view dengan data pelajar
    public function render()
    {
        $pelajarData = collect();

        if ($this->selectedRombelPengajarId && $this->semesterId) {
            // Ambil nilai yang sudah tersimpan di database
            $nilaiExist = Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->pluck('nilai_angka', 'pelajar_id');

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

        return view('livewire.input-nilai-akhir', [
            'pelajarData' => $pelajarData,
        ]);
    }
}
