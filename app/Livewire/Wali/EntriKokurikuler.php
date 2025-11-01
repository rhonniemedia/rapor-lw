<?php

namespace App\Livewire\Wali;

use App\Models\Rombel;
use Livewire\Component;
use App\Models\Pelajar;
use Livewire\WithPagination;
use App\Models\Kokurikuler;
use App\Models\RombelPelajar;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class EntriKokurikuler extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Properties
    public $rombel;
    public $semesterAktif;

    // Search & Pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // Input kokurikuler
    public $predikatInput = [];
    public $capaianInput = [];

    // Cache
    private $cachedKokurikulerExist = null;

    // Query string
    protected $queryString = [
        'searchPelajar' => ['except' => ''],
    ];

    // Validation
    protected $rules = [
        'predikatInput.*' => 'nullable|string|in:Berkembang,Cakap,Mahir',
        'capaianInput.*' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'predikatInput.*.string' => 'Predikat harus berupa teks',
        'predikatInput.*.in' => 'Predikat harus salah satu dari: Berkembang, Cakap, Mahir',
        'capaianInput.*.string' => 'Capaian harus berupa teks',
        'capaianInput.*.max' => 'Capaian maksimal 1000 karakter',
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

        $this->loadKokurikulerPelajar();
    }

    protected $listeners = ['deleteKokurikuler'];

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

    private function loadKokurikulerPelajar(): void
    {
        if (!$this->semesterAktif) {
            $this->predikatInput = [];
            $this->capaianInput = [];
            $this->cachedKokurikulerExist = null;
            return;
        }

        $userId = Auth::id();

        // Reload cache dari database
        $kokurikulerData = Kokurikuler::where('guru_id', $userId)
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->get()
            ->keyBy('pelajar_id');

        $this->cachedKokurikulerExist = $kokurikulerData;
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

    public function saveKokurikuler(): void
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

            foreach ($this->predikatInput as $pelajarId => $predikat) {
                if (!in_array($pelajarId, $validPelajarIds)) {
                    continue;
                }

                $capaian = $this->capaianInput[$pelajarId] ?? null;

                // Jika predikat dan capaian kosong, skip
                if (empty($predikat) && empty($capaian)) {
                    continue;
                }

                try {
                    // Cari data yang sudah ada untuk di-update
                    $existingKokurikuler = Kokurikuler::where([
                        'pelajar_id' => $pelajarId,
                        'guru_id' => $userId,
                        'tahun_ajaran_semester_id' => $this->semesterAktif->id,
                    ])->lockForUpdate()->first();

                    $dataToSave = [
                        'predikat' => $predikat,
                        'capaian' => $capaian,
                        'tanggal_input' => $tanggalInput,
                        'updated_by' => $userId,
                    ];

                    if ($existingKokurikuler) {
                        // Update data yang sudah ada
                        $existingKokurikuler->update($dataToSave);
                    } else {
                        // Buat data baru jika belum ada
                        $dataToSave['pelajar_id'] = $pelajarId;
                        $dataToSave['guru_id'] = $userId;
                        $dataToSave['tahun_ajaran_semester_id'] = $this->semesterAktif->id;
                        $dataToSave['created_by'] = $userId;

                        Kokurikuler::create($dataToSave);
                    }

                    $savedCount++;
                } catch (\Exception $e) {
                    Log::error('Error saving individual kokurikuler', [
                        'pelajar_id' => $pelajarId,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }

            DB::commit();

            $this->predikatInput = [];
            $this->capaianInput = [];
            $this->cachedKokurikulerExist = null;
            $this->loadKokurikulerPelajar();

            if ($savedCount > 0) {
                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text' => "Berhasil menyimpan {$savedCount} data kokurikuler pelajar.",
                ]);
            } else {
                $this->dispatch('swal:info', [
                    'title' => 'Info',
                    'text' => 'Tidak ada data baru yang disimpan.',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving kokurikuler', [
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

    public function resetKokurikuler(): void
    {
        if ($this->cachedKokurikulerExist === null) {
            $this->loadKokurikulerPelajar();
        }

        foreach ($this->predikatInput as $pelajarId => $predikat) {
            if ($this->cachedKokurikulerExist && $this->cachedKokurikulerExist->has($pelajarId)) {
                $this->predikatInput[$pelajarId] = $this->cachedKokurikulerExist->get($pelajarId)->predikat;
                $this->capaianInput[$pelajarId] = $this->cachedKokurikulerExist->get($pelajarId)->capaian;
            } else {
                $this->predikatInput[$pelajarId] = null;
                $this->capaianInput[$pelajarId] = null;
            }
        }

        $this->dispatch('swal:info', [
            'title' => 'Direset!',
            'text' => 'Input telah dikembalikan ke data tersimpan.',
        ]);
    }

    public function deleteKokurikuler($pelajarId = null): void
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
            // Hapus data kokurikuler untuk pelajar ini di semester aktif oleh guru ini
            $deleted = Kokurikuler::where('pelajar_id', $pelajarId)
                ->where('guru_id', $userId)
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                ->delete();

            if ($deleted) {
                if (isset($this->predikatInput[$pelajarId])) {
                    unset($this->predikatInput[$pelajarId]);
                }
                if (isset($this->capaianInput[$pelajarId])) {
                    unset($this->capaianInput[$pelajarId]);
                }

                $this->cachedKokurikulerExist = null;
                $this->loadKokurikulerPelajar();

                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text' => 'Data kokurikuler berhasil dihapus.',
                ]);
            } else {
                $this->dispatch('swal:info', [
                    'title' => 'Info',
                    'text' => 'Data tidak ditemukan.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting kokurikuler: ' . $e->getMessage(), ['pelajar_id' => $pelajarId]);
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
            $kokurikulerExist = $this->cachedKokurikulerExist ?? collect();

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($kokurikulerExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                if (!array_key_exists($pelajarId, $this->predikatInput)) {
                    $this->predikatInput[$pelajarId] = $kokurikulerExist->get($pelajarId)?->predikat;
                }
                if (!array_key_exists($pelajarId, $this->capaianInput)) {
                    $this->capaianInput[$pelajarId] = $kokurikulerExist->get($pelajarId)?->capaian;
                }

                $kokurikulerData = $kokurikulerExist->get($pelajarId);

                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id' => $pelajarId,
                    'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                    'nisn' => $rombelPelajar->pelajar->nisn,
                    'predikat_sekarang' => $kokurikulerData?->predikat,
                    'capaian_sekarang' => $kokurikulerData?->capaian,
                    'tanggal_input' => $kokurikulerData?->tanggal_input,
                ];
            });
        }

        return view('livewire.wali.entri-kokurikuler', [
            'pelajarData' => $pelajarData,
            'totalSiswa' => $totalSiswa,
        ]);
    }
}
