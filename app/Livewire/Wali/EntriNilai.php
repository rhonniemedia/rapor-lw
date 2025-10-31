<?php

namespace App\Livewire\Wali;

use App\Models\Nilai;
use App\Models\Rombel;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RombelPelajar;
use App\Models\RombelPengajar;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class EntriNilai extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Properties
    public $rombel;
    public $semesterAktif;
    public $selectedRombelPengajarId = null;
    public $mataPelajaranList = [];
    public $guruName = null;

    // Search & Pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // Input nilai
    public $nilaiInput = [];

    // Cache
    private $cachedNilaiExist = null;

    // Query string
    protected $queryString = [
        'selectedRombelPengajarId' => ['except' => null],
        'searchPelajar' => ['except' => ''],
    ];

    // Validation
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
        // Load rombel dari wali kelas yang login
        $this->loadRombelWaliKelas();

        if (!$this->rombel) {
            session()->flash('error', 'Anda tidak memiliki kelas binaan.');
            return redirect()->route('walikelas.dashboard');
        }

        // Load semester aktif
        $this->loadSemesterAktif();

        if (!$this->semesterAktif) {
            session()->flash('warning', 'Tidak ada semester aktif saat ini.');
        }

        // Load mata pelajaran
        $this->loadMataPelajaran();

        // Load nilai jika mata pelajaran sudah dipilih
        if ($this->selectedRombelPengajarId) {
            $this->loadNilaiPelajar();
        }
    }

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

    private function loadSemesterAktif(): void
    {
        $this->semesterAktif = TahunAjaranSemester::where('status', 'aktif')
            ->with(['semester', 'tahunAjaran'])
            ->first();
    }

    private function loadMataPelajaran(): void
    {
        if (!$this->rombel) {
            $this->mataPelajaranList = [];
            return;
        }

        // Load mata pelajaran yang diajarkan di rombel ini
        $this->mataPelajaranList = RombelPengajar::with([
            'mataPelajaran',
            'guru'
        ])
            ->where('rombel_id', $this->rombel->id)
            ->orderBy('mata_pelajaran_id')
            ->get();
    }

    public function updatedSelectedRombelPengajarId(): void
    {
        $this->resetPage();
        $this->searchPelajar = '';
        $this->cachedNilaiExist = null;
        $this->loadNilaiPelajar();
    }

    public function updatingSearchPelajar(): void
    {
        $this->resetPage();
    }

    private function loadNilaiPelajar(): void
    {
        if (!$this->selectedRombelPengajarId || !$this->semesterAktif) {
            $this->nilaiInput = [];
            $this->guruName = null;
            $this->cachedNilaiExist = null;
            return;
        }

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

        // Cache nilai existing
        $this->cachedNilaiExist = Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->pluck('nilai_angka', 'pelajar_id');

        // Reset dan populate nilai input
        $this->nilaiInput = [];
        foreach ($this->cachedNilaiExist as $pelajarId => $nilai) {
            $this->nilaiInput[$pelajarId] = $nilai;
        }
    }

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

    private function hitungPredikat(float $nilai): string
    {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 75) return 'B';
        if ($nilai >= 60) return 'C';
        return 'D';
    }

    public function saveNilai(): void
    {
        if (!$this->selectedRombelPengajarId || !$this->semesterAktif) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Silakan pilih mata pelajaran terlebih dahulu.',
            ]);
            return;
        }

        // Validasi input
        $this->validate();

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

        // Validasi pelajar_id yang valid
        $validPelajarIds = RombelPelajar::where('rombel_id', $this->rombel->id)
            ->pluck('pelajar_id')
            ->toArray();

        DB::beginTransaction();
        try {
            $savedCount = 0;

            foreach ($this->nilaiInput as $pelajarId => $nilai) {
                if (!in_array($pelajarId, $validPelajarIds)) {
                    continue;
                }

                if ($nilai === null || $nilai === '') {
                    continue;
                }

                $nilaiBersih = is_numeric($nilai) ? floatval($nilai) : null;

                if ($nilaiBersih === null || $nilaiBersih < 0 || $nilaiBersih > 100) {
                    continue;
                }

                $predikat = $this->hitungPredikat($nilaiBersih);

                Nilai::updateOrCreate(
                    [
                        'pelajar_id' => $pelajarId,
                        'mata_pelajaran_id' => $mataPelajaranId,
                        'rombel_pengajar_id' => $this->selectedRombelPengajarId,
                        'tahun_ajaran_semester_id' => $this->semesterAktif->id,
                    ],
                    [
                        'guru_id' => $guruId,
                        'nilai_angka' => $nilaiBersih,
                        'predikat' => $predikat,
                        'updated_by' => Auth::id(),
                        'created_by' => Auth::id(),
                    ]
                );

                $savedCount++;
            }

            DB::commit();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => "Berhasil menyimpan {$savedCount} nilai untuk mata pelajaran '{$mataPelajaran}'.",
            ]);

            $this->cachedNilaiExist = null;
            $this->loadNilaiPelajar();
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving nilai: ' . $e->getMessage(), [
                'rombel_pengajar_id' => $this->selectedRombelPengajarId,
                'semester_id' => $this->semesterAktif->id,
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Gagal menyimpan nilai. Silakan coba lagi.',
            ]);
        }
    }

    public function resetNilai(): void
    {
        $this->nilaiInput = array_map(fn() => null, $this->nilaiInput);

        $this->dispatch('swal:info', [
            'title' => 'Direset!',
            'text' => 'Semua kolom input nilai telah dikosongkan.',
        ]);
    }

    public function render()
    {
        $pelajarData = collect();
        $totalSiswa = 0;

        if ($this->rombel) {
            $totalSiswa = RombelPelajar::where('rombel_id', $this->rombel->id)->count();
        }

        if ($this->selectedRombelPengajarId && $this->semesterAktif) {
            $nilaiExist = $this->cachedNilaiExist ?? collect();

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($nilaiExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

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

        return view('livewire.wali.entri-nilai', [
            'pelajarData' => $pelajarData,
            'totalSiswa' => $totalSiswa,
        ]);
    }
}
