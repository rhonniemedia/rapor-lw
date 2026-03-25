<?php

namespace App\Livewire\Admin;

use App\Models\EkskulPelajar;
use App\Models\Ekstrakurikuler;
use App\Models\Pelajar;
use App\Models\Rombel;
use App\Models\RombelPelajar;
use App\Models\TahunAjaran;
use App\Models\TahunAjaranSemester;
use App\Models\TemplateEkstrakurikulerDeskripsi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class InputEkstrakurikuler extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filter Properties
    public $tahunAjaranId = null; // Filter untuk dropdown TA
    public $semesterMurniId = null; // Filter untuk dropdown Semester murni (dari tabel Semesters)

    // Properti utama untuk query (kombinasi TA & Semester)
    public $selectedTahunAjaranSemesterId = null;
    public $selectedRombelId = null;
    public $selectedEkstrakurikulerId = null;

    // Lists untuk dropdown
    public $tahunAjaranList = [];
    public $semesterMurniList = []; // Daftar semester murni (Ganjil/Genap)
    public $rombelList = [];
    public $ekstrakurikulerList = [];

    // Loaded data
    public $rombel = null;
    public $selectedEkstrakurikuler = null;
    public $semesterAktif = null;
    public $pembinaName = null;
    public $totalSiswa = 0;

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
        'tahunAjaranId' => ['except' => null],
        'semesterMurniId' => ['except' => null],
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

    protected $listeners = [
        'deleteEkskul',
        'confirmResetEkskul' => 'resetEkskul',
        'closeGenerateModal'
    ];

    public function mount()
    {
        $this->initializeFilters();
    }

    // ========================================
    // FILTER INITIALIZATION METHODS
    // ========================================

    private function initializeFilters(): void
    {
        // 1. Load lists (Tahun Ajaran & Semester Murni)
        $this->loadTahunAjaranList();
        $this->loadSemesterMurniList();

        // 2. Tentukan Tahun Ajaran Aktif jika belum ada di URL
        if (!$this->tahunAjaranId) {
            $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
            if ($activeTahunAjaran) {
                $this->tahunAjaranId = $activeTahunAjaran->id;
            }
        }

        // 3. Tentukan Semester Murni Aktif jika belum ada di URL
        if ($this->tahunAjaranId && !$this->semesterMurniId) {
            $activeTAS = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahunAjaranId)
                ->where('status', 'aktif')->first();
            if ($activeTAS) {
                $this->semesterMurniId = $activeTAS->semester_id;
            }
        }

        // 4. Set Selected TAS ID (menggabungkan TA dan Semester Murni)
        $this->setSelectedTahunAjaranSemesterId();

        // 5. Load Rombel dan Ekskul List
        if ($this->selectedTahunAjaranSemesterId) {
            $this->loadRombel();
        }
        $this->loadEkstrakurikulerList();

        // 6. Load Data Utama
        if ($this->selectedRombelId && $this->selectedTahunAjaranSemesterId && $this->selectedEkstrakurikulerId) {
            $this->loadRombelData();
            $this->loadSelectedEkstrakurikuler();
            $this->loadEkstrakurikulerPelajar();
        }
    }

    private function loadTahunAjaranList(): void
    {
        $this->tahunAjaranList = TahunAjaran::orderBy('status', 'desc')
            ->orderByDesc('tgl_mulai')
            ->get();
    }

    private function loadSemesterMurniList(): void
    {
        $this->semesterMurniList = \App\Models\Semester::orderBy('id')->get();
    }

    private function setSelectedTahunAjaranSemesterId(): void
    {
        if ($this->tahunAjaranId && $this->semesterMurniId) {
            $tas = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahunAjaranId)
                ->where('semester_id', $this->semesterMurniId)
                ->first();
            $this->selectedTahunAjaranSemesterId = $tas ? $tas->id : null;
        } else {
            $this->selectedTahunAjaranSemesterId = null;
        }
    }

    // ========================================
    // FILTER UPDATE HANDLERS
    // ========================================

    public function updatedTahunAjaranId(): void
    {
        $this->semesterMurniId = null;
        $this->updatedSemesterMurniId();
    }

    public function updatedSemesterMurniId(): void
    {
        $this->setSelectedTahunAjaranSemesterId();

        $this->resetPage();
        $this->selectedRombelId = null;
        $this->selectedEkstrakurikulerId = null;
        $this->rombel = null;
        $this->semesterAktif = null;
        $this->selectedEkstrakurikuler = null;
        $this->ekskulInput = [];
        $this->cachedEkskulExist = null;
        $this->searchPelajar = '';
        $this->totalSiswa = 0;

        if ($this->selectedTahunAjaranSemesterId) {
            $this->loadRombel();
            $this->loadRombelData();
        } else {
            $this->rombelList = [];
        }
    }

    public function updatedSelectedRombelId(): void
    {
        $this->resetPage();
        $this->searchPelajar = '';
        $this->ekskulInput = [];
        $this->cachedEkskulExist = null;
        $this->rombel = null;
        $this->totalSiswa = 0;

        if ($this->selectedRombelId) {
            $this->loadRombelData();
        }

        if ($this->selectedRombelId && $this->selectedTahunAjaranSemesterId && $this->selectedEkstrakurikulerId) {
            $this->loadSelectedEkstrakurikuler();
            $this->loadEkstrakurikulerPelajar();
        }
    }

    public function updatedSelectedEkstrakurikulerId(): void
    {
        $this->resetPage();
        $this->searchPelajar = '';
        $this->ekskulInput = [];
        $this->cachedEkskulExist = null;
        $this->selectedEkstrakurikuler = null;
        $this->pembinaName = null;

        if ($this->selectedEkstrakurikulerId) {
            $this->loadSelectedEkstrakurikuler();
        }

        if ($this->selectedRombelId && $this->selectedTahunAjaranSemesterId && $this->selectedEkstrakurikulerId) {
            $this->loadEkstrakurikulerPelajar();
        }
    }

    public function updatingSearchPelajar(): void
    {
        $this->resetPage();
    }

    // ========================================
    // DATA LOADING METHODS (Disamakan dengan file InputEkstrakurikuler.php)
    // ========================================

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

    private function loadEkstrakurikulerList(): void
    {
        $this->ekstrakurikulerList = Ekstrakurikuler::with('pembina')
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();
    }

    private function loadRombelData(): void
    {
        if (!$this->selectedRombelId) {
            $this->rombel = null;
            $this->semesterAktif = null;
            return;
        }

        $this->rombel = Rombel::with([
            'tahunAjaranKurikulum.tahunAjaran',
            'tahunAjaranKurikulum.kurikulum',
            'waliKelas',
            'jurusan'
        ])->find($this->selectedRombelId);

        // Load semester aktif based on the selected TahunAjaranSemesterId
        if ($this->selectedTahunAjaranSemesterId) {
            $this->semesterAktif = TahunAjaranSemester::with(['semester', 'tahunAjaran'])
                ->find($this->selectedTahunAjaranSemesterId);
        } else {
            $this->semesterAktif = null;
        }
    }

    private function loadSelectedEkstrakurikuler(): void
    {
        if (!$this->selectedEkstrakurikulerId) {
            $this->selectedEkstrakurikuler = null;
            $this->pembinaName = null;
            return;
        }

        $this->selectedEkstrakurikuler = Ekstrakurikuler::with('pembina')
            ->find($this->selectedEkstrakurikulerId);

        $this->pembinaName = $this->selectedEkstrakurikuler->pembina->name ?? 'N/A';
    }

    private function loadEkstrakurikulerPelajar(): void
    {
        if (!$this->selectedEkstrakurikulerId || !$this->selectedTahunAjaranSemesterId || !$this->selectedRombelId) {
            $this->ekskulInput = [];
            $this->cachedEkskulExist = null;
            return;
        }

        // Ambil data ekskul pelajar yang sudah ada
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

        // Mengisi input array dari cache
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

    // ========================================
    // SAVE METHODS (Menggunakan logika dari file InputEkstrakurikuler.php)
    // ========================================
    public function saveEkskul(): void
    {
        // ... (Logika saveEkskul() dari file InputEkstrakurikuler.php) ...
        if (!$this->selectedEkstrakurikulerId || !$this->selectedTahunAjaranSemesterId || !$this->selectedRombelId) {
            $this->dispatchError('Silakan lengkapi filter terlebih dahulu.');
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
            $this->loadEkstrakurikulerPelajar();

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
        // ... (Logika processEkskulSaving() dari file InputEkstrakurikuler.php) ...
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
        // ... (Logika buildSaveSuccessMessage() dari file InputEkstrakurikuler.php) ...
        $messages = [];
        if ($result['savedCount'] > 0) $messages[] = "{$result['savedCount']} baru";
        if ($result['updatedCount'] > 0) $messages[] = "{$result['updatedCount']} diperbarui";
        if ($result['deletedCount'] > 0) $messages[] = "{$result['deletedCount']} dihapus";

        return "Berhasil menyimpan data ekstrakurikuler '{$ekskulNama}': " . implode(", ", $messages);
    }

    public function resetEkskul(): void
    {
        // ... (Logika resetEkskul() dari file InputEkstrakurikuler.php) ...
        if ($this->cachedEkskulExist === null) {
            $this->loadEkstrakurikulerPelajar();
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
        // ... (Logika deleteEkskul() dari file InputEkstrakurikuler.php) ...
        if (is_array($pelajarId) && isset($pelajarId[0])) {
            $pelajarId = $pelajarId[0];
        }

        if (!$pelajarId || !$this->selectedEkstrakurikulerId || !$this->selectedTahunAjaranSemesterId) {
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
                $this->loadEkstrakurikulerPelajar();

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
    // GENERATE DESKRIPSI METHODS (Menggunakan logika dari file InputEkstrakurikuler.php)
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
        $query = EkskulPelajar::where('tahun_ajaran_semester_id', $this->selectedTahunAjaranSemesterId)
            ->where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->selectedRombelId);
            });

        $countPelajarWithEkskul = (clone $query)->count();

        $countDeskripsiKosong = (clone $query)->where(function (Builder $query) {
            $query->whereNull('deskripsi')
                ->orWhere('deskripsi', '');
        })->count();

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
            $this->loadEkstrakurikulerPelajar();

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
        // 1. Prioritas: Template spesifik untuk ekstrakurikuler ini
        $template = TemplateEkstrakurikulerDeskripsi::where('tahun_ajaran_semester_id', $this->selectedTahunAjaranSemesterId)
            ->where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
            ->where('predikat', $nilai)
            ->where('aktif', true)
            ->first();

        // 2. Fallback: Template umum (ekstrakurikuler_id IS NULL)
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

    // Tambahkan dispatchWarning untuk pesan error generate
    private function dispatchWarning(string $text, string $title = 'Perhatian!'): void
    {
        $this->dispatch('swal:warning', ['title' => $title, 'text' => $text]);
    }

    private function logError(string $action, \Exception $e, array $context = []): void
    {
        Log::error("Error {$action}: " . $e->getMessage(), array_merge($context, [
            'ekstrakurikuler_id' => $this->selectedEkstrakurikulerId,
            'semester_id' => $this->selectedTahunAjaranSemesterId ?? 'N/A',
            'rombel_id' => $this->selectedRombelId ?? 'N/A',
            'user_id' => Auth::id(),
        ]));
    }


    // ========================================
    // RENDER METHOD
    // ========================================

    public function render()
    {
        $pelajarData = collect();
        $this->totalSiswa = 0; // Reset

        if ($this->selectedRombelId) {
            $this->totalSiswa = RombelPelajar::where('rombel_id', $this->selectedRombelId)->count();
        }

        if ($this->selectedRombelId && $this->selectedEkstrakurikulerId && $this->selectedTahunAjaranSemesterId) {
            if ($this->cachedEkskulExist === null) {
                $this->loadEkstrakurikulerPelajar();
            }

            // Memastikan data terkait info box dimuat
            if ($this->rombel === null || $this->semesterAktif === null || $this->selectedEkstrakurikuler === null) {
                $this->loadRombelData();
                $this->loadSelectedEkstrakurikuler();
            }

            $ekskulExist = $this->cachedEkskulExist ?? collect();

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy(
                    Pelajar::select('nama_lengkap')
                        ->whereColumn('pelajars.id', 'rombel_pelajars.pelajar_id')
                )
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($ekskulExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                // Ambil data dari input atau cache jika belum ada di input
                if (!isset($this->ekskulInput[$pelajarId])) {
                    $existingEkskul = $ekskulExist->get($pelajarId);
                    $this->ekskulInput[$pelajarId] = [
                        'nilai' => $existingEkskul->nilai ?? null,
                        'deskripsi' => $existingEkskul->deskripsi ?? '',
                    ];
                }

                $existingEkskulData = $ekskulExist->get($pelajarId);
                $nilaiKey = $existingEkskulData->nilai ?? '-';

                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id' => $pelajarId,
                    'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                    'nisn' => $rombelPelajar->pelajar->nisn,
                    'ekskul_existing' => $existingEkskulData,
                    'nilai_label' => $this->predikatOptions[$nilaiKey] ?? $nilaiKey,
                ];
            });
        }

        return view('livewire.admin.input-ekstrakurikuler', [
            'pelajarData' => $pelajarData,
        ]);
    }
}
