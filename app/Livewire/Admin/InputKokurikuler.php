<?php

namespace App\Livewire\Admin;

use App\Models\Rombel;
use Livewire\Component;
use App\Models\TahunAjaran;
use Livewire\WithPagination;
use App\Models\Kokurikuler;
use App\Models\RombelPelajar;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\TemplateKokurikulerCapaian;
use Illuminate\Database\Eloquent\Builder;

class InputKokurikuler extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filter Properties
    public $tahunAjaranId = null;
    public $semesterId = null;
    public $rombelId = null;
    public $selectedRombelPengajarId = null;

    // Search & Pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // Data Collections
    public $tahunAjaranList = [];
    public $semesterList = [];
    public $rombelList = [];

    // Main Data
    public $rombel;
    public $kokurikulerInput = [];
    public $generateMode = 'empty';

    // Predikat Options (Diubah sesuai permintaan: A=Mahir, B=Cakap, C=Berkembang. D dihapus.)
    public $predikatOptions = [
        'A' => 'Mahir',
        'B' => 'Cakap',
        'C' => 'Berkembang',
        // 'D' => 'Kurang' // Dihapus
    ];

    protected $queryString = [
        'tahunAjaranId' => ['except' => null],
        'semesterId' => ['except' => null],
        'rombelId' => ['except' => null],
        'searchPelajar' => ['except' => ''],
    ];

    protected $listeners = [
        'resetKokurikulerConfirmed' => 'resetKokurikuler',
        'deleteKokurikuler' => 'deleteKokurikuler',
    ];

    protected $rules = [
        'kokurikulerInput.*.predikat' => 'nullable|string|in:A,B,C',
        'kokurikulerInput.*.capaian' => 'nullable|string|max:1000',
        'generateMode' => 'required|in:empty,all',
    ];

    protected $messages = [
        'kokurikulerInput.*.predikat.in' => 'Predikat harus salah satu dari: A, B, C',
        'kokurikulerInput.*.capaian.string' => 'Capaian harus berupa teks',
        'kokurikulerInput.*.capaian.max' => 'Capaian maksimal 1000 karakter',
        'generateMode.required' => 'Pilih mode generate',
        'generateMode.in' => 'Mode generate tidak valid',
    ];

    public function mount()
    {
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
            $this->loadRombel();
        }

        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
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

    private function loadRombel(): void
    {
        if (!$this->tahunAjaranId) {
            $this->rombelList = [];
            return;
        }

        $this->rombelList = Rombel::whereHas('tahunAjaran', function ($q) {
            $q->where('tahun_ajaran_id', $this->tahunAjaranId);
        })
            ->orderBy('tingkat', 'asc')
            ->orderBy('nama', 'asc')
            ->get();
    }

    private function loadRombelData(): void
    {
        if (!$this->rombelId) {
            $this->rombel = null;
            $this->selectedRombelPengajarId = null;
            return;
        }

        $this->rombel = Rombel::with([
            'tahunAjaranKurikulum.tahunAjaran',
            'tahunAjaranKurikulum.kurikulum',
            'waliKelas',
            'jurusan'
        ])->find($this->rombelId);

        if (!$this->rombel) {
            $this->rombelId = null;
            $this->selectedRombelPengajarId = null;
            $this->dispatchError('Rombel tidak ditemukan.');
            return;
        }

        $this->selectedRombelPengajarId = $this->rombelId;
    }

    private function loadKokurikulerPelajar(): void
    {
        if (!$this->rombelId || !$this->semesterId) {
            $this->kokurikulerInput = [];
            return;
        }

        $kokurikulerExisting = Kokurikuler::where('tahun_ajaran_semester_id', $this->semesterId)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombelId);
            })
            ->get()
            ->keyBy('pelajar_id');

        $this->kokurikulerInput = [];

        foreach ($kokurikulerExisting as $pelajarId => $pelajarKokurikuler) {
            $this->kokurikulerInput[$pelajarId] = [
                'predikat' => $pelajarKokurikuler->predikat ?? null,
                'capaian' => $pelajarKokurikuler->capaian ?? '',
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
        $this->rombelId = null;
        $this->selectedRombelPengajarId = null;
        $this->rombelList = [];
        $this->kokurikulerInput = [];
        $this->rombel = null;

        $this->loadRombel();
        $this->resetPage();
    }

    public function updatedRombelId(): void
    {
        $this->kokurikulerInput = [];

        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
            $this->loadKokurikulerPelajar();
        } else {
            $this->rombel = null;
            $this->selectedRombelPengajarId = null;
        }

        $this->resetPage();
    }

    public function updatingSearchPelajar(): void
    {
        $this->resetPage();
    }

    // ========================================
    // QUERY METHODS
    // ========================================

    private function getPelajarQuery(): Builder
    {
        if (!$this->rombelId) {
            return RombelPelajar::whereNull('id');
        }

        $query = RombelPelajar::where('rombel_id', $this->rombelId)
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
    // KOKURIKULER MANAGEMENT METHODS
    // ========================================

    public function saveKokurikuler(): void
    {
        // Memperbarui rule in:A,B,C,D menjadi in:A,B,C
        $this->validate([
            'kokurikulerInput.*.predikat' => 'nullable|string|in:A,B,C',
            'kokurikulerInput.*.capaian' => 'nullable|string|max:1000',
        ]);

        if (!$this->validateKokurikulerContext()) {
            return;
        }

        $rombel = Rombel::find($this->rombelId);
        if (!$rombel) {
            $this->dispatchError('Data rombel tidak ditemukan.');
            return;
        }

        $validPelajarIds = $this->getValidPelajarIds();

        DB::beginTransaction();
        try {
            $result = $this->processKokurikulerSaving($validPelajarIds);

            DB::commit();

            if ($result['totalProcessed'] > 0) {
                $message = $this->buildSaveSuccessMessage($rombel->nama, $result);
                $this->dispatchSuccess($message);
                $this->loadKokurikulerPelajar();
            } else {
                $this->dispatch('swal:warning', [
                    'title' => 'Perhatian!',
                    'text' => 'Tidak ada data yang disimpan.',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollback();
            $this->logError('saving kokurikuler', $e);
            $this->dispatchError('Gagal menyimpan data kokurikuler: ' . $e->getMessage());
        }
    }

    private function processKokurikulerSaving(array $validPelajarIds): array
    {
        $savedCount = 0;
        $updatedCount = 0;
        $deletedCount = 0;
        $guruId = Auth::id();
        $tanggalInput = now();

        foreach ($this->kokurikulerInput as $pelajarId => $input) {
            if (!in_array($pelajarId, $validPelajarIds)) {
                continue;
            }

            $predikat = trim($input['predikat'] ?? '');
            $capaian = trim($input['capaian'] ?? '');

            $existingData = Kokurikuler::where('pelajar_id', $pelajarId)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->first();

            // Logic untuk menghapus jika predikat dikosongkan
            if (empty($predikat)) {
                if ($existingData) {
                    $existingData->delete();
                    $deletedCount++;
                }
                continue;
            }

            // Memastikan predikat yang diinput valid (A, B, atau C)
            if (!in_array($predikat, array_keys($this->predikatOptions))) {
                continue;
            }

            if ($existingData) {
                $existingData->update([
                    'predikat' => $predikat,
                    'capaian' => $capaian,
                    'guru_id' => $guruId,
                    'tanggal_input' => $tanggalInput,
                ]);
                $updatedCount++;
            } else {
                Kokurikuler::create([
                    'pelajar_id' => $pelajarId,
                    'guru_id' => $guruId,
                    'tahun_ajaran_semester_id' => $this->semesterId,
                    'predikat' => $predikat,
                    'capaian' => $capaian,
                    'tanggal_input' => $tanggalInput,
                ]);
                $savedCount++;
            }
        }

        return [
            'savedCount' => $savedCount,
            'updatedCount' => $updatedCount,
            'deletedCount' => $deletedCount,
            'totalProcessed' => $savedCount + $updatedCount + $deletedCount,
        ];
    }

    private function buildSaveSuccessMessage(string $rombelNama, array $result): string
    {
        $messages = [];
        if ($result['savedCount'] > 0) $messages[] = "{$result['savedCount']} baru";
        if ($result['updatedCount'] > 0) $messages[] = "{$result['updatedCount']} diperbarui";
        if ($result['deletedCount'] > 0) $messages[] = "{$result['deletedCount']} dihapus";

        return "Berhasil menyimpan data kokurikuler untuk {$rombelNama}: " . implode(", ", $messages);
    }

    public function confirmResetKokurikuler(): void
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Reset Input Kokurikuler?',
            'text' => 'Semua input kokurikuler akan dikosongkan (belum disimpan).',
            'confirmButtonText' => 'Ya, Reset',
            'nextEvent' => 'resetKokurikulerConfirmed',
        ]);
    }

    public function resetKokurikuler(): void
    {
        foreach ($this->kokurikulerInput as $pelajarId => $data) {
            $this->kokurikulerInput[$pelajarId] = [
                'predikat' => null,
                'capaian' => ''
            ];
        }

        $this->dispatch('swal:success', [
            'title' => 'Direset!',
            'text' => 'Semua kolom input kokurikuler telah dikosongkan.',
        ]);
    }

    public function deleteKokurikuler($pelajarId = null): void
    {
        $pelajarId = $this->extractPelajarId($pelajarId);

        if (!$this->validateDeleteContext($pelajarId)) {
            return;
        }

        try {
            $rombel = Rombel::find($this->rombelId);
            if (!$rombel) {
                throw new \Exception('Data rombel tidak ditemukan');
            }

            $validPelajar = RombelPelajar::where('rombel_id', $this->rombelId)
                ->where('pelajar_id', $pelajarId)
                ->exists();

            if (!$validPelajar) {
                throw new \Exception('Pelajar tidak ditemukan di rombel ini');
            }

            $deleted = Kokurikuler::where('pelajar_id', $pelajarId)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->delete();

            if ($deleted) {
                $this->handleSuccessfulDeletion($pelajarId);
            } else {
                $this->dispatchInfo('Data kokurikuler tidak ditemukan.');
            }
        } catch (\Exception $e) {
            $this->logError('deleting kokurikuler', $e, ['pelajar_id' => $pelajarId]);
            $this->dispatchError('Terjadi kesalahan saat menghapus data kokurikuler.');
        }
    }

    private function handleSuccessfulDeletion($pelajarId): void
    {
        if (isset($this->kokurikulerInput[$pelajarId])) {
            $this->kokurikulerInput[$pelajarId] = [
                'predikat' => null,
                'capaian' => ''
            ];
        }

        $this->loadKokurikulerPelajar();
        $this->dispatchSuccess('Data kokurikuler berhasil dihapus.');
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

        // Mengirimkan statistik yang diperbarui ke modal
        $this->dispatch('show-generate-modal', $statistics);
    }

    private function calculateGenerateStatistics(): array
    {
        // PERBAIKAN: Menghitung Capaian Kosong sebagai NULL atau String Kosong
        $countCapaianKosong = Kokurikuler::where('tahun_ajaran_semester_id', $this->semesterId)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombelId);
            })
            // KONDISI BARU
            ->where(function (Builder $query) {
                $query->whereNull('capaian')
                    ->orWhere('capaian', '');
            })
            ->count();

        return [
            'countPelajarWithKokurikuler' => Kokurikuler::where('tahun_ajaran_semester_id', $this->semesterId)
                ->whereIn('pelajar_id', function ($query) {
                    $query->select('pelajar_id')
                        ->from('rombel_pelajars')
                        ->where('rombel_id', $this->rombelId);
                })
                ->count(),
            'countCapaianKosong' => $countCapaianKosong, // MENGGUNAKAN HASIL PERBAIKAN
            'countTemplateAvailable' => TemplateKokurikulerCapaian::where('tahun_ajaran_semester_id', $this->semesterId)
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
            $this->loadKokurikulerPelajar();

            $this->dispatchGenerateResult($result);
        } catch (\Exception $e) {
            DB::rollback();
            $this->logError('generating capaian kokurikuler', $e);
            $this->dispatchError('Terjadi kesalahan saat generate capaian.');
        }
    }

    private function processCapaianGeneration(): array
    {
        $query = Kokurikuler::where('tahun_ajaran_semester_id', $this->semesterId)
            ->whereIn('pelajar_id', function ($q) {
                $q->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombelId);
            });

        if ($this->generateMode === 'empty') {
            // PERBAIKAN: Filter untuk NULL ATAU String Kosong
            $query->where(function (Builder $q) {
                $q->whereNull('capaian')
                    ->orWhere('capaian', '');
            });
        }

        $kokurikulerList = $query->get();
        $successCount = 0;
        $errorList = [];

        foreach ($kokurikulerList as $kokurikuler) {
            // Predikat D (Kurang) mungkin ada di database lama, tapi tidak ada template
            if (!isset($this->predikatOptions[$kokurikuler->predikat])) {
                continue;
            }

            $template = $this->getMatchingTemplate($kokurikuler->predikat);

            if ($template) {
                $kokurikuler->capaian = $template->deskripsi;
                $kokurikuler->save();
                $successCount++;
            } else {
                $pelajar = $kokurikuler->pelajar;
                $errorList[] = [
                    'nama' => $pelajar->nama_lengkap ?? 'N/A',
                    'predikat' => $this->predikatOptions[$kokurikuler->predikat] ?? $kokurikuler->predikat, // Tampilkan nama predikat baru
                ];
            }
        }

        return ['successCount' => $successCount, 'errorList' => $errorList];
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
            'text' => $errorMessage,
        ]);
    }

    private function buildGenerateErrorMessage(array $result): string
    {
        $message = "Generate selesai dengan catatan:\n";
        $message .= "- Berhasil: {$result['successCount']} pelajar\n";
        $message .= "- Gagal: " . count($result['errorList']) . " pelajar (tidak ada template yang cocok)\n\n";
        $message .= "Detail error:\n";

        foreach ($result['errorList'] as $error) {
            $message .= "- {$error['nama']} (predikat: {$error['predikat']}) - Template tidak ditemukan\n";
        }

        return $message;
    }

    private function getMatchingTemplate(string $predikat): ?TemplateKokurikulerCapaian
    {
        return TemplateKokurikulerCapaian::where('tahun_ajaran_semester_id', $this->semesterId)
            ->where('tingkat', $this->rombel->tingkat)
            ->where('predikat', $predikat)
            ->where('aktif', true)
            ->first();
    }

    // ========================================
    // UTILITY METHODS
    // ========================================

    private function resetFilters(): void
    {
        $this->semesterId = null;
        $this->rombelId = null;
        $this->selectedRombelPengajarId = null;
        $this->semesterList = [];
        $this->rombelList = [];
        $this->kokurikulerInput = [];
        $this->rombel = null;
    }

    private function getValidPelajarIds(): array
    {
        return RombelPelajar::where('rombel_id', $this->rombelId)
            ->pluck('pelajar_id')
            ->toArray();
    }

    private function extractPelajarId($pelajarId)
    {
        return is_array($pelajarId) && isset($pelajarId[0]) ? $pelajarId[0] : $pelajarId;
    }

    // ========================================
    // VALIDATION METHODS
    // ========================================

    private function validateKokurikulerContext(): bool
    {
        return $this->rombelId && $this->semesterId;
    }

    private function validateDeleteContext($pelajarId): bool
    {
        if (!$pelajarId || !$this->semesterId) {
            $this->dispatchError('ID Pelajar tidak ditemukan atau data tidak valid.', 'Gagal!');
            return false;
        }
        return true;
    }

    private function validateGenerateContext(): bool
    {
        if (!$this->rombelId || !$this->semesterId || !$this->rombel) {
            $this->dispatchError('Pastikan semua filter sudah dipilih.');
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

    private function logError(string $action, \Exception $e, array $context = []): void
    {
        Log::error("Error {$action}: " . $e->getMessage(), array_merge($context, [
            'rombel_id' => $this->rombelId,
            'semester_id' => $this->semesterId,
            'user_id' => Auth::id(),
        ]));
    }

    // ========================================
    // RENDER METHOD
    // ========================================

    public function render()
    {
        $pelajarData = collect();
        $pelajarPaginated = null;

        if ($this->rombelId && $this->semesterId) {
            $kokurikulerExist = Kokurikuler::where('tahun_ajaran_semester_id', $this->semesterId)
                ->whereIn('pelajar_id', function ($query) {
                    $query->select('pelajar_id')
                        ->from('rombel_pelajars')
                        ->where('rombel_id', $this->rombelId);
                })
                ->get()
                ->keyBy('pelajar_id');

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($kokurikulerExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                if (!isset($this->kokurikulerInput[$pelajarId])) {
                    $existingKokurikuler = $kokurikulerExist->get($pelajarId);
                    $this->kokurikulerInput[$pelajarId] = [
                        'predikat' => $existingKokurikuler->predikat ?? null,
                        'capaian' => $existingKokurikuler->capaian ?? '',
                    ];
                }

                $existingKokurikulerData = $kokurikulerExist->get($pelajarId);

                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id' => $pelajarId,
                    'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                    'nisn' => $rombelPelajar->pelajar->nisn,
                    'kokurikuler_existing' => $existingKokurikulerData,
                ];
            });
        }

        return view('livewire.admin.input-kokurikuler', [
            'pelajarData' => $pelajarData,
            'pelajarPaginated' => $pelajarPaginated, // Jika Anda perlu mengirim pagination links
        ]);
    }
}
