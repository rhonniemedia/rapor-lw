<?php

namespace App\Livewire\Admin;

use App\Models\Rombel;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RombelPelajar;
use App\Models\Ekstrakurikuler;
use App\Models\EkskulPelajar;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\TemplateEkstrakurikulerDeskripsi;
use Illuminate\Database\Eloquent\Builder;

class InputEkstrakurikuler extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Properties - Filter
    public $tahunAjaranSemesterList = [];
    public $rombelList = [];
    public $ekstrakurikulerList = [];

    public $selectedTahunAjaranSemesterId = null;
    public $selectedRombelId = null;
    public $selectedEkstrakurikulerId = null;

    // Additional Info
    public $semesterInfo = null;
    public $rombelInfo = null;
    public $pembinaName = null;

    // Search & Pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // Input ekstrakurikuler
    public $ekskulInput = [];
    public $generateMode = 'empty';

    // Cache
    private $cachedEkskulExist = null;

    // Predikat Options
    public $predikatOptions = [
        'A' => 'Sangat Baik',
        'B' => 'Baik',
        'C' => 'Cukup',
    ];

    // Query string
    protected $queryString = [
        'selectedTahunAjaranSemesterId' => ['except' => null],
        'selectedRombelId' => ['except' => null],
        'selectedEkstrakurikulerId' => ['except' => null],
        'searchPelajar' => ['except' => ''],
    ];

    // Validation
    protected $rules = [
        'ekskulInput.*.nilai' => 'nullable|string|in:A,B,C',
        'ekskulInput.*.deskripsi' => 'nullable|string|max:1000',
        'generateMode' => 'required|in:empty,all',
    ];

    protected $messages = [
        'ekskulInput.*.nilai.in' => 'Nilai harus salah satu dari: A, B, C',
        'ekskulInput.*.deskripsi.string' => 'Deskripsi harus berupa teks',
        'ekskulInput.*.deskripsi.max' => 'Deskripsi maksimal 1000 karakter',
        'generateMode.required' => 'Pilih mode generate',
        'generateMode.in' => 'Mode generate tidak valid',
    ];

    public function mount()
    {
        $this->loadTahunAjaranSemesterList();
        $this->loadEkstrakurikulerList();

        if ($this->selectedTahunAjaranSemesterId) {
            $this->loadRombelList();
        }

        if ($this->selectedRombelId && $this->selectedEkstrakurikulerId) {
            $this->loadEkskulPelajar();
        }
    }

    protected $listeners = [
        'deleteEkskul',
        'confirmResetEkskul' => 'resetEkskul',
        'closeGenerateModal'
    ];

    private function loadTahunAjaranSemesterList(): void
    {
        $this->tahunAjaranSemesterList = TahunAjaranSemester::with(['tahunAjaran', 'semester'])
            ->orderByDesc('status')
            ->orderBy('id', 'desc')
            ->get();
    }

    private function loadRombelList(): void
    {
        if (!$this->selectedTahunAjaranSemesterId) {
            $this->rombelList = [];
            return;
        }

        $semester = TahunAjaranSemester::with('tahunAjaran')->find($this->selectedTahunAjaranSemesterId);

        if (!$semester) {
            $this->rombelList = [];
            return;
        }

        $this->rombelList = Rombel::with(['jurusan', 'waliKelas', 'tahunAjaranKurikulum.kurikulum'])
            ->whereHas('tahunAjaranKurikulum', function ($q) use ($semester) {
                $q->where('tahun_ajaran_id', $semester->tahun_ajaran_id);
            })
            ->orderBy('tingkat')
            ->orderBy('nomor')
            ->get();
    }

    private function loadEkstrakurikulerList(): void
    {
        $this->ekstrakurikulerList = Ekstrakurikuler::with('pembina')
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();
    }

    public function updatedSelectedTahunAjaranSemesterId(): void
    {
        $this->resetPage();
        $this->selectedRombelId = null;
        $this->selectedEkstrakurikulerId = null;
        $this->rombelInfo = null;
        $this->semesterInfo = null;
        $this->pembinaName = null;
        $this->searchPelajar = '';
        $this->ekskulInput = [];
        $this->cachedEkskulExist = null;

        $this->loadRombelList();
        $this->loadSemesterInfo();
    }

    public function updatedSelectedRombelId(): void
    {
        $this->resetPage();
        $this->searchPelajar = '';
        $this->ekskulInput = [];
        $this->cachedEkskulExist = null;

        $this->loadRombelInfo();

        if ($this->selectedEkstrakurikulerId) {
            $this->loadEkskulPelajar();
        }
    }

    public function updatedSelectedEkstrakurikulerId(): void
    {
        $this->resetPage();
        $this->searchPelajar = '';
        $this->ekskulInput = [];
        $this->cachedEkskulExist = null;

        $this->loadPembinaInfo();

        if ($this->selectedRombelId) {
            $this->loadEkskulPelajar();
        }
    }

    public function updatingSearchPelajar(): void
    {
        $this->resetPage();
    }

    private function loadSemesterInfo(): void
    {
        if (!$this->selectedTahunAjaranSemesterId) {
            $this->semesterInfo = null;
            return;
        }

        $this->semesterInfo = TahunAjaranSemester::with(['tahunAjaran', 'semester'])
            ->find($this->selectedTahunAjaranSemesterId);
    }

    private function loadRombelInfo(): void
    {
        if (!$this->selectedRombelId) {
            $this->rombelInfo = null;
            return;
        }

        $this->rombelInfo = Rombel::with([
            'jurusan',
            'waliKelas',
            'tahunAjaranKurikulum.kurikulum',
            'tahunAjaranKurikulum.tahunAjaran'
        ])->find($this->selectedRombelId);
    }

    private function loadPembinaInfo(): void
    {
        if (!$this->selectedEkstrakurikulerId) {
            $this->pembinaName = null;
            return;
        }

        $ekstrakurikuler = Ekstrakurikuler::with('pembina')
            ->find($this->selectedEkstrakurikulerId);

        $this->pembinaName = $ekstrakurikuler->pembina->name ?? 'N/A';
    }

    private function loadEkskulPelajar(): void
    {
        if (!$this->selectedEkstrakurikulerId || !$this->selectedTahunAjaranSemesterId || !$this->selectedRombelId) {
            $this->ekskulInput = [];
            $this->cachedEkskulExist = null;
            return;
        }

        $ekskulData = EkskulPelajar::where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
            ->where('tahun_ajaran_semester_id', $this->selectedTahunAjaranSemesterId)
            ->whereIn('pelajar_id', function ($q) {
                $q->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->selectedRombelId);
            })
            ->get()
            ->keyBy('pelajar_id');

        $this->cachedEkskulExist = $ekskulData;
        $this->ekskulInput = [];

        foreach ($ekskulData as $pelajarId => $data) {
            $this->ekskulInput[$pelajarId] = [
                'nilai' => $data->nilai ?? null,
                'deskripsi' => $data->deskripsi ?? '',
            ];
        }
    }

    private function getPelajarQuery(): Builder
    {
        if (!$this->selectedRombelId) {
            return RombelPelajar::whereNull('id');
        }

        $query = RombelPelajar::where('rombel_id', $this->selectedRombelId)
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
        if (!$this->validateSaveContext()) {
            return;
        }

        try {
            $this->validate([
                'ekskulInput.*.nilai' => 'nullable|string|in:A,B,C',
                'ekskulInput.*.deskripsi' => 'nullable|string|max:1000',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            $this->dispatchError('Periksa input Anda. ' . implode(', ', array_map(fn($err) => implode(', ', $err), $e->errors())), 'Validasi Gagal!');
            return;
        }

        $validPelajarIds = RombelPelajar::where('rombel_id', $this->selectedRombelId)
            ->pluck('pelajar_id')
            ->toArray();

        DB::beginTransaction();
        try {
            $result = $this->processEkskulSaving($validPelajarIds);

            DB::commit();

            $this->ekskulInput = [];
            $this->cachedEkskulExist = null;
            $this->loadEkskulPelajar();

            if ($result['totalProcessed'] > 0) {
                $ekstrakurikuler = Ekstrakurikuler::find($this->selectedEkstrakurikulerId);
                $message = $this->buildSaveSuccessMessage($ekstrakurikuler->nama ?? 'Ekstrakurikuler', $result);
                $this->dispatchSuccess($message);
            } else {
                $this->dispatchInfo('Tidak ada data baru yang disimpan.');
            }
        } catch (\Exception $e) {
            DB::rollback();

            $this->logError('saving ekskul', $e);
            $this->dispatchError('Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    private function processEkskulSaving(array $validPelajarIds): array
    {
        $savedCount = 0;
        $updatedCount = 0;
        $deletedCount = 0;
        $userId = Auth::id();

        foreach ($this->ekskulInput as $pelajarId => $input) {
            if (!in_array($pelajarId, $validPelajarIds)) {
                continue;
            }

            $nilai = trim($input['nilai'] ?? '');
            $deskripsi = trim($input['deskripsi'] ?? '');

            $existingData = EkskulPelajar::where('pelajar_id', $pelajarId)
                ->where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
                ->where('tahun_ajaran_semester_id', $this->selectedTahunAjaranSemesterId)
                ->first();

            if (empty($nilai) && empty($deskripsi)) {
                if ($existingData) {
                    $existingData->delete();
                    $deletedCount++;
                }
                continue;
            }

            if (!in_array($nilai, array_keys($this->predikatOptions)) && !empty($nilai)) {
                continue;
            }

            if ($existingData) {
                $existingData->update([
                    'nilai' => $nilai,
                    'deskripsi' => $deskripsi,
                    'updated_by' => $userId,
                ]);
                $updatedCount++;
            } else {
                if (!empty($nilai)) {
                    EkskulPelajar::create([
                        'pelajar_id' => $pelajarId,
                        'ekstrakurikuler_id' => $this->selectedEkstrakurikulerId,
                        'tahun_ajaran_semester_id' => $this->selectedTahunAjaranSemesterId,
                        'nilai' => $nilai,
                        'deskripsi' => $deskripsi,
                        'created_by' => $userId,
                    ]);
                    $savedCount++;
                }
            }
        }

        return [
            'savedCount' => $savedCount,
            'updatedCount' => $updatedCount,
            'deletedCount' => $deletedCount,
            'totalProcessed' => $savedCount + $updatedCount + $deletedCount,
        ];
    }

    private function buildSaveSuccessMessage(string $ekskulNama, array $result): string
    {
        $messages = [];
        if ($result['savedCount'] > 0) $messages[] = "{$result['savedCount']} baru";
        if ($result['updatedCount'] > 0) $messages[] = "{$result['updatedCount']} diperbarui";
        if ($result['deletedCount'] > 0) $messages[] = "{$result['deletedCount']} dihapus";

        return "Berhasil menyimpan data ekstrakurikuler '{$ekskulNama}': " . implode(", ", $messages);
    }

    public function resetEkskul(): void
    {
        if ($this->cachedEkskulExist === null) {
            $this->loadEkskulPelajar();
        }

        foreach ($this->cachedEkskulExist as $pelajarId => $data) {
            $this->ekskulInput[$pelajarId] = [
                'nilai' => $data->nilai ?? null,
                'deskripsi' => $data->deskripsi ?? '',
            ];
        }

        foreach ($this->ekskulInput as $pelajarId => $input) {
            if (!isset($this->cachedEkskulExist[$pelajarId])) {
                $this->ekskulInput[$pelajarId] = [
                    'nilai' => null,
                    'deskripsi' => ''
                ];
            }
        }

        $this->dispatchInfo('Input telah dikembalikan ke data tersimpan.');
    }

    public function deleteEkskul($pelajarId = null): void
    {
        if (is_array($pelajarId) && isset($pelajarId[0])) {
            $pelajarId = $pelajarId[0];
        }

        if (!$pelajarId || !$this->validateSaveContext()) {
            $this->dispatchError('ID Pelajar tidak ditemukan atau data tidak valid.', 'Gagal!');
            return;
        }

        try {
            $deleted = EkskulPelajar::where('pelajar_id', $pelajarId)
                ->where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
                ->where('tahun_ajaran_semester_id', $this->selectedTahunAjaranSemesterId)
                ->delete();

            if ($deleted) {
                if (isset($this->ekskulInput[$pelajarId])) {
                    $this->ekskulInput[$pelajarId] = [
                        'nilai' => null,
                        'deskripsi' => ''
                    ];
                }

                $this->cachedEkskulExist = null;
                $this->loadEkskulPelajar();

                $this->dispatchSuccess('Data ekstrakurikuler berhasil dihapus.');
            } else {
                $this->dispatchInfo('Data tidak ditemukan.');
            }
        } catch (\Exception $e) {
            $this->logError('deleting ekskul', $e, ['pelajar_id' => $pelajarId]);
            $this->dispatchError('Terjadi kesalahan saat menghapus data.');
        }
    }

    // ========================================
    // GENERATE DESKRIPSI METHODS
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
        $countPelajarWithEkskul = EkskulPelajar::where('tahun_ajaran_semester_id', $this->selectedTahunAjaranSemesterId)
            ->where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->selectedRombelId);
            })
            ->count();

        $countDeskripsiKosong = EkskulPelajar::where('tahun_ajaran_semester_id', $this->selectedTahunAjaranSemesterId)
            ->where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->selectedRombelId);
            })
            ->where(function (Builder $query) {
                $query->whereNull('deskripsi')
                    ->orWhere('deskripsi', '');
            })
            ->count();

        return [
            'countPelajarWithEkskul' => $countPelajarWithEkskul,
            'countDeskripsiKosong' => $countDeskripsiKosong,
            'countTemplateAvailable' => TemplateEkstrakurikulerDeskripsi::where('tahun_ajaran_semester_id', $this->selectedTahunAjaranSemesterId)
                ->where(function ($q) {
                    $q->where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
                        ->orWhereNull('ekstrakurikuler_id');
                })
                ->where('aktif', true)
                ->count(),
        ];
    }

    private function validateGenerateStatistics(array $statistics): bool
    {
        if ($statistics['countTemplateAvailable'] === 0) {
            $this->dispatchError('Belum ada template deskripsi untuk ekstrakurikuler ini.', 'Template Tidak Ditemukan!');
            return false;
        }

        if ($statistics['countPelajarWithEkskul'] === 0) {
            $this->dispatchError('Belum ada pelajar yang memiliki data ekstrakurikuler tersimpan.', 'Tidak Ada Data!');
            return false;
        }

        return true;
    }

    public function closeGenerateModal(): void
    {
        $this->dispatch('hide-generate-modal');
        $this->generateMode = 'empty';
    }

    public function generateDeskripsi(): void
    {
        $this->validate([
            'generateMode' => 'required|in:empty,all',
        ]);

        if (!$this->validateGenerateContext()) {
            return;
        }

        DB::beginTransaction();
        try {
            $result = $this->processDeskripsiGeneration();

            DB::commit();

            $this->closeGenerateModal();
            $this->cachedEkskulExist = null;
            $this->loadEkskulPelajar();

            $this->dispatchGenerateResult($result);
        } catch (\Exception $e) {
            DB::rollback();
            $this->logError('generating deskripsi ekstrakurikuler', $e);
            $this->dispatchError('Terjadi kesalahan saat generate deskripsi.');
        }
    }

    private function processDeskripsiGeneration(): array
    {
        $query = EkskulPelajar::where('tahun_ajaran_semester_id', $this->selectedTahunAjaranSemesterId)
            ->where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
            ->whereIn('pelajar_id', function ($q) {
                $q->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->selectedRombelId);
            });

        if ($this->generateMode === 'empty') {
            $query->where(function (Builder $q) {
                $q->whereNull('deskripsi')
                    ->orWhere('deskripsi', '');
            });
        }

        $ekskulList = $query->get();
        $successCount = 0;
        $errorList = [];

        foreach ($ekskulList as $ekskul) {
            if (!isset($this->predikatOptions[$ekskul->nilai])) {
                continue;
            }

            $template = $this->getMatchingTemplate($ekskul->nilai);

            if ($template) {
                $deskripsi = $template->deskripsi;

                if ($template->gunakan_placeholder) {
                    $deskripsi = $this->replacePlaceholders($deskripsi, $ekskul);
                }

                $ekskul->deskripsi = $deskripsi;
                $ekskul->updated_by = Auth::id();
                $ekskul->save();
                $successCount++;
            } else {
                $pelajar = $ekskul->pelajar;
                $errorList[] = [
                    'nama' => $pelajar->nama_lengkap ?? 'N/A',
                    'nilai' => $this->predikatOptions[$ekskul->nilai] ?? $ekskul->nilai,
                ];
            }
        }

        return ['successCount' => $successCount, 'errorList' => $errorList];
    }

    private function getMatchingTemplate(string $nilai): ?TemplateEkstrakurikulerDeskripsi
    {
        $template = TemplateEkstrakurikulerDeskripsi::where('tahun_ajaran_semester_id', $this->selectedTahunAjaranSemesterId)
            ->where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
            ->where('predikat', $nilai)
            ->where('aktif', true)
            ->first();

        if (!$template) {
            $template = TemplateEkstrakurikulerDeskripsi::where('tahun_ajaran_semester_id', $this->selectedTahunAjaranSemesterId)
                ->whereNull('ekstrakurikuler_id')
                ->where('predikat', $nilai)
                ->where('aktif', true)
                ->first();
        }

        return $template;
    }

    private function replacePlaceholders(string $deskripsi, $ekskul): string
    {
        $ekstrakurikuler = Ekstrakurikuler::find($this->selectedEkstrakurikulerId);
        $pelajar = $ekskul->pelajar;

        $placeholders = [
            '[NAMA_PELAJAR]' => $pelajar->nama_lengkap ?? '',
            '[NAMA_EKSTRAKURIKULER]' => $ekstrakurikuler->nama ?? '',
            '[NILAI]' => $this->predikatOptions[$ekskul->nilai] ?? $ekskul->nilai,
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $deskripsi);
    }

    private function dispatchGenerateResult(array $result): void
    {
        if (empty($result['errorList'])) {
            $this->dispatchSuccess("Berhasil generate deskripsi untuk {$result['successCount']} pelajar.");
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
            $message .= "- {$error['nama']} (nilai: {$error['nilai']}) - Template tidak ditemukan\n";
        }

        return $message;
    }

    // ========================================
    // VALIDATION METHODS
    // ========================================

    private function validateSaveContext(): bool
    {
        if (!$this->selectedEkstrakurikulerId || !$this->selectedTahunAjaranSemesterId || !$this->selectedRombelId) {
            $this->dispatchError('Silakan pilih semua filter terlebih dahulu.');
            return false;
        }
        return true;
    }

    private function validateGenerateContext(): bool
    {
        if (!$this->selectedEkstrakurikulerId || !$this->selectedTahunAjaranSemesterId || !$this->selectedRombelId) {
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
            'ekstrakurikuler_id' => $this->selectedEkstrakurikulerId,
            'rombel_id' => $this->selectedRombelId,
            'semester_id' => $this->selectedTahunAjaranSemesterId ?? 'N/A',
            'user_id' => Auth::id(),
        ]));
    }

    // ========================================
    // RENDER METHOD
    // ========================================

    public function render()
    {
        $pelajarData = collect();
        $totalSiswa = 0;

        if ($this->selectedRombelId) {
            $totalSiswa = RombelPelajar::where('rombel_id', $this->selectedRombelId)->count();
        }

        if ($this->selectedRombelId && $this->selectedEkstrakurikulerId && $this->selectedTahunAjaranSemesterId) {
            if ($this->cachedEkskulExist === null) {
                $this->loadEkskulPelajar();
            }

            $ekskulExist = $this->cachedEkskulExist ?? collect();

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($ekskulExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                // 1. Memastikan input array terisi dengan nilai tersimpan jika belum diubah
                if (!isset($this->ekskulInput[$pelajarId])) {
                    $existingEkskul = $ekskulExist->get($pelajarId);
                    $this->ekskulInput[$pelajarId] = [
                        'nilai' => $existingEkskul->nilai ?? null,
                        'deskripsi' => $existingEkskul->deskripsi ?? '',
                    ];
                }

                $existingEkskulData = $ekskulExist->get($pelajarId);

                // 2. Mengembalikan object data pelajar lengkap
                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id' => $pelajarId,
                    'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                    'nisn' => $rombelPelajar->pelajar->nisn,
                    'ekskul_existing' => $existingEkskulData,
                ];
            });
        }

        return view('livewire.admin.input-ekstrakurikuler', [
            'pelajarData' => $pelajarData,
            'totalSiswa' => $totalSiswa,
        ]);
    }
}
