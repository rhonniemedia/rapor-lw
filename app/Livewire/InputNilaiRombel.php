<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Rombel;
use App\Models\RombelPengajar;
use App\Models\Nilai;
use App\Models\RombelPelajar;
use Illuminate\Support\Facades\DB;

class InputNilaiRombel extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Properti utama
    public $rombelId;
    public $rombel;

    // 🔹 Properti pencarian & pagination
    public $searchPelajar = '';
    public $perPagePelajar = '';

    // 🔹 Data dropdown & display
    public $mataPelajaranList = [];
    public $selectedRombelPengajarId = null;
    public $guruName = null;

    // 🔹 Input data nilai
    public $nilaiInput = [];

    // 🔹 Query string untuk persistensi state
    protected $queryString = [
        'selectedRombelPengajarId' => ['except' => ''],
        'searchPelajar' => ['except' => ''],
    ];

    // 🔹 Event listener
    protected $listeners = [
        'saveNilaiConfirmed' => 'saveNilai',
        'resetNilaiConfirmed' => 'resetNilai',
    ];

    public function mount($rombelId)
    {
        $this->rombelId = $rombelId;

        // Eager load relasi untuk menghindari N+1 query
        $this->rombel = Rombel::with('tahunAjaranKurikulum')->findOrFail($rombelId);

        // Load data mata pelajaran
        $this->loadMataPelajaran();

        // Set default mata pelajaran pertama jika belum ada yang dipilih
        if (!empty($this->mataPelajaranList) && !$this->selectedRombelPengajarId) {
            $this->selectedRombelPengajarId = $this->mataPelajaranList->first()->id ?? null;
        }

        // Load data pelajar jika sudah ada mata pelajaran terpilih
        if ($this->selectedRombelPengajarId) {
            $this->loadNilaiPelajar();
        }
    }

    // 🔹 Reset pagination saat search berubah
    public function updatingSearchPelajar()
    {
        $this->resetPage();
    }

    // 🔹 Handler saat mata pelajaran berubah
    public function updatedSelectedRombelPengajarId()
    {
        $this->resetPage();
        $this->searchPelajar = '';
        $this->loadNilaiPelajar();
    }

    // 🔹 Load daftar mata pelajaran yang diajar di rombel
    private function loadMataPelajaran()
    {
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
        if (!$this->selectedRombelPengajarId) {
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

        $this->dispatch('swal:confirm', [
            'title' => 'Simpan Nilai?',
            'text' => 'Semua nilai yang diinput akan disimpan.',
            'nextEvent' => 'saveNilaiConfirmed',
        ]);
    }

    // 🔹 Simpan nilai
    public function saveNilai()
    {
        if (!$this->selectedRombelPengajarId) {
            return;
        }

        $rombelPengajar = RombelPengajar::findOrFail($this->selectedRombelPengajarId);
        $mataPelajaran = $rombelPengajar->mataPelajaran->nama;
        $mataPelajaranId = $rombelPengajar->mata_pelajaran_id;
        $guruId = $rombelPengajar->guru_id;

        // Ambil tahun ajaran semester ID
        $tahunAjaranSemesterId = $this->rombel->tahun_ajaran_semester_id
            ?? $this->rombel->tahunAjaranKurikulum->tahun_ajaran_semester_id
            ?? $this->rombel->tahunAjaranKurikulum->id;

        DB::beginTransaction();
        try {
            $savedCount = 0;

            foreach ($this->nilaiInput as $pelajarId => $nilai) {
                // Validasi dan bersihkan nilai
                $nilaiBersih = is_numeric($nilai) ? (int)$nilai : null;

                if ($nilaiBersih !== null && ($nilaiBersih < 0 || $nilaiBersih > 100)) {
                    continue; // Skip nilai yang tidak valid
                }

                // Update atau create nilai
                Nilai::updateOrCreate(
                    [
                        'rombel_pengajar_id' => $this->selectedRombelPengajarId,
                        'pelajar_id' => $pelajarId,
                    ],
                    [
                        'nilai_angka' => $nilaiBersih,
                        'guru_id' => $guruId,
                        'tingkat' => $this->rombel->tingkat,
                        'semester' => $this->rombel->tahunAjaranKurikulum->semester,
                        'mata_pelajaran_id' => $mataPelajaranId,
                        'tahun_ajaran_semester_id' => $tahunAjaranSemesterId,
                    ]
                );

                if ($nilaiBersih !== null) {
                    $savedCount++;
                }
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

        if ($this->selectedRombelPengajarId) {
            // Ambil nilai yang sudah tersimpan di database
            $nilaiExist = Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
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

        return view('livewire.input-nilai-rombel', [
            'pelajarData' => $pelajarData,
        ]);
    }
}
