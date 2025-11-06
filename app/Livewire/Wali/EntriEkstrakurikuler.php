<?php

namespace App\Livewire\Wali;

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
        $this->loadRombelWaliKelas();

        if (!$this->rombel) {
            session()->flash('error', 'Anda tidak memiliki kelas binaan.');
            return redirect()->route('walikelas.dashboard');
        }

        $this->loadSemesterAktif();

        if (!$this->semesterAktif) {
            session()->flash('warning', 'Tidak ada semester aktif saat ini.');
        }

        $this->loadEkstrakurikulerList();

        if ($this->selectedEkstrakurikulerId) {
            $this->loadEkskulPelajar();
        }
    }

    protected $listeners = [
        'deleteEkskul',
        'confirmResetEkskul' => 'resetEkskul',
        'closeGenerateModal'
    ];

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

    private function loadEkstrakurikulerList(): void
    {
        $this->ekstrakurikulerList = Ekstrakurikuler::with('pembina')
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();
    }

    public function updatedSelectedEkstrakurikulerId(): void
    {
        $this->resetPage();
        $this->searchPelajar = '';
        $this->ekskulInput = [];
        $this->cachedEkskulExist = null;
        $this->loadEkskulPelajar();
    }

    public function updatingSearchPelajar(): void
    {
        $this->resetPage();
    }

    private function loadEkskulPelajar(): void
    {
        if (!$this->selectedEkstrakurikulerId || !$this->semesterAktif || !$this->rombel) {
            $this->ekskulInput = [];
            $this->pembinaName = null;
            $this->cachedEkskulExist = null;
            return;
        }

        $ekstrakurikuler = Ekstrakurikuler::with('pembina')
            ->find($this->selectedEkstrakurikulerId);

        if (!$ekstrakurikuler) {
            $this->ekskulInput = [];
            $this->pembinaName = null;
            $this->cachedEkskulExist = null;
            $this->selectedEkstrakurikulerId = null;
            return;
        }

        $this->pembinaName = $ekstrakurikuler->pembina->name ?? 'N/A';

        // Ambil data ekskul pelajar yang sudah ada
        $ekskulData = EkskulPelajar::where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->whereIn('pelajar_id', function ($q) {
                $q->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombel->id);
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
        if (!$this->selectedEkstrakurikulerId || !$this->semesterAktif || !$this->rombel) {
            $this->dispatchError('Silakan pilih ekstrakurikuler terlebih dahulu.');
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

        $validPelajarIds = RombelPelajar::where('rombel_id', $this->rombel->id)
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

            Log::error('Error saving ekskul', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'semester_id' => $this->semesterAktif->id,
            ]);

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
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                ->first();

            // Logic untuk menghapus jika nilai dikosongkan
            if (empty($nilai) && empty($deskripsi)) {
                if ($existingData) {
                    $existingData->delete();
                    $deletedCount++;
                }
                continue;
            }

            // Memastikan nilai yang diinput valid (A, B, atau C)
            if (!in_array($nilai, array_keys($this->predikatOptions)) && !empty($nilai)) {
                continue;
            }

            // Jika ada nilai yang terisi, simpan/update
            if ($existingData) {
                $existingData->update([
                    'nilai' => $nilai,
                    'deskripsi' => $deskripsi,
                    'updated_by' => $userId,
                ]);
                $updatedCount++;
            } else {
                // Hanya buat baru jika ada nilai yang dipilih
                if (!empty($nilai)) {
                    EkskulPelajar::create([
                        'pelajar_id' => $pelajarId,
                        'ekstrakurikuler_id' => $this->selectedEkstrakurikulerId,
                        'tahun_ajaran_semester_id' => $this->semesterAktif->id,
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

        // Mengembalikan input ke nilai tersimpan (cache)
        foreach ($this->cachedEkskulExist as $pelajarId => $data) {
            $this->ekskulInput[$pelajarId] = [
                'nilai' => $data->nilai ?? null,
                'deskripsi' => $data->deskripsi ?? '',
            ];
        }

        // Mengosongkan input yang tidak ada di cache
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

        if (!$pelajarId || !$this->selectedEkstrakurikulerId || !$this->semesterAktif) {
            $this->dispatchError('ID Pelajar tidak ditemukan atau data tidak valid.', 'Gagal!');
            return;
        }

        try {
            $deleted = EkskulPelajar::where('pelajar_id', $pelajarId)
                ->where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
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
            Log::error('Error deleting ekskul: ' . $e->getMessage(), ['pelajar_id' => $pelajarId]);
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
        $countPelajarWithEkskul = EkskulPelajar::where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombel->id);
            })
            ->count();

        $countDeskripsiKosong = EkskulPelajar::where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombel->id);
            })
            ->where(function (Builder $query) {
                $query->whereNull('deskripsi')
                    ->orWhere('deskripsi', '');
            })
            ->count();

        return [
            'countPelajarWithEkskul' => $countPelajarWithEkskul,
            'countDeskripsiKosong' => $countDeskripsiKosong,
            'countTemplateAvailable' => TemplateEkstrakurikulerDeskripsi::where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                ->where(function ($q) {
                    $q->where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
                        ->orWhereNull('ekstrakurikuler_id'); // Template umum
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
        $query = EkskulPelajar::where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
            ->whereIn('pelajar_id', function ($q) {
                $q->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombel->id);
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

                // Handle placeholder jika ada
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
        // Prioritaskan template spesifik ekstrakurikuler
        $template = TemplateEkstrakurikulerDeskripsi::where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->where('ekstrakurikuler_id', $this->selectedEkstrakurikulerId)
            ->where('predikat', $nilai)
            ->where('aktif', true)
            ->first();

        // Jika tidak ada, gunakan template umum
        if (!$template) {
            $template = TemplateEkstrakurikulerDeskripsi::where('tahun_ajaran_semester_id', $this->semesterAktif->id)
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

    private function validateGenerateContext(): bool
    {
        if (!$this->selectedEkstrakurikulerId || !$this->semesterAktif || !$this->rombel) {
            $this->dispatchError('Pastikan ekstrakurikuler sudah dipilih dan ada semester aktif.');
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
            'semester_id' => $this->semesterAktif->id ?? 'N/A',
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

        if ($this->rombel) {
            $totalSiswa = RombelPelajar::where('rombel_id', $this->rombel->id)->count();
        }

        if ($this->selectedEkstrakurikulerId && $this->semesterAktif) {
            if ($this->cachedEkskulExist === null) {
                $this->loadEkskulPelajar();
            }

            $ekskulExist = $this->cachedEkskulExist ?? collect();

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($ekskulExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                // Memastikan input array terisi dengan nilai tersimpan jika belum diubah
                if (!isset($this->ekskulInput[$pelajarId])) {
                    $existingEkskul = $ekskulExist->get($pelajarId);
                    $this->ekskulInput[$pelajarId] = [
                        'nilai' => $existingEkskul->nilai ?? null,
                        'deskripsi' => $existingEkskul->deskripsi ?? '',
                    ];
                }

                $existingEkskulData = $ekskulExist->get($pelajarId);

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

        return view('livewire.wali.entri-ekstrakurikuler', [
            'pelajarData' => $pelajarData,
            'totalSiswa' => $totalSiswa,
        ]);
    }
}
