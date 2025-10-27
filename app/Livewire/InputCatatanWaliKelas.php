<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Rombel;
use App\Models\CatatanWaliKelas;
use App\Models\RombelPelajar;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InputCatatanWaliKelas extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 📹 Properti utama
    public $rombelId;
    public $rombel;

    // 📹 Properti pencarian & pagination
    public $searchPelajar = '';
    public $perPagePelajar = 1000;

    // 📹 Input data catatan
    public $catatanInput = []; // Format: ['pelajar_id' => ['jenis_catatan' => '', 'catatan' => '']]

    // 📹 Jenis catatan options
    public $jenisCatatanOptions = [
        'sikap' => 'Sikap & Perilaku',
        'prestasi' => 'Prestasi',
        'kedisiplinan' => 'Kedisiplinan',
        'sosial' => 'Sosial & Pergaulan',
        'akademik' => 'Akademik',
        'lainnya' => 'Lainnya',
    ];

    // 📹 Query string untuk persistensi state
    protected $queryString = [
        'searchPelajar' => ['except' => ''],
    ];

    // 📹 Event listener
    protected $listeners = [
        'saveCatatanConfirmed' => 'saveCatatan',
        'resetCatatanConfirmed' => 'resetCatatan',
    ];

    // 📹 Validation rules
    protected $rules = [
        'catatanInput.*.jenis_catatan' => 'nullable|string|in:sikap,prestasi,kedisiplinan,sosial,akademik,lainnya',
        'catatanInput.*.catatan' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'catatanInput.*.jenis_catatan.in' => 'Jenis catatan tidak valid',
        'catatanInput.*.catatan.max' => 'Catatan maksimal 1000 karakter',
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

        $this->loadCatatanPelajar();
    }

    public function updatingSearchPelajar()
    {
        $this->resetPage();
    }

    private function loadCatatanPelajar()
    {
        $tahunAjaranSemesterId = $this->getTahunAjaranSemesterId();
        $guruId = $this->getGuruIdForQuery(); // 👈 Gunakan helper

        // Ambil catatan terakhir untuk setiap pelajar
        $catatanExist = CatatanWaliKelas::where('tahun_ajaran_semester_id', $tahunAjaranSemesterId)
            ->where('guru_id', $guruId) // 👈 Gunakan $guruId yang benar
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombelId);
            })
            ->orderBy('tanggal_input', 'desc')
            ->get()
            ->groupBy('pelajar_id')
            ->map(function ($group) {
                return $group->first(); // Ambil catatan terbaru
            });

        $this->catatanInput = [];

        // Populate catatan input dengan data existing
        foreach ($catatanExist as $pelajarId => $catatan) {
            $this->catatanInput[$pelajarId] = [
                'jenis_catatan' => $catatan->jenis_catatan ?? '',
                'catatan' => $catatan->catatan ?? '',
            ];
        }
    }

    private function getTahunAjaranSemesterId()
    {
        $sessionSemesterId = session('tahun_ajaran_semester_id');

        if ($sessionSemesterId) {
            return $sessionSemesterId;
        }

        $activeSemester = TahunAjaranSemester::where('status', 'aktif')->first();

        if ($activeSemester) {
            return $activeSemester->id;
        }

        if ($this->rombel && $this->rombel->tahunAjaranKurikulum) {
            $semester = TahunAjaranSemester::where('tahun_ajaran_id', $this->rombel->tahunAjaranKurikulum->tahun_ajaran_id)
                ->first();

            if ($semester) {
                return $semester->id;
            }
        }

        return null;
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

    private function getGuruIdForQuery()
    {
        $user = Auth::user();

        // Ambil roles langsung dari database
        $userRoleNames = DB::table('role_users')
            ->join('roles', 'role_users.role_id', '=', 'roles.id')
            ->where('role_users.user_id', $user->id)
            ->pluck('roles.nama_role')
            ->toArray();

        $isAdmin = in_array('admin', $userRoleNames) || in_array('superadmin', $userRoleNames);

        // Jika admin, gunakan ID wali kelas; jika bukan, gunakan ID user
        if ($isAdmin && $this->rombel->waliKelas) {
            return $this->rombel->waliKelas->id;
        }

        return $user->id;
    }

    public function confirmSaveCatatan()
    {
        $this->validate();

        $this->dispatch('swal:confirm', [
            'title' => 'Simpan Catatan?',
            'text' => 'Semua catatan yang diinput akan disimpan.',
            'nextEvent' => 'saveCatatanConfirmed',
        ]);
    }

    public function saveCatatan()
    {
        $tahunAjaranSemesterId = $this->getTahunAjaranSemesterId();

        if (!$tahunAjaranSemesterId) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Tidak dapat menemukan tahun ajaran semester yang aktif.',
            ]);
            return;
        }

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
            $this->dispatch('swal:error', [
                'title' => 'Akses Ditolak!',
                'text' => 'Anda bukan wali kelas dari rombel ini.',
            ]);
            return;
        }

        // Tentukan guru_id
        if ($isAdmin) {
            // Validasi wali kelas ada
            if (!$this->rombel->waliKelas) {
                $this->dispatch('swal:error', [
                    'title' => 'Wali Kelas Belum Ditentukan!',
                    'text' => 'Silakan tetapkan wali kelas untuk rombel ini terlebih dahulu.',
                ]);
                return;
            }
            $guruId = $this->rombel->waliKelas->id;
        } else {
            $guruId = $user->id;
        }

        DB::beginTransaction();
        try {
            $savedCount = 0;

            foreach ($this->catatanInput as $pelajarId => $catatan) {
                if (empty($catatan['catatan']) || empty($catatan['jenis_catatan'])) {
                    continue;
                }

                if (!in_array($catatan['jenis_catatan'], array_keys($this->jenisCatatanOptions))) {
                    continue;
                }

                CatatanWaliKelas::create([
                    'id' => Str::uuid(),
                    'pelajar_id' => $pelajarId,
                    'guru_id' => $guruId,
                    'tahun_ajaran_semester_id' => $tahunAjaranSemesterId,
                    'jenis_catatan' => $catatan['jenis_catatan'],
                    'catatan' => $catatan['catatan'],
                    'tanggal_input' => Carbon::now(),
                ]);

                $savedCount++;
            }

            DB::commit();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => "Berhasil menyimpan $savedCount catatan wali kelas.",
            ]);

            $this->loadCatatanPelajar();
        } catch (\Exception $e) {
            DB::rollback();

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Gagal menyimpan catatan: ' . $e->getMessage(),
            ]);
        }
    }

    public function confirmResetCatatan()
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Reset Input Catatan?',
            'text' => 'Semua input catatan akan dikosongkan (belum disimpan).',
            'nextEvent' => 'resetCatatanConfirmed',
        ]);
    }

    public function resetCatatan()
    {
        foreach ($this->catatanInput as $pelajarId => $data) {
            $this->catatanInput[$pelajarId] = [
                'jenis_catatan' => '',
                'catatan' => '',
            ];
        }

        $this->dispatch('swal:info', [
            'title' => 'Direset!',
            'text' => 'Semua kolom input catatan telah dikosongkan.',
        ]);
    }

    public function render()
    {
        $tahunAjaranSemesterId = $this->getTahunAjaranSemesterId();
        $guruId = $this->getGuruIdForQuery(); // 👈 Gunakan helper

        // Ambil catatan terakhir untuk setiap pelajar
        $catatanExist = CatatanWaliKelas::where('tahun_ajaran_semester_id', $tahunAjaranSemesterId)
            ->where('guru_id', $guruId) // 👈 Gunakan $guruId yang benar
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombelId);
            })
            ->orderBy('tanggal_input', 'desc')
            ->get()
            ->groupBy('pelajar_id')
            ->map(function ($group) {
                return $group->first();
            });

        $pelajarPaginated = $this->getPelajarQuery()
            ->orderBy('id', 'asc')
            ->paginate($this->perPagePelajar);

        $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($catatanExist) {
            $pelajarId = $rombelPelajar->pelajar_id;

            // Inisialisasi input catatan jika belum ada
            if (!isset($this->catatanInput[$pelajarId])) {
                $existingCatatan = $catatanExist->get($pelajarId);
                $this->catatanInput[$pelajarId] = [
                    'jenis_catatan' => $existingCatatan->jenis_catatan ?? '',
                    'catatan' => $existingCatatan->catatan ?? '',
                ];
            }

            return (object) [
                'rombel_pelajar_id' => $rombelPelajar->id,
                'pelajar_id' => $pelajarId,
                'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                'nisn' => $rombelPelajar->pelajar->nisn,
                'catatan_terakhir' => $catatanExist->get($pelajarId),
            ];
        });

        return view('livewire.input-catatan-wali-kelas', [
            'pelajarData' => $pelajarData,
        ]);
    }
}
