<?php

namespace App\Livewire\Admin;

use App\Models\Nilai;
use App\Models\Rombel;
use Livewire\Component;
use App\Models\TahunAjaran;
use Livewire\WithPagination;
use App\Models\RombelPelajar;
use App\Models\RombelPengajar;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\TemplateNilaiCapaian;
use Illuminate\Database\Eloquent\Builder;

class InputNilaiAkhir extends Component
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
    public $mataPelajaranList = [];

    // Main Data
    public $rombel;
    public $guruName = null;
    public $nilaiInput = [];
    public $generateMode = 'empty';

    protected $queryString = [
        'tahunAjaranId' => ['except' => null],
        'semesterId' => ['except' => null],
        'rombelId' => ['except' => null],
        'selectedRombelPengajarId' => ['except' => null],
        'searchPelajar' => ['except' => ''],
    ];

    protected $listeners = [
        'saveNilaiConfirmed' => 'saveNilai',
        'resetNilaiConfirmed' => 'resetNilai',
        'deleteNilai' => 'deleteNilai',
    ];

    protected $rules = [
        'nilaiInput.*' => 'nullable|numeric|min:0|max:100',
        'generateMode' => 'required|in:empty,all',
    ];

    protected $messages = [
        'nilaiInput.*.numeric' => 'Nilai harus berupa angka',
        'nilaiInput.*.min' => 'Nilai minimal adalah 0',
        'nilaiInput.*.max' => 'Nilai maksimal adalah 100',
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

        if ($this->rombelId) {
            $this->loadRombelData();
            $this->loadMataPelajaran();
        }

        if ($this->selectedRombelPengajarId) {
            $this->loadNilaiPelajar();
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
            $this->dispatchError('Rombel tidak ditemukan.');
        }
    }

    private function loadMataPelajaran(): void
    {
        if (!$this->rombelId) {
            $this->mataPelajaranList = [];
            return;
        }

        $this->mataPelajaranList = RombelPengajar::with(['mataPelajaran', 'guru'])
            ->where('rombel_id', $this->rombelId)
            ->orderBy('mata_pelajaran_id')
            ->get();
    }

    private function loadNilaiPelajar(): void
    {
        if (!$this->selectedRombelPengajarId || !$this->semesterId) {
            $this->nilaiInput = [];
            $this->guruName = null;
            return;
        }

        $rombelPengajar = RombelPengajar::with('guru', 'mataPelajaran')
            ->find($this->selectedRombelPengajarId);

        if (!$rombelPengajar) {
            $this->clearNilaiData();
            return;
        }

        $this->guruName = $rombelPengajar->guru->name ?? 'N/A';

        $nilaiExisting = Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->pluck('nilai_angka', 'pelajar_id');

        $this->nilaiInput = $nilaiExisting->toArray();
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
        $this->mataPelajaranList = [];
        $this->nilaiInput = [];
        $this->rombel = null;

        $this->loadRombel();
        $this->resetPage();
    }

    public function updatedRombelId(): void
    {
        $this->selectedRombelPengajarId = null;
        $this->mataPelajaranList = [];
        $this->nilaiInput = [];

        if ($this->rombelId) {
            $this->loadRombelData();
            $this->loadMataPelajaran();
        } else {
            $this->rombel = null;
        }

        $this->resetPage();
    }

    public function updatedSelectedRombelPengajarId(): void
    {
        $this->resetPage();
        $this->searchPelajar = '';
        $this->loadNilaiPelajar();
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
    // NILAI MANAGEMENT METHODS
    // ========================================

    public function confirmSaveNilai(): void
    {
        if (!$this->selectedRombelPengajarId) {
            $this->dispatchError('Silakan pilih mata pelajaran terlebih dahulu.');
            return;
        }

        $this->validate();

        $this->dispatch('swal:confirm', [
            'title' => 'Simpan Nilai?',
            'text' => 'Semua nilai yang diinput akan disimpan.',
            'nextEvent' => 'saveNilaiConfirmed',
        ]);
    }

    public function saveNilai(): void
    {
        if (!$this->validateNilaiContext()) {
            return;
        }

        $rombelPengajar = $this->getRombelPengajar();
        if (!$rombelPengajar) {
            return;
        }

        $validPelajarIds = $this->getValidPelajarIds();

        DB::beginTransaction();
        try {
            $savedCount = $this->processNilaiSaving($rombelPengajar, $validPelajarIds);

            DB::commit();

            $this->dispatchSuccess("Berhasil menyimpan {$savedCount} nilai untuk mata pelajaran '{$rombelPengajar->mataPelajaran->nama}'.");
            $this->loadNilaiPelajar();
        } catch (\Exception $e) {
            DB::rollback();
            $this->logError('saving nilai', $e);
            $this->dispatchError('Gagal menyimpan nilai. Silakan coba lagi.');
        }
    }

    private function processNilaiSaving($rombelPengajar, array $validPelajarIds): int
    {
        $savedCount = 0;

        foreach ($this->nilaiInput as $pelajarId => $nilai) {
            if (!$this->isValidNilaiForSaving($pelajarId, $nilai, $validPelajarIds)) {
                continue;
            }

            $nilaiBersih = floatval($nilai);
            $predikat = $this->hitungPredikat($nilaiBersih);

            Nilai::updateOrCreate(
                [
                    'pelajar_id' => $pelajarId,
                    'mata_pelajaran_id' => $rombelPengajar->mata_pelajaran_id,
                    'rombel_pengajar_id' => $this->selectedRombelPengajarId,
                    'tahun_ajaran_semester_id' => $this->semesterId,
                ],
                [
                    'guru_id' => $rombelPengajar->guru_id,
                    'nilai_angka' => $nilaiBersih,
                    'predikat' => $predikat,
                    'updated_by' => Auth::id(),
                    'created_by' => Auth::id(),
                ]
            );

            $savedCount++;
        }

        return $savedCount;
    }

    private function isValidNilaiForSaving($pelajarId, $nilai, array $validPelajarIds): bool
    {
        if (!in_array($pelajarId, $validPelajarIds)) {
            return false;
        }

        if ($nilai === null || $nilai === '') {
            return false;
        }

        if (!is_numeric($nilai)) {
            return false;
        }

        $nilaiBersih = floatval($nilai);
        return $nilaiBersih >= 0 && $nilaiBersih <= 100;
    }

    public function confirmResetNilai(): void
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Reset Input Nilai?',
            'text' => 'Semua input nilai akan dikosongkan (belum disimpan).',
            'nextEvent' => 'resetNilaiConfirmed',
        ]);
    }

    public function resetNilai(): void
    {
        $this->nilaiInput = array_map(fn() => null, $this->nilaiInput);

        $this->dispatch('swal:info', [
            'title' => 'Direset!',
            'text' => 'Semua kolom input nilai telah dikosongkan.',
        ]);
    }

    public function deleteNilai($pelajarId = null): void
    {
        $pelajarId = $this->extractPelajarId($pelajarId);

        if (!$this->validateDeleteContext($pelajarId)) {
            return;
        }

        try {
            $rombelPengajar = $this->getRombelPengajar();
            if (!$rombelPengajar) {
                throw new \Exception('Data mata pelajaran tidak ditemukan');
            }

            $deleted = $this->performNilaiDeletion($pelajarId, $rombelPengajar);

            if ($deleted) {
                $this->handleSuccessfulDeletion($pelajarId);
            } else {
                $this->dispatchInfo('Nilai tidak ditemukan.');
            }
        } catch (\Exception $e) {
            $this->logError('deleting nilai', $e, ['pelajar_id' => $pelajarId]);
            $this->dispatchError('Terjadi kesalahan saat menghapus nilai.');
        }
    }

    private function performNilaiDeletion($pelajarId, $rombelPengajar): int
    {
        return Nilai::where('pelajar_id', $pelajarId)
            ->where('mata_pelajaran_id', $rombelPengajar->mata_pelajaran_id)
            ->where('rombel_pengajar_id', $this->selectedRombelPengajarId)
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->delete();
    }

    private function handleSuccessfulDeletion($pelajarId): void
    {
        if (isset($this->nilaiInput[$pelajarId])) {
            unset($this->nilaiInput[$pelajarId]);
        }

        $this->loadNilaiPelajar();
        $this->dispatchSuccess('Nilai berhasil dihapus.');
    }

    // ========================================
    // GENERATE CAPAIAN METHODS
    // ========================================

    public function openGenerateModal(): void
    {
        if (!$this->validateGenerateContext()) {
            return;
        }

        $rombelPengajar = $this->getRombelPengajar();
        if (!$rombelPengajar) {
            return;
        }

        $statistics = $this->calculateGenerateStatistics($rombelPengajar);

        if (!$this->validateGenerateStatistics($statistics)) {
            return;
        }

        $this->dispatch('show-generate-modal', $statistics);
    }

    private function calculateGenerateStatistics($rombelPengajar): array
    {
        return [
            'countPelajarWithNilai' => Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->count(),
            'countCapaianKosong' => Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->whereNull('capaian_kompetensi')
                ->count(),
            'countTemplateAvailable' => TemplateNilaiCapaian::where('tahun_ajaran_semester_id', $this->semesterId)
                ->where('mata_pelajaran_id', $rombelPengajar->mata_pelajaran_id)
                ->where('tingkat', $this->rombel->tingkat)
                ->where('aktif', true)
                ->count(),
        ];
    }

    private function validateGenerateStatistics(array $statistics): bool
    {
        if ($statistics['countTemplateAvailable'] === 0) {
            $this->dispatchError('Belum ada template capaian untuk mata pelajaran dan tingkat kelas ini.', 'Template Tidak Ditemukan!');
            return false;
        }

        if ($statistics['countPelajarWithNilai'] === 0) {
            $this->dispatchError('Belum ada pelajar yang memiliki nilai tersimpan.', 'Tidak Ada Data!');
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
        $this->validate(['generateMode' => 'required|in:empty,all']);

        if (!$this->validateGenerateContext()) {
            return;
        }

        $rombelPengajar = $this->getRombelPengajar();
        if (!$rombelPengajar) {
            return;
        }

        DB::beginTransaction();
        try {
            $result = $this->processCapaianGeneration($rombelPengajar);

            DB::commit();

            $this->closeGenerateModal();
            $this->loadNilaiPelajar();

            $this->dispatchGenerateResult($result);
        } catch (\Exception $e) {
            DB::rollback();
            $this->logError('generating capaian', $e);
            $this->dispatchError('Terjadi kesalahan saat generate capaian.');
        }
    }

    private function processCapaianGeneration($rombelPengajar): array
    {
        $query = Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
            ->where('tahun_ajaran_semester_id', $this->semesterId);

        if ($this->generateMode === 'empty') {
            $query->whereNull('capaian_kompetensi');
        }

        $nilaiList = $query->get();
        $successCount = 0;
        $errorList = [];

        foreach ($nilaiList as $nilai) {
            $template = $this->getMatchingTemplate($nilai->nilai_angka, $rombelPengajar->mata_pelajaran_id);

            if ($template) {
                $nilai->capaian_kompetensi = $template->deskripsi;
                $nilai->updated_by = Auth::id();
                $nilai->save();
                $successCount++;
            } else {
                $errorList[] = [
                    'nama' => $nilai->pelajar->nama_lengkap ?? 'N/A',
                    'nilai' => $nilai->nilai_angka,
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
            $message .= "- {$error['nama']} (nilai: {$error['nilai']}) - Template tidak ditemukan\n";
        }

        return $message;
    }

    private function getMatchingTemplate(float $nilaiAngka, string $mataPelajaranId): ?TemplateNilaiCapaian
    {
        return TemplateNilaiCapaian::where('tahun_ajaran_semester_id', $this->semesterId)
            ->where('mata_pelajaran_id', $mataPelajaranId)
            ->where('tingkat', $this->rombel->tingkat)
            ->where('aktif', true)
            ->where('rentang_min', '<=', $nilaiAngka)
            ->where('rentang_max', '>=', $nilaiAngka)
            ->first();
    }

    // ========================================
    // UTILITY METHODS
    // ========================================

    private function hitungPredikat(float $nilai): string
    {
        if ($nilai >= 91) return 'A';
        if ($nilai >= 83) return 'B';
        if ($nilai >= 75) return 'C';
        return 'D';
    }

    private function resetFilters(): void
    {
        $this->semesterId = null;
        $this->rombelId = null;
        $this->selectedRombelPengajarId = null;
        $this->semesterList = [];
        $this->rombelList = [];
        $this->mataPelajaranList = [];
        $this->nilaiInput = [];
        $this->rombel = null;
    }

    private function clearNilaiData(): void
    {
        $this->nilaiInput = [];
        $this->guruName = null;
        $this->selectedRombelPengajarId = null;
    }

    private function getRombelPengajar()
    {
        $rombelPengajar = RombelPengajar::with('mataPelajaran')
            ->find($this->selectedRombelPengajarId);

        if (!$rombelPengajar) {
            $this->dispatchError('Data mata pelajaran tidak ditemukan.');
        }

        return $rombelPengajar;
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

    private function validateNilaiContext(): bool
    {
        return $this->selectedRombelPengajarId && $this->semesterId;
    }

    private function validateDeleteContext($pelajarId): bool
    {
        if (!$pelajarId || !$this->selectedRombelPengajarId || !$this->semesterId) {
            $this->dispatchError('ID Pelajar tidak ditemukan atau data tidak valid.', 'Gagal!');
            return false;
        }
        return true;
    }

    private function validateGenerateContext(): bool
    {
        if (!$this->selectedRombelPengajarId || !$this->semesterId || !$this->rombel) {
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
            'rombel_pengajar_id' => $this->selectedRombelPengajarId,
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

        if ($this->selectedRombelPengajarId && $this->semesterId) {
            $allNilai = Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->get()
                ->keyBy('pelajar_id');

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($allNilai) {
                $pelajarId = $rombelPelajar->pelajar_id;
                $nilaiRecord = $allNilai->get($pelajarId);

                if (!isset($this->nilaiInput[$pelajarId]) && $nilaiRecord) {
                    $this->nilaiInput[$pelajarId] = $nilaiRecord->nilai_angka;
                }

                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id' => $pelajarId,
                    'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                    'nisn' => $rombelPelajar->pelajar->nisn,
                    'nilai_sekarang' => $nilaiRecord->nilai_angka ?? null,
                    'capaian_kompetensi' => $nilaiRecord->capaian_kompetensi ?? null,
                ];
            });
        }

        return view('livewire.admin.input-nilai-akhir', [
            'pelajarData' => $pelajarData,
        ]);
    }
}
