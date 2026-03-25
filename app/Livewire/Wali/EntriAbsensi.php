<?php

namespace App\Livewire\Wali;

use App\Models\Rombel;
use Livewire\Component;
use App\Models\Pelajar;
use Livewire\WithPagination;
use App\Models\Kehadiran;
use App\Models\RombelPelajar;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class EntriAbsensi extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Properties
    public $rombel;
    public $semesterAktif;

    // Search & Pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // Input absensi
    public $sakitInput = [];
    public $izinInput = [];
    public $tanpaKeteranganInput = [];

    // Cache
    private $cachedKehadiranExist = null;

    // Query string
    protected $queryString = [
        'searchPelajar' => ['except' => ''],
    ];

    // Validation
    protected $rules = [
        'sakitInput.*' => 'nullable|integer|min:0|max:999',
        'izinInput.*' => 'nullable|integer|min:0|max:999',
        'tanpaKeteranganInput.*' => 'nullable|integer|min:0|max:999',
    ];

    protected $messages = [
        'sakitInput.*.integer' => 'Jumlah sakit harus berupa angka',
        'sakitInput.*.min' => 'Jumlah sakit tidak boleh negatif',
        'sakitInput.*.max' => 'Jumlah sakit maksimal 999',
        'izinInput.*.integer' => 'Jumlah izin harus berupa angka',
        'izinInput.*.min' => 'Jumlah izin tidak boleh negatif',
        'izinInput.*.max' => 'Jumlah izin maksimal 999',
        'tanpaKeteranganInput.*.integer' => 'Jumlah tanpa keterangan harus berupa angka',
        'tanpaKeteranganInput.*.min' => 'Jumlah tanpa keterangan tidak boleh negatif',
        'tanpaKeteranganInput.*.max' => 'Jumlah tanpa keterangan maksimal 999',
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

        $this->loadKehadiranPelajar();
    }

    protected $listeners = ['deleteKehadiran'];

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

    private function loadKehadiranPelajar(): void
    {
        if (!$this->semesterAktif || !$this->rombel) {
            $this->sakitInput = [];
            $this->izinInput = [];
            $this->tanpaKeteranganInput = [];
            $this->cachedKehadiranExist = null;
            return;
        }

        // Reload cache dari database
        $kehadiranData = Kehadiran::where('rombel_id', $this->rombel->id)
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->get()
            ->keyBy('pelajar_id');

        $this->cachedKehadiranExist = $kehadiranData;
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

    public function saveKehadiran(): void
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

            foreach ($this->sakitInput as $pelajarId => $sakit) {
                if (!in_array($pelajarId, $validPelajarIds)) {
                    continue;
                }

                $izin = $this->izinInput[$pelajarId] ?? 0;
                $tanpaKeterangan = $this->tanpaKeteranganInput[$pelajarId] ?? 0;

                // Jika semua nilai kosong atau 0, skip
                if (empty($sakit) && empty($izin) && empty($tanpaKeterangan)) {
                    continue;
                }

                // Convert empty to 0
                $sakit = empty($sakit) ? 0 : (int)$sakit;
                $izin = empty($izin) ? 0 : (int)$izin;
                $tanpaKeterangan = empty($tanpaKeterangan) ? 0 : (int)$tanpaKeterangan;

                try {
                    $existingKehadiran = Kehadiran::where([
                        'pelajar_id' => $pelajarId,
                        'rombel_id' => $this->rombel->id,
                        'tahun_ajaran_semester_id' => $this->semesterAktif->id,
                    ])->lockForUpdate()->first();

                    $dataToSave = [
                        'jumlah_sakit' => $sakit,
                        'jumlah_izin' => $izin,
                        'jumlah_tanpa_keterangan' => $tanpaKeterangan,
                        'updated_by' => $userId,
                    ];

                    if ($existingKehadiran) {
                        $existingKehadiran->update($dataToSave);
                    } else {
                        $dataToSave['pelajar_id'] = $pelajarId;
                        $dataToSave['rombel_id'] = $this->rombel->id;
                        $dataToSave['tahun_ajaran_semester_id'] = $this->semesterAktif->id;
                        $dataToSave['created_by'] = $userId;

                        Kehadiran::create($dataToSave);
                    }

                    $savedCount++;
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->errorInfo[1] == 1062) {
                        Kehadiran::where([
                            'pelajar_id' => $pelajarId,
                            'rombel_id' => $this->rombel->id,
                            'tahun_ajaran_semester_id' => $this->semesterAktif->id,
                        ])->update($dataToSave);

                        $savedCount++;
                    } else {
                        throw $e;
                    }
                }
            }

            DB::commit();

            $this->sakitInput = [];
            $this->izinInput = [];
            $this->tanpaKeteranganInput = [];
            $this->cachedKehadiranExist = null;
            $this->loadKehadiranPelajar();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => "Berhasil menyimpan {$savedCount} data kehadiran pelajar.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving kehadiran', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'rombel_id' => $this->rombel->id,
                'semester_id' => $this->semesterAktif->id,
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ]);
        }
    }

    public function resetKehadiran(): void
    {
        if ($this->cachedKehadiranExist === null) {
            $this->loadKehadiranPelajar();
        }

        foreach ($this->sakitInput as $pelajarId => $value) {
            if ($this->cachedKehadiranExist && $this->cachedKehadiranExist->has($pelajarId)) {
                $this->sakitInput[$pelajarId] = $this->cachedKehadiranExist->get($pelajarId)->jumlah_sakit;
                $this->izinInput[$pelajarId] = $this->cachedKehadiranExist->get($pelajarId)->jumlah_izin;
                $this->tanpaKeteranganInput[$pelajarId] = $this->cachedKehadiranExist->get($pelajarId)->jumlah_tanpa_keterangan;
            } else {
                $this->sakitInput[$pelajarId] = null;
                $this->izinInput[$pelajarId] = null;
                $this->tanpaKeteranganInput[$pelajarId] = null;
            }
        }

        $this->dispatch('swal:info', [
            'title' => 'Direset!',
            'text' => 'Input telah dikembalikan ke data tersimpan.',
        ]);
    }

    public function deleteKehadiran($pelajarId = null): void
    {
        if (is_array($pelajarId) && isset($pelajarId[0])) {
            $pelajarId = $pelajarId[0];
        }

        if (!$pelajarId || !$this->semesterAktif || !$this->rombel) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'ID Pelajar tidak ditemukan atau data tidak valid.',
            ]);
            return;
        }

        try {
            $deleted = Kehadiran::where('pelajar_id', $pelajarId)
                ->where('rombel_id', $this->rombel->id)
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                ->delete();

            if ($deleted) {
                if (isset($this->sakitInput[$pelajarId])) {
                    unset($this->sakitInput[$pelajarId]);
                }
                if (isset($this->izinInput[$pelajarId])) {
                    unset($this->izinInput[$pelajarId]);
                }
                if (isset($this->tanpaKeteranganInput[$pelajarId])) {
                    unset($this->tanpaKeteranganInput[$pelajarId]);
                }

                $this->cachedKehadiranExist = null;
                $this->loadKehadiranPelajar();

                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text' => 'Data kehadiran berhasil dihapus.',
                ]);
            } else {
                $this->dispatch('swal:info', [
                    'title' => 'Info',
                    'text' => 'Data tidak ditemukan.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting kehadiran: ' . $e->getMessage(), ['pelajar_id' => $pelajarId]);
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
            $kehadiranExist = $this->cachedKehadiranExist ?? collect();

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy(
                    Pelajar::select('nama_lengkap')
                        ->whereColumn('pelajars.id', 'rombel_pelajars.pelajar_id')
                )
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($kehadiranExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                if (!array_key_exists($pelajarId, $this->sakitInput)) {
                    $this->sakitInput[$pelajarId] = $kehadiranExist->get($pelajarId)?->jumlah_sakit;
                }
                if (!array_key_exists($pelajarId, $this->izinInput)) {
                    $this->izinInput[$pelajarId] = $kehadiranExist->get($pelajarId)?->jumlah_izin;
                }
                if (!array_key_exists($pelajarId, $this->tanpaKeteranganInput)) {
                    $this->tanpaKeteranganInput[$pelajarId] = $kehadiranExist->get($pelajarId)?->jumlah_tanpa_keterangan;
                }

                $kehadiranData = $kehadiranExist->get($pelajarId);
                $totalKehadiran = 0;

                if ($kehadiranData) {
                    $totalKehadiran = ($kehadiranData->jumlah_sakit ?? 0) +
                        ($kehadiranData->jumlah_izin ?? 0) +
                        ($kehadiranData->jumlah_tanpa_keterangan ?? 0);
                }

                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id' => $pelajarId,
                    'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                    'nisn' => $rombelPelajar->pelajar->nisn,
                    'sakit_sekarang' => $kehadiranData?->jumlah_sakit,
                    'izin_sekarang' => $kehadiranData?->jumlah_izin,
                    'tanpa_keterangan_sekarang' => $kehadiranData?->jumlah_tanpa_keterangan,
                    'total_ketidakhadiran' => $totalKehadiran,
                ];
            });
        }

        return view('livewire.wali.entri-absensi', [
            'pelajarData' => $pelajarData,
            'totalSiswa' => $totalSiswa,
        ]);
    }
}
