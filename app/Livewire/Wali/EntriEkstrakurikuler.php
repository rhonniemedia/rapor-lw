<?php

namespace App\Livewire\Wali;

use App\Models\Rombel;
use Livewire\Component;
use App\Models\Pelajar;
use Livewire\WithPagination;
use App\Models\EkskulPelajar;
use App\Models\RombelPelajar;
use App\Models\Ekstrakurikuler;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class EntriEkstrakurikuler extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Properties
    public $rombel;
    public $semesterAktif;
    public $selectedEkstrakurikulerId = null;
    public $ekstrakurikulerList = [];
    public $pembinaName = null;

    // Search & Pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // Input nilai dan deskripsi
    public $nilaiInput = [];
    public $deskripsiInput = [];

    // Cache
    private $cachedEkskulExist = null;

    // Query string
    protected $queryString = [
        'selectedEkstrakurikulerId' => ['except' => null],
        'searchPelajar' => ['except' => ''],
    ];

    // Validation
    protected $rules = [
        'nilaiInput.*' => 'nullable|string|max:50',
        'deskripsiInput.*' => 'nullable|string|max:1000',
    ];

    protected $messages = [
        'nilaiInput.*.string' => 'Nilai harus berupa teks',
        'nilaiInput.*.max' => 'Nilai maksimal 50 karakter',
        'deskripsiInput.*.string' => 'Deskripsi harus berupa teks',
        'deskripsiInput.*.max' => 'Deskripsi maksimal 1000 karakter',
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

        $this->loadEkstrakurikuler();

        if ($this->selectedEkstrakurikulerId) {
            $this->loadEkskulPelajar();
        }
    }

    protected $listeners = ['deleteEkskul'];

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

    private function loadEkstrakurikuler(): void
    {
        $this->ekstrakurikulerList = Ekstrakurikuler::with('pembina')
            ->orderBy('nama')
            ->get();
    }

    public function updatedSelectedEkstrakurikulerId(): void
    {
        $this->resetPage();
        $this->searchPelajar = '';
        $this->nilaiInput = [];
        $this->deskripsiInput = [];
        $this->cachedEkskulExist = null;
        $this->loadEkskulPelajar();
    }

    public function updatingSearchPelajar(): void
    {
        $this->resetPage();
    }

    private function loadEkskulPelajar(): void
    {
        if (!$this->selectedEkstrakurikulerId || !$this->semesterAktif) {
            $this->nilaiInput = [];
            $this->deskripsiInput = [];
            $this->pembinaName = null;
            $this->cachedEkskulExist = null;
            return;
        }

        $ekstrakurikuler = Ekstrakurikuler::with('pembina')
            ->find($this->selectedEkstrakurikulerId);

        if (!$ekstrakurikuler) {
            $this->nilaiInput = [];
            $this->deskripsiInput = [];
            $this->pembinaName = null;
            $this->cachedEkskulExist = null;
            $this->selectedEkstrakurikulerId = null;
            return;
        }

        $this->pembinaName = $ekstrakurikuler->pembina->name ?? 'N/A';

        // Reload cache dari database
        $ekskulData = EkskulPelajar::where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->get()
            ->keyBy('pelajar_id');

        $this->cachedEkskulExist = $ekskulData;
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

    public function saveEkskul(): void
    {
        if (!$this->selectedEkstrakurikulerId || !$this->semesterAktif) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Silakan pilih ekstrakurikuler terlebih dahulu.',
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

        $ekstrakurikuler = Ekstrakurikuler::find($this->selectedEkstrakurikulerId);

        if (!$ekstrakurikuler) {
            Log::error('Ekstrakurikuler not found', ['id' => $this->selectedEkstrakurikulerId]);
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Data ekstrakurikuler tidak ditemukan.',
            ]);
            return;
        }

        $namaEkskul = $ekstrakurikuler->nama;

        $validPelajarIds = RombelPelajar::where('rombel_id', $this->rombel->id)
            ->pluck('pelajar_id')
            ->toArray();

        DB::beginTransaction();
        try {
            $savedCount = 0;
            $userId = Auth::id();

            foreach ($this->nilaiInput as $pelajarId => $nilai) {
                if (!in_array($pelajarId, $validPelajarIds)) {
                    continue;
                }

                // Jika nilai dan deskripsi kosong, skip
                $deskripsi = $this->deskripsiInput[$pelajarId] ?? null;
                if (empty($nilai) && empty($deskripsi)) {
                    continue;
                }

                try {
                    $existingEkskul = EkskulPelajar::where([
                        'pelajar_id' => $pelajarId,
                        'ekstrakurikuler_id' => $this->selectedEkstrakurikulerId,
                        'tahun_ajaran_semester_id' => $this->semesterAktif->id,
                    ])->lockForUpdate()->first();

                    $dataToSave = [
                        'nilai' => $nilai,
                        'deskripsi' => $deskripsi,
                        'updated_by' => $userId,
                    ];

                    if ($existingEkskul) {
                        $existingEkskul->update($dataToSave);
                    } else {
                        $dataToSave['pelajar_id'] = $pelajarId;
                        $dataToSave['ekstrakurikuler_id'] = $this->selectedEkstrakurikulerId;
                        $dataToSave['tahun_ajaran_semester_id'] = $this->semesterAktif->id;
                        $dataToSave['created_by'] = $userId;

                        EkskulPelajar::create($dataToSave);
                    }

                    $savedCount++;
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->errorInfo[1] == 1062) {
                        EkskulPelajar::where([
                            'pelajar_id' => $pelajarId,
                            'ekstrakurikuler_id' => $this->selectedEkstrakurikulerId,
                            'tahun_ajaran_semester_id' => $this->semesterAktif->id,
                        ])->update([
                            'nilai' => $nilai,
                            'deskripsi' => $deskripsi,
                            'updated_by' => $userId,
                        ]);

                        $savedCount++;
                    } else {
                        throw $e;
                    }
                }
            }

            DB::commit();

            $this->nilaiInput = [];
            $this->deskripsiInput = [];
            $this->cachedEkskulExist = null;
            $this->loadEkskulPelajar();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => "Berhasil menyimpan {$savedCount} data untuk ekstrakurikuler '{$namaEkskul}'.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving ekskul', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ekstrakurikuler_id' => $this->selectedEkstrakurikulerId,
                'semester_id' => $this->semesterAktif->id,
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ]);
        }
    }

    public function resetEkskul(): void
    {
        if ($this->cachedEkskulExist === null) {
            $this->loadEkskulPelajar();
        }

        foreach ($this->nilaiInput as $pelajarId => $nilai) {
            if ($this->cachedEkskulExist && $this->cachedEkskulExist->has($pelajarId)) {
                $this->nilaiInput[$pelajarId] = $this->cachedEkskulExist->get($pelajarId)->nilai;
                $this->deskripsiInput[$pelajarId] = $this->cachedEkskulExist->get($pelajarId)->deskripsi;
            } else {
                $this->nilaiInput[$pelajarId] = null;
                $this->deskripsiInput[$pelajarId] = null;
            }
        }

        $this->dispatch('swal:info', [
            'title' => 'Direset!',
            'text' => 'Input telah dikembalikan ke data tersimpan.',
        ]);
    }

    public function deleteEkskul($pelajarId = null): void
    {
        if (is_array($pelajarId) && isset($pelajarId[0])) {
            $pelajarId = $pelajarId[0];
        }

        if (!$pelajarId || !$this->selectedEkstrakurikulerId || !$this->semesterAktif) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'ID Pelajar tidak ditemukan atau data tidak valid.',
            ]);
            return;
        }

        try {
            $deleted = EkskulPelajar::where('pelajar_id', $pelajarId)
                ->where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                ->delete();

            if ($deleted) {
                if (isset($this->nilaiInput[$pelajarId])) {
                    unset($this->nilaiInput[$pelajarId]);
                }
                if (isset($this->deskripsiInput[$pelajarId])) {
                    unset($this->deskripsiInput[$pelajarId]);
                }

                $this->cachedEkskulExist = null;
                $this->loadEkskulPelajar();

                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text' => 'Data ekstrakurikuler berhasil dihapus.',
                ]);
            } else {
                $this->dispatch('swal:info', [
                    'title' => 'Info',
                    'text' => 'Data tidak ditemukan.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting ekskul: ' . $e->getMessage(), ['pelajar_id' => $pelajarId]);
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

        if ($this->selectedEkstrakurikulerId && $this->semesterAktif) {
            $ekskulExist = $this->cachedEkskulExist ?? collect();

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($ekskulExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                if (!array_key_exists($pelajarId, $this->nilaiInput)) {
                    $this->nilaiInput[$pelajarId] = $ekskulExist->get($pelajarId)?->nilai;
                }
                if (!array_key_exists($pelajarId, $this->deskripsiInput)) {
                    $this->deskripsiInput[$pelajarId] = $ekskulExist->get($pelajarId)?->deskripsi;
                }

                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id' => $pelajarId,
                    'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                    'nisn' => $rombelPelajar->pelajar->nisn,
                    'nilai_sekarang' => $ekskulExist->get($pelajarId)?->nilai,
                    'deskripsi_sekarang' => $ekskulExist->get($pelajarId)?->deskripsi,
                ];
            });
        }

        return view('livewire.wali.entri-ekstrakurikuler', [
            'pelajarData' => $pelajarData,
            'totalSiswa' => $totalSiswa,
        ]);
    }
}
