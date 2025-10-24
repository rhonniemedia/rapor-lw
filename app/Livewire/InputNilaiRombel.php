<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Rombel;
use App\Models\RombelPengajar;
use App\Models\Nilai;
use App\Models\RombelPelajar;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InputNilaiRombel extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Properti utama
    public $rombelId;
    public $rombel;

    // 🔹 Properti pencarian & pagination
    public $searchPelajar = '';
    public $perPagePelajar = 1000; // Tampilkan semua (max 1000 untuk safety)

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

    // 🔹 Validation rules
    protected $rules = [
        'nilaiInput.*' => 'nullable|numeric|min:0|max:100',
    ];

    protected $messages = [
        'nilaiInput.*.numeric' => 'Nilai harus berupa angka',
        'nilaiInput.*.min' => 'Nilai minimal adalah 0',
        'nilaiInput.*.max' => 'Nilai maksimal adalah 100',
    ];

    public function mount($rombelId)
    {
        $this->rombelId = $rombelId;

        // Eager load relasi untuk menghindari N+1 query
        $this->rombel = Rombel::with(['tahunAjaranKurikulum.tahunAjaran'])->findOrFail($rombelId);

        // Load data mata pelajaran
        $this->loadMataPelajaran();

        // JANGAN set default mata pelajaran, biarkan kosong
        // User harus memilih mata pelajaran secara manual

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

        // Ambil tahun ajaran semester aktif
        $tahunAjaranSemesterId = $this->getTahunAjaranSemesterId();

        // Ambil nilai yang sudah ada
        $nilaiExist = Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
            ->where('tahun_ajaran_semester_id', $tahunAjaranSemesterId)
            ->pluck('nilai_angka', 'pelajar_id');

        // Reset array input nilai
        $this->nilaiInput = [];

        // Populate nilai input dengan data existing
        foreach ($nilaiExist as $pelajarId => $nilai) {
            $this->nilaiInput[$pelajarId] = $nilai;
        }
    }

    // 🔹 Get tahun ajaran semester ID
    private function getTahunAjaranSemesterId()
    {
        // Cek dari session terlebih dahulu
        $sessionSemesterId = session('tahun_ajaran_semester_id');

        if ($sessionSemesterId) {
            return $sessionSemesterId;
        }

        // Jika tidak ada di session, ambil yang aktif
        $activeSemester = TahunAjaranSemester::where('status', 'aktif')->first();

        if ($activeSemester) {
            return $activeSemester->id;
        }

        // Fallback: cari berdasarkan tahun ajaran dari rombel
        if ($this->rombel && $this->rombel->tahunAjaranKurikulum) {
            $semester = TahunAjaranSemester::where('tahun_ajaran_id', $this->rombel->tahunAjaranKurikulum->tahun_ajaran_id)
                ->first();

            if ($semester) {
                return $semester->id;
            }
        }

        return null;
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
        if (!$this->selectedRombelPengajarId) {
            return;
        }

        // Ambil data rombel pengajar
        $rombelPengajar = RombelPengajar::with('mataPelajaran')->findOrFail($this->selectedRombelPengajarId);
        $mataPelajaran = $rombelPengajar->mataPelajaran->nama;
        $mataPelajaranId = $rombelPengajar->mata_pelajaran_id;
        $guruId = $rombelPengajar->guru_id;

        // Ambil tahun ajaran semester ID
        $tahunAjaranSemesterId = $this->getTahunAjaranSemesterId();

        if (!$tahunAjaranSemesterId) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Tidak dapat menemukan tahun ajaran semester yang aktif.',
            ]);
            return;
        }

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
                    ->where('tahun_ajaran_semester_id', $tahunAjaranSemesterId)
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
                        'tahun_ajaran_semester_id' => $tahunAjaranSemesterId,
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

        if ($this->selectedRombelPengajarId) {
            // Ambil tahun ajaran semester aktif
            $tahunAjaranSemesterId = $this->getTahunAjaranSemesterId();

            // Ambil nilai yang sudah tersimpan di database
            $nilaiExist = Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
                ->where('tahun_ajaran_semester_id', $tahunAjaranSemesterId)
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
