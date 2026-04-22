<?php

namespace App\Livewire\Wali;

use App\Models\Rombel;
use Livewire\Component;
use App\Models\Pelajar;
use Livewire\WithPagination;
use App\Models\Kokurikuler;
use App\Models\RombelPelajar;
use App\Models\TahunAjaran;
use App\Models\TahunAjaranSemester;
use App\Models\TemplateKokurikulerCapaian;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class EntriKokurikuler extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filter Properties
    public $tahunAjaranId = null;
    public $semesterId = null;

    // Data Collections
    public $tahunAjaranList = [];
    public $semesterList = [];

    // Main Data
    public $rombel;

    // Search & Pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // Input kokurikuler
    public $kokurikulerInput = [];
    public $generateMode = 'empty';

    // Cache
    private $cachedKokurikulerExist = null;

    // Predikat Options
    public $predikatOptions = [
        'A' => 'Mahir',
        'B' => 'Cakap',
        'C' => 'Berkembang',
    ];

    // Query string
    protected $queryString = [
        'tahunAjaranId' => ['except' => null],
        'semesterId'    => ['except' => null],
        'searchPelajar' => ['except' => ''],
    ];

    // Validation
    protected $rules = [
        'kokurikulerInput.*.predikat' => 'nullable|string|in:A,B,C',
        'kokurikulerInput.*.capaian'  => 'nullable|string|max:1000',
        'generateMode'                => 'required|in:empty,all',
    ];

    protected $messages = [
        'kokurikulerInput.*.predikat.in'  => 'Predikat harus salah satu dari: A, B, C',
        'kokurikulerInput.*.capaian.string' => 'Capaian harus berupa teks',
        'kokurikulerInput.*.capaian.max'  => 'Capaian maksimal 1000 karakter',
        'generateMode.required'           => 'Pilih mode generate',
        'generateMode.in'                 => 'Mode generate tidak valid',
    ];

    protected $listeners = [
        'deleteKokurikuler',
        'confirmResetKokurikuler' => 'resetKokurikuler',
        'closeGenerateModal',
    ];

    public function mount()
    {
        $this->loadRombelWaliKelas();

        if (!$this->rombel) {
            session()->flash('error', 'Anda tidak memiliki kelas binaan.');
            return redirect()->route('walikelas.dashboard');
        }

        $this->initializeFilters();
    }

    // ========================================
    // INITIALIZATION METHODS
    // ========================================

    private function initializeFilters(): void
    {
        $this->loadTahunAjaran();

        if (!$this->tahunAjaranId) {
            $this->setActiveTahunAjaran();
        }

        if ($this->tahunAjaranId) {
            $this->loadSemester();

            if (!$this->semesterId) {
                $this->setActiveSemester();
            }
        }

        if ($this->tahunAjaranId && $this->semesterId) {
            $this->loadKokurikulerPelajar();
        }
    }

    private function setActiveTahunAjaran(): void
    {
        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        if ($activeTahunAjaran) {
            $this->tahunAjaranId = $activeTahunAjaran->id;
        }
    }

    private function setActiveSemester(): void
    {
        $activeSemester = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahunAjaranId)
            ->where('status', 'aktif')
            ->first();
        if ($activeSemester) {
            $this->semesterId = $activeSemester->id;
        }
    }

    // ========================================
    // DATA LOADING METHODS
    // ========================================

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

    private function loadTahunAjaran(): void
    {
        $this->tahunAjaranList = TahunAjaran::orderBy('tgl_mulai', 'desc')->get();
    }

    private function loadSemester(): void
    {
        if (!$this->tahunAjaranId) {
            $this->semesterList = [];
            return;
        }

        $this->semesterList = TahunAjaranSemester::with('semester')
            ->where('tahun_ajaran_id', $this->tahunAjaranId)
            ->get();
    }

    private function loadKokurikulerPelajar(): void
    {
        if (!$this->semesterId || !$this->rombel) {
            $this->kokurikulerInput = [];
            $this->cachedKokurikulerExist = null;
            return;
        }

        $kokurikulerData = Kokurikuler::where('tahun_ajaran_semester_id', $this->semesterId)
            ->whereIn('pelajar_id', function ($q) {
                $q->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombel->id);
            })
            ->get()
            ->keyBy('pelajar_id');

        $this->cachedKokurikulerExist = $kokurikulerData;
        $this->kokurikulerInput = [];

        foreach ($kokurikulerData as $pelajarId => $data) {
            $this->kokurikulerInput[$pelajarId] = [
                'predikat' => $data->predikat ?? null,
                'capaian'  => $data->capaian ?? '',
            ];
        }
    }

    // ========================================
    // FILTER UPDATE HANDLERS
    // ========================================

    public function updatedTahunAjaranId(): void
    {
        $this->resetFilters();
        $this->loadSemester();
        $this->setActiveSemester();

        if ($this->semesterId) {
            $this->updatedSemesterId();
        }

        $this->resetPage();
    }

    public function updatedSemesterId(): void
    {
        $this->kokurikulerInput = [];
        $this->cachedKokurikulerExist = null;

        $this->loadKokurikulerPelajar();
        $this->resetPage();
    }

    public function updatingSearchPelajar(): void
    {
        $this->resetPage();
    }

    // ========================================
    // QUERY HELPER
    // ========================================

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

    // ========================================
    // SAVE / RESET / DELETE METHODS
    // ========================================

    public function saveKokurikuler(): void
    {
        if (!$this->semesterId || !$this->rombel) {
            $this->dispatchError('Tidak ada semester dipilih atau kelas binaan.');
            return;
        }

        try {
            $this->validate([
                'kokurikulerInput.*.predikat' => 'nullable|string|in:A,B,C',
                'kokurikulerInput.*.capaian'  => 'nullable|string|max:1000',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            $this->dispatchError('Periksa input Anda. ' . implode(', ', array_map(fn($err) => implode(', ', $err), $e->errors())), 'Validasi Gagal!');
            return;
        }

        $validPelajarIds = RombelPelajar::where('rombel_id', $this->rombel->id)
            ->pluck('pelajar_id')
            ->toArray();

        DB::beginTransaction();
        try {
            $result = $this->processKokurikulerSaving($validPelajarIds);

            DB::commit();

            $this->kokurikulerInput = [];
            $this->cachedKokurikulerExist = null;
            $this->loadKokurikulerPelajar();

            if ($result['totalProcessed'] > 0) {
                $message = $this->buildSaveSuccessMessage($this->rombel->nama ?? 'Kelas', $result);
                $this->dispatchSuccess($message);
            } else {
                $this->dispatchInfo('Tidak ada data baru yang disimpan.');
            }
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving kokurikuler', [
                'message'     => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
                'semester_id' => $this->semesterId,
            ]);

            $this->dispatchError('Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    private function processKokurikulerSaving(array $validPelajarIds): array
    {
        $savedCount   = 0;
        $updatedCount = 0;
        $deletedCount = 0;
        $guruId       = Auth::id();
        $tanggalInput = now();

        foreach ($this->kokurikulerInput as $pelajarId => $input) {
            if (!in_array($pelajarId, $validPelajarIds)) {
                continue;
            }

            $predikat = trim($input['predikat'] ?? '');
            $capaian  = trim($input['capaian'] ?? '');

            $existingData = Kokurikuler::where('pelajar_id', $pelajarId)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->first();

            if (empty($predikat) && empty($capaian)) {
                if ($existingData) {
                    $existingData->delete();
                    $deletedCount++;
                }
                continue;
            }

            if (!in_array($predikat, array_keys($this->predikatOptions)) && !empty($predikat)) {
                continue;
            }

            if ($existingData) {
                $existingData->update([
                    'predikat'      => $predikat,
                    'capaian'       => $capaian,
                    'guru_id'       => $guruId,
                    'tanggal_input' => $tanggalInput,
                    'updated_by'    => $guruId,
                ]);
                $updatedCount++;
            } else {
                if (!empty($predikat)) {
                    Kokurikuler::create([
                        'pelajar_id'               => $pelajarId,
                        'guru_id'                  => $guruId,
                        'tahun_ajaran_semester_id' => $this->semesterId,
                        'predikat'                 => $predikat,
                        'capaian'                  => $capaian,
                        'tanggal_input'            => $tanggalInput,
                        'created_by'               => $guruId,
                    ]);
                    $savedCount++;
                }
            }
        }

        return [
            'savedCount'     => $savedCount,
            'updatedCount'   => $updatedCount,
            'deletedCount'   => $deletedCount,
            'totalProcessed' => $savedCount + $updatedCount + $deletedCount,
        ];
    }

    private function buildSaveSuccessMessage(string $rombelNama, array $result): string
    {
        $messages = [];
        if ($result['savedCount'] > 0)   $messages[] = "{$result['savedCount']} baru";
        if ($result['updatedCount'] > 0) $messages[] = "{$result['updatedCount']} diperbarui";
        if ($result['deletedCount'] > 0) $messages[] = "{$result['deletedCount']} dihapus";

        return "Berhasil menyimpan data kokurikuler untuk {$rombelNama}: " . implode(", ", $messages);
    }

    public function resetKokurikuler(): void
    {
        if ($this->cachedKokurikulerExist === null) {
            $this->loadKokurikulerPelajar();
        }

        foreach ($this->cachedKokurikulerExist as $pelajarId => $data) {
            $this->kokurikulerInput[$pelajarId] = [
                'predikat' => $data->predikat ?? null,
                'capaian'  => $data->capaian ?? '',
            ];
        }

        foreach ($this->kokurikulerInput as $pelajarId => $input) {
            if (!isset($this->cachedKokurikulerExist[$pelajarId])) {
                $this->kokurikulerInput[$pelajarId] = [
                    'predikat' => null,
                    'capaian'  => ''
                ];
            }
        }

        $this->dispatchInfo('Input telah dikembalikan ke data tersimpan.');
    }

    public function deleteKokurikuler($pelajarId = null): void
    {
        if (is_array($pelajarId) && isset($pelajarId[0])) {
            $pelajarId = $pelajarId[0];
        }

        if (!$pelajarId || !$this->semesterId) {
            $this->dispatchError('ID Pelajar tidak ditemukan atau data tidak valid.', 'Gagal!');
            return;
        }

        $userId = Auth::id();

        try {
            $deleted = Kokurikuler::where('pelajar_id', $pelajarId)
                ->where('guru_id', $userId)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->delete();

            if ($deleted) {
                if (isset($this->kokurikulerInput[$pelajarId])) {
                    $this->kokurikulerInput[$pelajarId] = [
                        'predikat' => null,
                        'capaian'  => ''
                    ];
                }

                $this->cachedKokurikulerExist = null;
                $this->loadKokurikulerPelajar();

                $this->dispatchSuccess('Data kokurikuler berhasil dihapus.');
            } else {
                $this->dispatchInfo('Data tidak ditemukan.');
            }
        } catch (\Exception $e) {
            Log::error('Error deleting kokurikuler: ' . $e->getMessage(), ['pelajar_id' => $pelajarId]);
            $this->dispatchError('Terjadi kesalahan saat menghapus data.');
        }
    }

    // ========================================
    // GENERATE CAPAIAN METHODS
    // ========================================

    public function openGenerateModal(): void
    {
        if (!$this->validateGenerateContext()) {
            return;
        }

        $statistics = $this->calculateGenerateStatistics();

        if (!$this->validateGenerateStatistics($statistics)) {
            return;
        }

        $this->dispatch('show-generate-modal', $statistics);
    }

    private function calculateGenerateStatistics(): array
    {
        $countPelajarWithKokurikuler = Kokurikuler::where('tahun_ajaran_semester_id', $this->semesterId)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombel->id);
            })
            ->count();

        $countCapaianKosong = Kokurikuler::where('tahun_ajaran_semester_id', $this->semesterId)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombel->id);
            })
            ->where(function (Builder $query) {
                $query->whereNull('capaian')
                    ->orWhere('capaian', '');
            })
            ->count();

        return [
            'countPelajarWithKokurikuler' => $countPelajarWithKokurikuler,
            'countCapaianKosong'          => $countCapaianKosong,
            'countTemplateAvailable'      => TemplateKokurikulerCapaian::where('tahun_ajaran_semester_id', $this->semesterId)
                ->where('tingkat', $this->rombel->tingkat)
                ->where('aktif', true)
                ->count(),
        ];
    }

    private function validateGenerateStatistics(array $statistics): bool
    {
        if ($statistics['countTemplateAvailable'] === 0) {
            $this->dispatchError('Belum ada template capaian untuk tingkat kelas ini.', 'Template Tidak Ditemukan!');
            return false;
        }

        if ($statistics['countPelajarWithKokurikuler'] === 0) {
            $this->dispatchError('Belum ada pelajar yang memiliki data kokurikuler tersimpan.', 'Tidak Ada Data!');
            return false;
        }

        return true;
    }

    public function closeGenerateModal(): void
    {
        $this->dispatch('hide-generate-modal');
        $this->generateMode = 'empty';
    }

    public function generateCapaian(): void
    {
        $this->validate([
            'generateMode' => 'required|in:empty,all',
        ]);

        if (!$this->validateGenerateContext()) {
            return;
        }

        DB::beginTransaction();
        try {
            $result = $this->processCapaianGeneration();

            DB::commit();

            $this->closeGenerateModal();
            $this->cachedKokurikulerExist = null;
            $this->loadKokurikulerPelajar();

            $this->dispatchGenerateResult($result);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error generating capaian kokurikuler: ' . $e->getMessage());
            $this->dispatchError('Terjadi kesalahan saat generate capaian.');
        }
    }

    private function processCapaianGeneration(): array
    {
        $query = Kokurikuler::where('tahun_ajaran_semester_id', $this->semesterId)
            ->whereIn('pelajar_id', function ($q) {
                $q->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombel->id);
            });

        if ($this->generateMode === 'empty') {
            $query->where(function (Builder $q) {
                $q->whereNull('capaian')
                    ->orWhere('capaian', '');
            });
        }

        $kokurikulerList = $query->get();
        $successCount    = 0;
        $errorList       = [];

        foreach ($kokurikulerList as $kokurikuler) {
            if (!isset($this->predikatOptions[$kokurikuler->predikat])) {
                continue;
            }

            $template = $this->getMatchingTemplate($kokurikuler->predikat);

            if ($template) {
                $kokurikuler->capaian = $template->deskripsi;
                $kokurikuler->save();
                $successCount++;
            } else {
                $pelajar     = $kokurikuler->pelajar;
                $errorList[] = [
                    'nama'     => $pelajar->nama_lengkap ?? 'N/A',
                    'predikat' => $this->predikatOptions[$kokurikuler->predikat] ?? $kokurikuler->predikat,
                ];
            }
        }

        return ['successCount' => $successCount, 'errorList' => $errorList];
    }

    private function getMatchingTemplate(string $predikat): ?TemplateKokurikulerCapaian
    {
        return TemplateKokurikulerCapaian::where('tahun_ajaran_semester_id', $this->semesterId)
            ->where('tingkat', $this->rombel->tingkat)
            ->where('predikat', $predikat)
            ->where('aktif', true)
            ->first();
    }

    private function dispatchGenerateResult(array $result): void
    {
        if (empty($result['errorList'])) {
            $this->dispatchSuccess("Berhasil generate capaian untuk {$result['successCount']} pelajar.");
            return;
        }

        $errorMessage = $this->buildGenerateErrorMessage($result);
        $this->dispatch('swal:warning', [
            'title' => 'Generate Selesai!',
            'text'  => $errorMessage,
        ]);
    }

    private function buildGenerateErrorMessage(array $result): string
    {
        $message  = "Generate selesai dengan catatan:\n";
        $message .= "- Berhasil: {$result['successCount']} pelajar\n";
        $message .= "- Gagal: " . count($result['errorList']) . " pelajar (tidak ada template yang cocok)\n\n";
        $message .= "Detail error:\n";

        foreach ($result['errorList'] as $error) {
            $message .= "- {$error['nama']} (predikat: {$error['predikat']}) - Template tidak ditemukan\n";
        }

        return $message;
    }

    // ========================================
    // UTILITY / VALIDATION METHODS
    // ========================================

    private function resetFilters(): void
    {
        $this->semesterId            = null;
        $this->semesterList          = [];
        $this->kokurikulerInput      = [];
        $this->cachedKokurikulerExist = null;
    }

    private function validateGenerateContext(): bool
    {
        if (!$this->rombel || !$this->semesterId) {
            $this->dispatchError('Pastikan tahun ajaran dan semester sudah dipilih.');
            return false;
        }
        return true;
    }

    // ========================================
    // DISPATCH HELPER METHODS
    // ========================================

    private function dispatchSuccess(string $text, string $title = 'Berhasil!'): void
    {
        $this->dispatch('swal:success', ['title' => $title, 'text' => $text]);
    }

    private function dispatchError(string $text, string $title = 'Error!'): void
    {
        $this->dispatch('swal:error', ['title' => $title, 'text' => $text]);
    }

    private function dispatchInfo(string $text, string $title = 'Info'): void
    {
        $this->dispatch('swal:info', ['title' => $title, 'text' => $text]);
    }

    // ========================================
    // RENDER METHOD
    // ========================================

    public function render()
    {
        $pelajarData = collect();

        if ($this->rombel && $this->semesterId) {

            if ($this->cachedKokurikulerExist === null) {
                $this->loadKokurikulerPelajar();
            }

            $kokurikulerExist = $this->cachedKokurikulerExist ?? collect();

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy(
                    Pelajar::select('nama_lengkap')
                        ->whereColumn('pelajars.id', 'rombel_pelajars.pelajar_id')
                )
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($kokurikulerExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                if (!isset($this->kokurikulerInput[$pelajarId])) {
                    $existingKokurikuler = $kokurikulerExist->get($pelajarId);
                    $this->kokurikulerInput[$pelajarId] = [
                        'predikat' => $existingKokurikuler->predikat ?? null,
                        'capaian'  => $existingKokurikuler->capaian ?? '',
                    ];
                }

                $existingKokurikulerData = $kokurikulerExist->get($pelajarId);

                return (object) [
                    'rombel_pelajar_id'   => $rombelPelajar->id,
                    'pelajar_id'          => $pelajarId,
                    'nama_lengkap'        => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk'         => $rombelPelajar->pelajar->nomor_induk,
                    'nisn'                => $rombelPelajar->pelajar->nisn,
                    'kokurikuler_existing' => $existingKokurikulerData,
                ];
            });
        }

        // Resolve selected semester label for display
        $selectedSemesterObj = $this->semesterId
            ? collect($this->semesterList)->firstWhere('id', $this->semesterId)
            : null;

        return view('livewire.wali.entri-kokurikuler', [
            'pelajarData'         => $pelajarData,
            'selectedSemesterObj' => $selectedSemesterObj,
        ]);
    }
}
