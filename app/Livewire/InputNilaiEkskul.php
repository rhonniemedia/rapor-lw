<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Rombel;
use App\Models\Ekstrakurikuler;
use App\Models\EkskulPelajar;
use App\Models\RombelPelajar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InputNilaiEkskul extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 📹 Properti utama
    public $rombelId;
    public $rombel;

    // 📹 Properti pencarian & pagination
    public $searchPelajar = '';
    public $perPagePelajar = 1000;

    // 📹 Data dropdown & display
    public $ekstrakurikulerList = [];
    public $selectedEkstrakurikulerId = null;
    public $selectedEkskulName = null;

    // 📹 Input data nilai & deskripsi
    public $nilaiInput = []; // Format: ['pelajar_id' => 'nilai']
    public $deskripsiInput = []; // Format: ['pelajar_id' => 'deskripsi']

    // 📹 Predikat ekskul options
    public $predikatOptions = [
        'A' => 'Sangat Baik',
        'B' => 'Baik',
        'C' => 'Cukup',
    ];

    // 📹 Query string untuk persistensi state
    protected $queryString = [
        'selectedEkstrakurikulerId' => ['except' => ''],
        'searchPelajar' => ['except' => ''],
    ];

    // 📹 Event listener
    protected $listeners = [
        'saveNilaiEkskulConfirmed' => 'saveNilaiEkskul',
        'resetNilaiEkskulConfirmed' => 'resetNilaiEkskul',
    ];

    // 📹 Validation rules
    protected $rules = [
        'nilaiInput.*' => 'nullable|string|in:A,B,C',
        'deskripsiInput.*' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'nilaiInput.*.in' => 'Nilai harus A, B, atau C',
        'deskripsiInput.*.max' => 'Deskripsi maksimal 500 karakter',
    ];

    public function mount($rombelId)
    {
        $this->rombelId = $rombelId;
        $this->rombel = Rombel::with(['tahunAjaranKurikulum.tahunAjaran', 'waliKelas'])->findOrFail($rombelId);

        $user = Auth::user();

        // Ambil roles langsung dari database
        $userRoleNames = DB::table('role_users')
            ->join('roles', 'role_users.role_id', '=', 'roles.id')
            ->where('role_users.user_id', $user->id)
            ->pluck('roles.nama_role')
            ->toArray();

        $isAdmin = in_array('admin', $userRoleNames) || in_array('superadmin', $userRoleNames);
        $isWaliKelas = $this->rombel->wali_kelas_slug === $user->slug;

        if (!$isAdmin && !$isWaliKelas) {
            abort(403, 'Anda bukan wali kelas dari rombel ini.');
        }

        $this->loadEkstrakurikuler();

        if ($this->selectedEkstrakurikulerId) {
            $this->loadNilaiEkskul();
        }
    }

    public function updatingSearchPelajar()
    {
        $this->resetPage();
    }

    public function updatedSelectedEkstrakurikulerId()
    {
        $this->resetPage();
        $this->searchPelajar = '';
        $this->loadNilaiEkskul();
    }

    private function loadEkstrakurikuler()
    {
        // Load semua ekstrakurikuler yang aktif
        $this->ekstrakurikulerList = Ekstrakurikuler::where('status', 'aktif')
            ->orderBy('nama')
            ->get();
    }

    private function loadNilaiEkskul()
    {
        if (!$this->selectedEkstrakurikulerId) {
            $this->nilaiInput = [];
            $this->deskripsiInput = [];
            $this->selectedEkskulName = null;
            return;
        }

        // Ambil data ekstrakurikuler
        $ekskul = Ekstrakurikuler::find($this->selectedEkstrakurikulerId);
        $this->selectedEkskulName = $ekskul->nama ?? 'N/A';

        // Ambil nilai yang sudah ada
        $nilaiExist = EkskulPelajar::where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombelId);
            })
            ->get()
            ->keyBy('pelajar_id');

        // Reset array input
        $this->nilaiInput = [];
        $this->deskripsiInput = [];

        // Populate input dengan data existing
        foreach ($nilaiExist as $pelajarId => $ekskulPelajar) {
            $this->nilaiInput[$pelajarId] = $ekskulPelajar->nilai;
            $this->deskripsiInput[$pelajarId] = $ekskulPelajar->deskripsi;
        }
    }

    private function getPelajarQuery()
    {
        $query = RombelPelajar::where('rombel_id', $this->rombelId)
            ->with(['pelajar']);

        if (!empty($this->searchPelajar)) {
            $query->whereHas('pelajar', function ($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->searchPelajar . '%')
                    ->orWhere('nisn', 'like', '%' . $this->searchPelajar . '%')
                    ->orWhere('nomor_induk', 'like', '%' . $this->searchPelajar . '%');
            });
        }

        return $query;
    }

    public function confirmSaveNilaiEkskul()
    {
        if (!$this->selectedEkstrakurikulerId) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Silakan pilih ekstrakurikuler terlebih dahulu.',
            ]);
            return;
        }

        $this->validate();

        $this->dispatch('swal:confirm', [
            'title' => 'Simpan Nilai Ekstrakurikuler?',
            'text' => 'Semua nilai yang diinput akan disimpan.',
            'nextEvent' => 'saveNilaiEkskulConfirmed',
        ]);
    }

    public function saveNilaiEkskul()
    {
        if (!$this->selectedEkstrakurikulerId) {
            return;
        }

        $ekskul = Ekstrakurikuler::findOrFail($this->selectedEkstrakurikulerId);

        DB::beginTransaction();
        try {
            $savedCount = 0;

            foreach ($this->nilaiInput as $pelajarId => $nilai) {
                // Skip jika nilai kosong
                if (empty($nilai)) {
                    continue;
                }

                // Validasi nilai
                if (!in_array($nilai, array_keys($this->predikatOptions))) {
                    continue;
                }

                $deskripsi = $this->deskripsiInput[$pelajarId] ?? null;

                // Cek apakah nilai sudah ada
                $ekskulPelajarExist = EkskulPelajar::where('pelajar_id', $pelajarId)
                    ->where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
                    ->first();

                if ($ekskulPelajarExist) {
                    // Update nilai yang sudah ada
                    $ekskulPelajarExist->update([
                        'nilai' => $nilai,
                        'deskripsi' => $deskripsi,
                    ]);
                } else {
                    // Buat nilai baru
                    EkskulPelajar::create([
                        'id' => Str::uuid(),
                        'ekstrakurikuler_id' => $this->selectedEkstrakurikulerId,
                        'pelajar_id' => $pelajarId,
                        'nilai' => $nilai,
                        'deskripsi' => $deskripsi,
                    ]);
                }

                $savedCount++;
            }

            DB::commit();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => "Berhasil menyimpan $savedCount nilai untuk ekstrakurikuler '{$ekskul->nama}'.",
            ]);

            $this->loadNilaiEkskul();
        } catch (\Exception $e) {
            DB::rollback();

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Gagal menyimpan nilai: ' . $e->getMessage(),
            ]);
        }
    }

    public function confirmResetNilaiEkskul()
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Reset Input Nilai?',
            'text' => 'Semua input nilai akan dikosongkan (belum disimpan).',
            'nextEvent' => 'resetNilaiEkskulConfirmed',
        ]);
    }

    public function resetNilaiEkskul()
    {
        $this->nilaiInput = array_map(fn() => null, $this->nilaiInput);
        $this->deskripsiInput = array_map(fn() => null, $this->deskripsiInput);

        $this->dispatch('swal:info', [
            'title' => 'Direset!',
            'text' => 'Semua kolom input nilai telah dikosongkan.',
        ]);
    }

    public function render()
    {
        $pelajarData = collect();

        if ($this->selectedEkstrakurikulerId) {
            // Ambil nilai yang sudah tersimpan
            $nilaiExist = EkskulPelajar::where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
                ->whereIn('pelajar_id', function ($query) {
                    $query->select('pelajar_id')
                        ->from('rombel_pelajars')
                        ->where('rombel_id', $this->rombelId);
                })
                ->get()
                ->keyBy('pelajar_id');

            // Query pelajar dengan pagination
            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            // Map data pelajar dengan nilai mereka
            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($nilaiExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                // Inisialisasi input nilai jika belum ada
                if (!isset($this->nilaiInput[$pelajarId])) {
                    $eksisting = $nilaiExist->get($pelajarId);
                    $this->nilaiInput[$pelajarId] = $eksisting->nilai ?? null;
                    $this->deskripsiInput[$pelajarId] = $eksisting->deskripsi ?? null;
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

        return view('livewire.input-nilai-ekskul', [
            'pelajarData' => $pelajarData,
        ]);
    }
}
