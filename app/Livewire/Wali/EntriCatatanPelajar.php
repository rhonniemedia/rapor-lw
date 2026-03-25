<?php

namespace App\Livewire\Wali;

use App\Models\Rombel;
use Livewire\Component;
use App\Models\Pelajar;
use Livewire\WithPagination;
use App\Models\CatatanPelajar;
use App\Models\CatatanWaliKelas;
use App\Models\RombelPelajar;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class EntriCatatanPelajar extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Properties
    public $rombel;
    public $semesterAktif;

    // Search & Pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // Input catatan
    public $catatanInput = [];

    // Cache
    private $cachedCatatanExist = null;

    // Query string
    protected $queryString = [
        'searchPelajar' => ['except' => ''],
    ];

    // Validation
    protected $rules = [
        'catatanInput.*' => 'nullable|string|max:5000',
    ];

    protected $messages = [
        'catatanInput.*.string' => 'Catatan harus berupa teks',
        'catatanInput.*.max' => 'Catatan maksimal 5000 karakter',
    ];

    public function mount()
    {
        $this->loadRombelWaliKelas();

        if (!$this->rombel) {
            session()->flash('error', 'Anda tidak memiliki kelas binaan.');
            return redirect()->route('walikelas.dashboard');
        }

        $this->loadSemesterAktif();

        if (!$this->semesterAktif) {
            session()->flash('warning', 'Tidak ada semester aktif saat ini.');
        }

        $this->loadCatatanPelajar();
    }

    protected $listeners = ['deleteCatatan'];

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

    public function updatingSearchPelajar(): void
    {
        $this->resetPage();
    }

    private function loadCatatanPelajar(): void
    {
        if (!$this->semesterAktif) {
            $this->catatanInput = [];
            $this->cachedCatatanExist = null;
            return;
        }

        $userId = Auth::id();

        // Reload cache dari database - ambil catatan terbaru per pelajar
        $catatanData = CatatanWaliKelas::where('guru_id', $userId)
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->orderBy('tanggal_input', 'desc')
            ->get()
            ->groupBy('pelajar_id')
            ->map(function ($group) {
                return $group->first(); // Ambil catatan terbaru
            });

        $this->cachedCatatanExist = $catatanData;
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

    public function saveCatatan(): void
    {
        if (!$this->semesterAktif || !$this->rombel) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Tidak ada semester aktif atau kelas binaan.',
            ]);
            return;
        }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            $this->dispatch('swal:error', [
                'title' => 'Validasi Gagal!',
                'text' => 'Periksa input Anda. ' . implode(', ', array_map(fn($err) => implode(', ', $err), $e->errors())),
            ]);
            return;
        }

        $validPelajarIds = RombelPelajar::where('rombel_id', $this->rombel->id)
            ->pluck('pelajar_id')
            ->toArray();

        DB::beginTransaction();
        try {
            $savedCount = 0;
            $userId = Auth::id();
            $tanggalInput = now();

            foreach ($this->catatanInput as $pelajarId => $catatan) {
                if (!in_array($pelajarId, $validPelajarIds)) {
                    continue;
                }

                // Jika catatan kosong, skip
                if (empty($catatan)) {
                    continue;
                }

                try {
                    // Cari catatan yang sudah ada untuk di-update
                    $existingCatatan = CatatanWaliKelas::where([
                        'pelajar_id' => $pelajarId,
                        'guru_id' => $userId,
                        'tahun_ajaran_semester_id' => $this->semesterAktif->id,
                    ])->lockForUpdate()->first();

                    $dataToSave = [
                        'catatan' => $catatan,
                        'tanggal_input' => $tanggalInput,
                        'updated_by' => $userId,
                    ];

                    if ($existingCatatan) {
                        // Update catatan yang sudah ada
                        $existingCatatan->update($dataToSave);
                    } else {
                        // Buat catatan baru jika belum ada
                        $dataToSave['pelajar_id'] = $pelajarId;
                        $dataToSave['guru_id'] = $userId;
                        $dataToSave['tahun_ajaran_semester_id'] = $this->semesterAktif->id;
                        $dataToSave['created_by'] = $userId;

                        CatatanWaliKelas::create($dataToSave);
                    }

                    $savedCount++;
                } catch (\Exception $e) {
                    Log::error('Error saving individual catatan', [
                        'pelajar_id' => $pelajarId,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }

            DB::commit();

            $this->catatanInput = [];
            $this->cachedCatatanExist = null;
            $this->loadCatatanPelajar();

            if ($savedCount > 0) {
                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text' => "Berhasil menyimpan {$savedCount} catatan pelajar.",
                ]);
            } else {
                $this->dispatch('swal:info', [
                    'title' => 'Info',
                    'text' => 'Tidak ada catatan baru yang disimpan.',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving catatan', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'semester_id' => $this->semesterAktif->id,
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ]);
        }
    }

    public function resetCatatan(): void
    {
        if ($this->cachedCatatanExist === null) {
            $this->loadCatatanPelajar();
        }

        foreach ($this->catatanInput as $pelajarId => $catatan) {
            if ($this->cachedCatatanExist && $this->cachedCatatanExist->has($pelajarId)) {
                $this->catatanInput[$pelajarId] = $this->cachedCatatanExist->get($pelajarId)->catatan;
            } else {
                $this->catatanInput[$pelajarId] = null;
            }
        }

        $this->dispatch('swal:info', [
            'title' => 'Direset!',
            'text' => 'Input telah dikembalikan ke data tersimpan.',
        ]);
    }

    public function deleteCatatan($pelajarId = null): void
    {
        if (is_array($pelajarId) && isset($pelajarId[0])) {
            $pelajarId = $pelajarId[0];
        }

        if (!$pelajarId || !$this->semesterAktif) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'ID Pelajar tidak ditemukan atau data tidak valid.',
            ]);
            return;
        }

        $userId = Auth::id();

        try {
            // Hapus semua catatan untuk pelajar ini di semester aktif oleh guru ini
            $deleted = CatatanWaliKelas::where('pelajar_id', $pelajarId)
                ->where('guru_id', $userId)
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                ->delete();

            if ($deleted) {
                if (isset($this->catatanInput[$pelajarId])) {
                    unset($this->catatanInput[$pelajarId]);
                }

                $this->cachedCatatanExist = null;
                $this->loadCatatanPelajar();

                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text' => "Berhasil menghapus {$deleted} catatan pelajar.",
                ]);
            } else {
                $this->dispatch('swal:info', [
                    'title' => 'Info',
                    'text' => 'Data tidak ditemukan.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting catatan: ' . $e->getMessage(), ['pelajar_id' => $pelajarId]);
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat menghapus data.',
            ]);
        }
    }

    public function render()
    {
        $pelajarData = collect();
        $totalSiswa = 0;

        if ($this->rombel) {
            $totalSiswa = RombelPelajar::where('rombel_id', $this->rombel->id)->count();
        }

        if ($this->semesterAktif && $this->rombel) {
            $catatanExist = $this->cachedCatatanExist ?? collect();

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy(
                    Pelajar::select('nama_lengkap')
                        ->whereColumn('pelajars.id', 'rombel_pelajars.pelajar_id')
                )
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($catatanExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                if (!array_key_exists($pelajarId, $this->catatanInput)) {
                    $this->catatanInput[$pelajarId] = $catatanExist->get($pelajarId)?->catatan;
                }

                $catatanData = $catatanExist->get($pelajarId);

                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id' => $pelajarId,
                    'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                    'nisn' => $rombelPelajar->pelajar->nisn,
                    'catatan_sekarang' => $catatanData?->catatan,
                    'tanggal_input' => $catatanData?->tanggal_input,
                ];
            });
        }

        return view('livewire.wali.entri-catatan-pelajar', [
            'pelajarData' => $pelajarData,
            'totalSiswa' => $totalSiswa,
        ]);
    }
}
