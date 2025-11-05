<?php

namespace App\Livewire\Admin;

use App\Models\Rombel;
use Livewire\Component;
use App\Models\TahunAjaran;
use Livewire\WithPagination;
use App\Models\EkskulPelajar;
use App\Models\RombelPelajar;
use App\Models\Ekstrakurikuler;
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

    // 🔹 Properti filter
    public $tahunAjaranId = null;
    public $semesterId = null;
    public $rombelId = null;
    public $ekstrakurikulerId = null;
    public $selectedRombelPengajarId = null; // Dipertahankan untuk konsistensi filter logic

    // 🔹 Properti utama
    public $rombel;
    public $selectedEkstrakurikuler;
    public $generateMode = 'empty';

    // 🔹 Properti pencarian & pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // 🔹 Data dropdown
    public $tahunAjaranList = [];
    public $semesterList = [];
    public $rombelList = [];
    public $ekstrakurikulerList = [];

    // 🔹 Nilai options
    public $nilaiOptions = [
        'A' => 'Sangat Baik',
        'B' => 'Baik',
        'C' => 'Cukup',
        'D' => 'Kurang'
    ];

    // 🔹 Input data ekstrakurikuler
    public $ekstrakurikulerInput = [];

    // 🔹 Cache untuk optimasi
    private $cachedEkstrakurikulerExist = null;

    // 🔹 Query string
    protected $queryString = [
        'tahunAjaranId' => ['except' => null],
        'semesterId' => ['except' => null],
        'rombelId' => ['except' => null],
        'ekstrakurikulerId' => ['except' => null],
        'searchPelajar' => ['except' => ''],
    ];

    // 🔹 Event listener
    protected $listeners = [
        // Menyesuaikan listener event untuk Ekstrakurikuler
        'saveEkstrakurikulerConfirmed' => 'saveEkstrakurikuler',
        'resetEkstrakurikulerConfirmed' => 'resetEkstrakurikuler',
        'deleteEkstrakurikuler' => 'deleteEkstrakurikuler',
    ];

    // 🔹 Validation rules (Ditambahkan validasi generate mode)
    protected $rules = [
        'ekstrakurikulerInput.*.nilai' => 'nullable|string|in:A,B,C,D',
        'ekstrakurikulerInput.*.deskripsi' => 'nullable|string|max:500',
        'generateMode' => 'required|in:empty,all',
    ];

    protected $messages = [
        'ekstrakurikulerInput.*.nilai.in' => 'Nilai harus salah satu dari: A, B, C, D',
        'ekstrakurikulerInput.*.deskripsi.string' => 'Deskripsi harus berupa teks',
        'ekstrakurikulerInput.*.deskripsi.max' => 'Deskripsi maksimal 500 karakter',
        'generateMode.required' => 'Pilih mode generate',
        'generateMode.in' => 'Mode generate tidak valid',
    ];

    public function mount()
    {
        $this->loadTahunAjaran();
        $this->initializeDefaultFilters();
        $this->loadEkstrakurikulerList();

        if ($this->rombelId && $this->semesterId && $this->ekstrakurikulerId) {
            $this->loadRombelData();
            $this->loadSelectedEkstrakurikuler();
            $this->loadEkstrakurikulerPelajar();
        }
    }

    private function initializeDefaultFilters(): void
    {
        // Set default tahun ajaran aktif
        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        if ($activeTahunAjaran && !$this->tahunAjaranId) {
            $this->tahunAjaranId = $activeTahunAjaran->id;
        }

        // Load semester jika tahun ajaran sudah dipilih
        if ($this->tahunAjaranId) {
            $this->loadSemester();

            // Set default semester aktif
            $activeSemester = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahunAjaranId)
                ->where('status', 'aktif')
                ->first();
            if ($activeSemester && !$this->semesterId) {
                $this->semesterId = $activeSemester->id;
            }
        }

        // Load rombel jika tahun ajaran dan semester sudah dipilih
        if ($this->tahunAjaranId && $this->semesterId) {
            $this->loadRombel();
        }
    }


    // 🔹 Load data tahun ajaran
    private function loadTahunAjaran(): void
    {
        $this->tahunAjaranList = TahunAjaran::orderBy('tgl_mulai', 'desc')->get();
    }

    // 🔹 Load data semester
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

    // 🔹 Load data rombel berdasarkan tahun ajaran
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

    // 🔹 Load data rombel yang dipilih
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

    // 🔹 Load list ekstrakurikuler aktif
    private function loadEkstrakurikulerList(): void
    {
        $this->ekstrakurikulerList = Ekstrakurikuler::with('pembina')
            ->where('status', 'aktif')
            ->orderBy('nama', 'asc')
            ->get();
    }

    // 🔹 Load ekstrakurikuler yang dipilih
    private function loadSelectedEkstrakurikuler(): void
    {
        if (!$this->ekstrakurikulerId) {
            $this->selectedEkstrakurikuler = null;
            return;
        }

        $this->selectedEkstrakurikuler = Ekstrakurikuler::with('pembina')->find($this->ekstrakurikulerId);

        if (!$this->selectedEkstrakurikuler) {
            $this->ekstrakurikulerId = null;
            $this->dispatchError('Ekstrakurikuler tidak ditemukan.');
        }
    }

    // 🔹 Handler saat tahun ajaran berubah
    public function updatedTahunAjaranId(): void
    {
        $this->resetFilters();
        $this->loadSemester();

        // Auto-select semester aktif
        $activeSemester = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahunAjaranId)
            ->where('status', 'aktif')
            ->first();
        if ($activeSemester) {
            $this->semesterId = $activeSemester->id;
            $this->updatedSemesterId();
        }

        $this->resetPage();
    }

    // 🔹 Handler saat semester berubah
    public function updatedSemesterId(): void
    {
        $this->rombelId = null;
        $this->ekstrakurikulerId = null;
        $this->selectedRombelPengajarId = null;
        $this->rombelList = [];
        $this->ekstrakurikulerInput = [];
        $this->rombel = null;
        $this->selectedEkstrakurikuler = null;
        $this->cachedEkstrakurikulerExist = null;
        $this->generateMode = 'empty';

        $this->loadRombel();
        $this->resetPage();
    }

    // 🔹 Handler saat rombel berubah
    public function updatedRombelId(): void
    {
        $this->ekstrakurikulerId = null;
        $this->ekstrakurikulerInput = [];
        $this->selectedEkstrakurikuler = null;
        $this->cachedEkstrakurikulerExist = null;
        $this->generateMode = 'empty';

        if ($this->rombelId && $this->semesterId) {
            $this->loadRombelData();
        } else {
            $this->rombel = null;
            $this->selectedRombelPengajarId = null;
        }

        $this->resetPage();
    }

    // 🔹 Handler saat ekstrakurikuler berubah
    public function updatedEkstrakurikulerId(): void
    {
        $this->ekstrakurikulerInput = [];
        $this->cachedEkstrakurikulerExist = null;
        $this->generateMode = 'empty';

        if ($this->ekstrakurikulerId && $this->rombelId && $this->semesterId) {
            $this->loadSelectedEkstrakurikuler();
            $this->loadEkstrakurikulerPelajar();
        } else {
            $this->selectedEkstrakurikuler = null;
        }

        $this->resetPage();
    }

    // 🔹 Reset pagination saat search berubah
    public function updatingSearchPelajar(): void
    {
        $this->resetPage();
    }

    // 🔹 Helper method untuk reset filters
    private function resetFilters(): void
    {
        $this->semesterId = null;
        $this->rombelId = null;
        $this->ekstrakurikulerId = null;
        $this->selectedRombelPengajarId = null;
        $this->semesterList = [];
        $this->rombelList = [];
        $this->ekstrakurikulerInput = [];
        $this->rombel = null;
        $this->selectedEkstrakurikuler = null;
        $this->cachedEkstrakurikulerExist = null;
        $this->generateMode = 'empty';
    }

    // 🔹 Load data ekstrakurikuler pelajar
    private function loadEkstrakurikulerPelajar(): void
    {
        if (!$this->rombelId || !$this->semesterId || !$this->ekstrakurikulerId) {
            $this->ekstrakurikulerInput = [];
            $this->cachedEkstrakurikulerExist = null;
            return;
        }

        // Cache ekstrakurikuler untuk menghindari query duplikat
        $this->cachedEkstrakurikulerExist = EkskulPelajar::where('tahun_ajaran_semester_id', $this->semesterId)
            ->where('ekstrakurikuler_id', $this->ekstrakurikulerId)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombelId);
            })
            ->get()
            ->keyBy('pelajar_id');

        // Reset array input ekstrakurikuler
        $this->ekstrakurikulerInput = [];

        // Populate ekstrakurikuler input dengan data existing
        foreach ($this->cachedEkstrakurikulerExist as $pelajarId => $pelajarEkskul) {
            $this->ekstrakurikulerInput[$pelajarId] = [
                'nilai' => $pelajarEkskul->nilai ?? null,
                'deskripsi' => $pelajarEkskul->deskripsi ?? '',
            ];
        }
    }

    // 🔹 Get query data pelajar dengan filter
    private function getPelajarQuery(): Builder
    {
        if (!$this->rombelId) {
            return RombelPelajar::whereNull('id'); // Return empty query
        }

        $query = RombelPelajar::where('rombel_id', $this->rombelId)
            ->with(['pelajar']);

        // Filter pencarian
        if (!empty($this->searchPelajar)) {
            $search = $this->searchPelajar;
            $query->whereHas('pelajar', function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    // 🔹 Simpan ekstrakurikuler (Menggunakan confirmSaveNilai style)
    public function confirmSaveEkstrakurikuler(): void
    {
        if (!$this->validateContext()) {
            return;
        }

        $this->validate();

        $this->dispatch('swal:confirm', [
            'title' => 'Simpan Nilai Ekstrakurikuler?',
            'text' => 'Semua nilai dan deskripsi yang diinput akan disimpan.',
            'nextEvent' => 'saveEkstrakurikulerConfirmed',
        ]);
    }

    public function saveEkstrakurikuler(): void
    {
        if (!$this->validateContext()) {
            return;
        }

        $rombel = Rombel::find($this->rombelId);
        $ekstrakurikuler = Ekstrakurikuler::find($this->ekstrakurikulerId);

        if (!$rombel || !$ekstrakurikuler) {
            $this->dispatchError('Filter data tidak lengkap atau tidak valid.');
            return;
        }

        // Security: Validasi pelajar_id yang valid
        $validPelajarIds = RombelPelajar::where('rombel_id', $this->rombelId)
            ->pluck('pelajar_id')
            ->toArray();

        DB::beginTransaction();
        try {
            $savedCount = 0;
            $updatedCount = 0;
            $deletedCount = 0;
            $userId = Auth::id();

            foreach ($this->ekstrakurikulerInput as $pelajarId => $input) {
                // Security check: pastikan pelajar ada di rombel ini
                if (!in_array($pelajarId, $validPelajarIds)) {
                    continue;
                }

                $nilai = trim($input['nilai'] ?? '');
                $deskripsi = trim($input['deskripsi'] ?? '');

                // Cek apakah ada data yang sudah ada
                $existingData = EkskulPelajar::where('pelajar_id', $pelajarId)
                    ->where('tahun_ajaran_semester_id', $this->semesterId)
                    ->where('ekstrakurikuler_id', $this->ekstrakurikulerId)
                    ->first();

                // Logic Delete: Jika nilai kosong, hapus data eksisting
                if (empty($nilai)) {
                    if ($existingData) {
                        $existingData->delete();
                        $deletedCount++;
                    }
                    continue;
                }

                if (!in_array($nilai, array_keys($this->nilaiOptions))) {
                    continue;
                }

                if ($existingData) {
                    // Update data yang sudah ada
                    $existingData->update([
                        'nilai' => $nilai,
                        'deskripsi' => $deskripsi,
                        'updated_by' => $userId,
                    ]);
                    $updatedCount++;
                } else {
                    // Buat data baru
                    EkskulPelajar::create([
                        'pelajar_id' => $pelajarId,
                        'ekstrakurikuler_id' => $this->ekstrakurikulerId,
                        'tahun_ajaran_semester_id' => $this->semesterId,
                        'nilai' => $nilai,
                        'deskripsi' => $deskripsi,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]);
                    $savedCount++;
                }
            }

            DB::commit();

            $totalProcessed = $savedCount + $updatedCount + $deletedCount;
            if ($totalProcessed > 0) {
                $messages = [];
                if ($savedCount > 0) $messages[] = "{$savedCount} baru";
                if ($updatedCount > 0) $messages[] = "{$updatedCount} diperbarui";
                if ($deletedCount > 0) $messages[] = "{$deletedCount} dihapus";

                $message = "Berhasil menyimpan data ekstrakurikuler {$ekstrakurikuler->nama} untuk {$rombel->nama}: " . implode(", ", $messages);

                $this->dispatchSuccess($message);

                $this->cachedEkstrakurikulerExist = null;
                $this->loadEkstrakurikulerPelajar();
            } else {
                $this->dispatchWarning('Tidak ada data yang disimpan.', 'Perhatian!');
            }
        } catch (\Exception $e) {
            DB::rollback();

            $this->logError('saving ekstrakurikuler', $e);

            $this->dispatchError('Gagal menyimpan data ekstrakurikuler: ' . $e->getMessage());
        }
    }

    // 🔹 Konfirmasi reset ekstrakurikuler
    public function confirmResetEkstrakurikuler(): void
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Reset Input Ekstrakurikuler?',
            'text' => 'Semua input ekstrakurikuler akan dikosongkan (belum disimpan).',
            'confirmButtonText' => 'Ya, Reset',
            'nextEvent' => 'resetEkstrakurikulerConfirmed',
        ]);
    }

    // 🔹 Reset semua input ekstrakurikuler
    public function resetEkstrakurikuler(): void
    {
        $this->ekstrakurikulerInput = array_map(fn($input) => [
            'nilai' => null,
            'deskripsi' => ''
        ], $this->ekstrakurikulerInput);

        $this->dispatchInfo('Semua kolom input ekstrakurikuler telah dikosongkan.', 'Direset!');
    }

    // 🔹 Delete ekstrakurikuler
    public function deleteEkstrakurikuler($pelajarId = null): void
    {
        $pelajarId = $this->extractPelajarId($pelajarId);

        if (!$this->validateDeleteContext($pelajarId)) {
            return;
        }

        try {
            $ekstrakurikuler = Ekstrakurikuler::find($this->ekstrakurikulerId);
            if (!$ekstrakurikuler) {
                throw new \Exception('Data ekstrakurikuler tidak ditemukan');
            }

            $deleted = EkskulPelajar::where('pelajar_id', $pelajarId)
                ->where('ekstrakurikuler_id', $this->ekstrakurikulerId)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->delete();

            if ($deleted) {
                // Hapus dari input list dan reload
                if (isset($this->ekstrakurikulerInput[$pelajarId])) {
                    unset($this->ekstrakurikulerInput[$pelajarId]);
                }
                $this->loadEkstrakurikulerPelajar();
                $this->dispatchSuccess('Data ekstrakurikuler berhasil dihapus.');
            } else {
                $this->dispatchInfo('Data ekstrakurikuler tidak ditemukan.');
            }
        } catch (\Exception $e) {
            $this->logError('deleting ekstrakurikuler', $e, ['pelajar_id' => $pelajarId]);
            $this->dispatchError('Terjadi kesalahan saat menghapus data ekstrakurikuler.');
        }
    }


    // ========================================
    // GENERATE DESKRIPSI METHODS (REFACTORED & DEBUGGED)
    // ========================================

    public function openGenerateModal(): void
    {
        if (!$this->validateContext()) {
            return;
        }

        // Memastikan ekstrakurikuler dipilih sebelum menghitung statistik
        if (!$this->selectedEkstrakurikuler) {
            $this->loadSelectedEkstrakurikuler();
        }

        $statistics = $this->calculateGenerateStatistics();

        if (!$this->validateGenerateStatistics($statistics)) {
            return;
        }

        // Mengirimkan statistik ke modal
        $this->dispatch('show-generate-ekskul-modal', $statistics);
    }

    private function calculateGenerateStatistics(): array
    {
        // Query Dasar
        $baseQuery = EkskulPelajar::where('tahun_ajaran_semester_id', $this->semesterId)
            ->where('ekstrakurikuler_id', $this->ekstrakurikulerId)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombelId);
            });

        // Hitung Ekskul Pelajar yang sudah punya nilai (predikat)
        $countPelajarWithNilai = (clone $baseQuery)
            ->whereNotNull('nilai')
            ->count();

        // Hitung Deskripsi yang Kosong (NULL atau String Kosong)
        $countDeskripsiKosong = (clone $baseQuery)
            ->whereNotNull('nilai')
            ->where(function (Builder $query) {
                $query->whereNull('deskripsi')
                    ->orWhere('deskripsi', '');
            })
            ->count();

        // Hitung Template yang tersedia
        $countTemplateAvailable = TemplateEkstrakurikulerDeskripsi::where('tahun_ajaran_semester_id', $this->semesterId)
            ->where('aktif', true)
            // Cek template khusus ekstrakurikuler INI atau template umum (ekstrakurikuler_id = NULL)
            ->where(function (Builder $query) {
                $query->where('ekstrakurikuler_id', $this->ekstrakurikulerId)
                    ->orWhereNull('ekstrakurikuler_id');
            })
            ->distinct()
            ->count();

        return [
            'countPelajarWithNilai' => $countPelajarWithNilai,
            'countDeskripsiKosong' => $countDeskripsiKosong,
            'countTemplateAvailable' => $countTemplateAvailable,
        ];
    }

    private function validateGenerateStatistics(array $statistics): bool
    {
        if ($statistics['countTemplateAvailable'] === 0) {
            $this->dispatchError('Belum ada template deskripsi untuk ekstrakurikuler ini atau template umum.', 'Template Tidak Ditemukan!');
            return false;
        }

        if ($statistics['countPelajarWithNilai'] === 0) {
            $this->dispatchError('Belum ada pelajar yang memiliki nilai ekstrakurikuler tersimpan (predikat belum diisi).', 'Tidak Ada Data!');
            return false;
        }

        return true;
    }

    public function closeGenerateModal(): void
    {
        $this->dispatch('hide-generate-ekskul-modal');
        $this->generateMode = 'empty';
    }

    public function generateDeskripsi(): void
    {
        $this->validate(['generateMode' => 'required|in:empty,all']);

        if (!$this->validateContext()) {
            return;
        }

        $ekstrakurikuler = Ekstrakurikuler::find($this->ekstrakurikulerId);
        if (!$ekstrakurikuler) {
            $this->dispatchError('Data ekstrakurikuler tidak valid.');
            return;
        }

        DB::beginTransaction();
        try {
            $result = $this->processDeskripsiGeneration($ekstrakurikuler);

            DB::commit();

            $this->closeGenerateModal();
            $this->loadEkstrakurikulerPelajar();

            $this->dispatchGenerateResult($result);
        } catch (\Exception $e) {
            DB::rollback();
            $this->logError('generating deskripsi ekstrakurikuler', $e);
            $this->dispatchError('Terjadi kesalahan saat generate deskripsi: ' . $e->getMessage());
        }
    }

    private function processDeskripsiGeneration($ekstrakurikuler): array
    {
        $query = EkskulPelajar::where('tahun_ajaran_semester_id', $this->semesterId)
            ->where('ekstrakurikuler_id', $this->ekstrakurikulerId)
            ->whereNotNull('nilai')
            ->whereIn('pelajar_id', function ($q) {
                $q->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombelId);
            });

        if ($this->generateMode === 'empty') {
            // Filter deskripsi NULL atau string kosong
            $query->where(function (Builder $q) {
                $q->whereNull('deskripsi')
                    ->orWhere('deskripsi', '');
            });
        }

        $ekskulPelajarList = $query->get();
        $successCount = 0;
        $errorList = [];

        foreach ($ekskulPelajarList as $ekskulPelajar) {
            // Pastikan nilai valid sebelum mencari template
            if (!isset($this->nilaiOptions[$ekskulPelajar->nilai])) {
                continue;
            }

            $template = $this->getMatchingTemplate($ekskulPelajar->nilai);

            if ($template) {
                // Proses placeholder
                $deskripsiFinal = $this->processPlaceholder($template->deskripsi, $ekstrakurikuler->nama);

                $ekskulPelajar->deskripsi = $deskripsiFinal;
                $ekskulPelajar->updated_by = Auth::id();
                $ekskulPelajar->save();
                $successCount++;
            } else {
                $pelajar = $ekskulPelajar->pelajar()->first();
                $errorList[] = [
                    'nama' => $pelajar->nama_lengkap ?? 'N/A',
                    'nilai' => $this->nilaiOptions[$ekskulPelajar->nilai] ?? $ekskulPelajar->nilai,
                ];
            }
        }

        return ['successCount' => $successCount, 'errorList' => $errorList];
    }

    private function processPlaceholder(string $deskripsi, string $ekskulNama): string
    {
        // Ganti placeholder {EKSTRAKURIKULER} dengan nama ekstrakurikuler
        return str_replace('{EKSTRAKURIKULER}', $ekskulNama, $deskripsi);
    }

    private function dispatchGenerateResult(array $result): void
    {
        if (empty($result['errorList'])) {
            $this->dispatchSuccess("Berhasil generate deskripsi untuk {$result['successCount']} pelajar.");
            return;
        }

        $errorMessage = $this->buildGenerateErrorMessage($result);
        $this->dispatchWarning($errorMessage, 'Generate Selesai!');
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

    private function getMatchingTemplate(string $predikat): ?TemplateEkstrakurikulerDeskripsi
    {
        // Prioritas 1: Cari template spesifik untuk ekstrakurikuler ini
        $templateSpesifik = TemplateEkstrakurikulerDeskripsi::where('tahun_ajaran_semester_id', $this->semesterId)
            ->where('ekstrakurikuler_id', $this->ekstrakurikulerId)
            ->where('predikat', $predikat)
            ->where('aktif', true)
            ->first();

        if ($templateSpesifik) {
            return $templateSpesifik;
        }

        // Prioritas 2: Cari template umum (ekstrakurikuler_id = NULL)
        $templateUmum = TemplateEkstrakurikulerDeskripsi::where('tahun_ajaran_semester_id', $this->semesterId)
            ->whereNull('ekstrakurikuler_id')
            ->where('predikat', $predikat)
            ->where('aktif', true)
            ->first();

        return $templateUmum;
    }


    // ========================================
    // UTILITY & HELPER METHODS
    // ========================================

    private function validateContext(): bool
    {
        if (!$this->rombelId || !$this->semesterId || !$this->ekstrakurikulerId) {
            $this->dispatchError('Pastikan semua filter sudah dipilih.');
            return false;
        }
        return true;
    }

    private function validateDeleteContext($pelajarId): bool
    {
        if (!$pelajarId || !$this->ekstrakurikulerId || !$this->semesterId) {
            $this->dispatchError('ID Pelajar tidak ditemukan atau data tidak valid.', 'Gagal!');
            return false;
        }
        return true;
    }

    private function extractPelajarId($pelajarId)
    {
        return is_array($pelajarId) && isset($pelajarId[0]) ? $pelajarId[0] : $pelajarId;
    }

    // 🔹 DISPATCH HELPER METHODS
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

    private function dispatchWarning(string $text, string $title = 'Perhatian!'): void
    {
        $this->dispatch('swal:warning', ['title' => $title, 'text' => $text]);
    }

    private function logError(string $action, \Exception $e, array $context = []): void
    {
        Log::error("Error {$action}: " . $e->getMessage(), array_merge($context, [
            'ekstrakurikuler_id' => $this->ekstrakurikulerId,
            'rombel_id' => $this->rombelId,
            'semester_id' => $this->semesterId,
            'user_id' => Auth::id(),
        ]));
    }

    // 🔹 Render view dengan data pelajar
    public function render()
    {
        $pelajarData = collect();

        if ($this->rombelId && $this->semesterId && $this->ekstrakurikulerId) {
            // Use cached ekstrakurikuler instead of querying again
            $ekstrakurikulerExist = $this->cachedEkstrakurikulerExist ?? collect();

            // Query pelajar dengan pagination
            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            // Map data pelajar dengan ekstrakurikuler mereka
            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($ekstrakurikulerExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                // Inisialisasi input ekstrakurikuler jika belum ada
                if (!isset($this->ekstrakurikulerInput[$pelajarId])) {
                    $existingEkskul = $ekstrakurikulerExist->get($pelajarId);
                    $this->ekstrakurikulerInput[$pelajarId] = [
                        'nilai' => $existingEkskul->nilai ?? null,
                        'deskripsi' => $existingEkskul->deskripsi ?? '',
                    ];
                }

                $existingEkskulData = $ekstrakurikulerExist->get($pelajarId);
                $nilaiKey = $existingEkskulData->nilai ?? '-';
                $nilaiLabel = $this->nilaiOptions[$nilaiKey] ?? $nilaiKey;


                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id' => $pelajarId,
                    'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                    'nisn' => $rombelPelajar->pelajar->nisn,
                    'ekstrakurikuler_existing' => $existingEkskulData ? (object) [
                        'id' => $existingEkskulData->id,
                        'nilai' => $nilaiKey,
                        'nilai_label' => $nilaiLabel,
                        'deskripsi' => $existingEkskulData->deskripsi,
                    ] : null,
                ];
            });
        }

        return view('livewire.admin.input-ekstrakurikuler', [
            'pelajarData' => $pelajarData,
        ]);
    }
}
