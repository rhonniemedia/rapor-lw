<?php

namespace App\Livewire\Wali;

use App\Models\Nilai;
use App\Models\Pelajar;
use App\Models\Rombel;
use App\Models\RombelPelajar;
use App\Models\RombelPengajar;
use App\Models\TahunAjaran;
use App\Models\TahunAjaranSemester;
use App\Models\TemplateNilaiCapaian;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class EntriNilai extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filter Properties
    public $tahunAjaranId = null;
    public $semesterId = null;
    public $selectedRombelPengajarId = null;

    // Data Collections
    public $tahunAjaranList = [];
    public $semesterList = [];
    public $mataPelajaranList = [];

    // Main Data
    public $rombel;
    public $guruName = null;
    public $agamaTerkait = null;

    // Search & Pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // Input nilai
    public $nilaiInput = [];
    public $generateMode = 'empty';

    // Cache
    private $cachedNilaiExist = null;

    // Query string
    protected $queryString = [
        'tahunAjaranId'             => ['except' => null],
        'semesterId'                => ['except' => null],
        'selectedRombelPengajarId'  => ['except' => null],
        'searchPelajar'             => ['except' => ''],
    ];

    // Listeners
    protected $listeners = [
        'deleteNilai',
        'resetNilaiConfirmed' => 'resetNilai',
    ];

    // Validation
    protected $rules = [
        'nilaiInput.*' => 'nullable|numeric|min:0|max:100',
        'generateMode' => 'required|in:empty,all',
    ];

    protected $messages = [
        'nilaiInput.*.numeric'  => 'Nilai harus berupa angka',
        'nilaiInput.*.min'      => 'Nilai minimal adalah 0',
        'nilaiInput.*.max'      => 'Nilai maksimal adalah 100',
        'generateMode.required' => 'Pilih mode generate',
        'generateMode.in'       => 'Mode generate tidak valid',
    ];

    public function mount()
    {
        $this->loadRombelWaliKelas();

        if (!$this->rombel) {
            session()->flash('error', 'Anda tidak memiliki kelas binaan.');
            return;
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

    private function loadMataPelajaran(): void
    {
        if (!$this->rombel) {
            $this->mataPelajaranList = [];
            return;
        }

        $this->mataPelajaranList = RombelPengajar::with([
            'mataPelajaran',
            'guru'
        ])
            ->where('rombel_id', $this->rombel->id)
            ->orderBy('mata_pelajaran_id')
            ->get();
    }

    private function loadNilaiPelajar(): void
    {
        if (!$this->selectedRombelPengajarId || !$this->semesterId) {
            $this->nilaiInput = [];
            $this->guruName = null;
            $this->cachedNilaiExist = null;
            $this->agamaTerkait = null;
            return;
        }

        $rombelPengajar = RombelPengajar::with('guru', 'mataPelajaran')
            ->find($this->selectedRombelPengajarId);

        if (!$rombelPengajar) {
            $this->nilaiInput = [];
            $this->guruName = null;
            $this->cachedNilaiExist = null;
            $this->selectedRombelPengajarId = null;
            $this->agamaTerkait = null;
            return;
        }

        $this->guruName = $rombelPengajar->guru->name ?? 'N/A';
        $this->agamaTerkait = $rombelPengajar->mataPelajaran->agama_terkait ?? null;

        $this->cachedNilaiExist = Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->select('pelajar_id', 'nilai_angka', 'capaian_kompetensi')
            ->get()
            ->keyBy('pelajar_id');
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
        $this->selectedRombelPengajarId = null;
        $this->mataPelajaranList = [];
        $this->nilaiInput = [];
        $this->cachedNilaiExist = null;
        $this->guruName = null;
        $this->agamaTerkait = null;

        $this->loadMataPelajaran();
        $this->resetPage();
    }

    public function updatedSelectedRombelPengajarId(): void
    {
        $this->resetPage();
        $this->searchPelajar = '';
        $this->nilaiInput = [];
        $this->cachedNilaiExist = null;
        $this->loadNilaiPelajar();
    }

    public function updatingSearchPelajar(): void
    {
        $this->resetPage();
    }

    // ========================================
    // HELPER QUERY
    // ========================================

    private function getPelajarQuery(): Builder
    {
        if (!$this->rombel) {
            return RombelPelajar::whereNull('id');
        }

        $query = RombelPelajar::where('rombel_id', $this->rombel->id)
            ->with(['pelajar']);

        // Filter Pelajar berdasarkan Hash Agama
        if ($this->agamaTerkait) {
            $agamaFilterNormalized = Str::lower($this->agamaTerkait);
            $agamaHashToFind = hash('sha256', $agamaFilterNormalized);

            $query->whereHas('pelajar', function ($q) use ($agamaHashToFind) {
                $q->where('agama_hash', $agamaHashToFind);
            });
        }

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

    private function hitungPredikat(float $nilai): string
    {
        if ($nilai >= 92) return 'A'; // Sangat Baik
        if ($nilai >= 84) return 'B'; // Baik
        if ($nilai >= 75) return 'C'; // Cukup / Tuntas
        return 'D'; // Kurang / Belum Tuntas
    }

    // ========================================
    // SAVE / RESET / DELETE METHODS
    // ========================================

    public function saveNilai(): void
    {
        if (!$this->selectedRombelPengajarId || !$this->semesterId) {
            $this->dispatchError('Silakan pilih mata pelajaran terlebih dahulu.');
            return;
        }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatchError('Periksa input nilai Anda. ' . implode(', ', array_map(fn($err) => implode(', ', $err), $e->errors())), 'Validasi Gagal!');
            return;
        }

        $rombelPengajar = $this->getRombelPengajar();

        if (!$rombelPengajar) {
            $this->dispatchError('Data mata pelajaran tidak ditemukan.');
            return;
        }

        $mataPelajaran   = $rombelPengajar->mataPelajaran->nama;
        $mataPelajaranId = $rombelPengajar->mata_pelajaran_id;
        $guruId          = $rombelPengajar->guru_id;

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

                $nilaiBersih = is_numeric($nilai) ? floatval($nilai) : null;

                if ($nilaiBersih === null || $nilaiBersih < 0 || $nilaiBersih > 100) {
                    continue;
                }

                $predikat = $this->hitungPredikat($nilaiBersih);

                Nilai::updateOrCreate(
                    [
                        'pelajar_id'               => $pelajarId,
                        'mata_pelajaran_id'        => $mataPelajaranId,
                        'tahun_ajaran_semester_id' => $this->semesterId,
                    ],
                    [
                        'rombel_pengajar_id' => $this->selectedRombelPengajarId,
                        'guru_id'            => $guruId,
                        'nilai_angka'        => $nilaiBersih,
                        'predikat'           => $predikat,
                        'updated_by'         => $userId,
                        'created_by'         => $userId,
                    ]
                );

                $savedCount++;
            }

            DB::commit();

            $this->nilaiInput = [];
            $this->cachedNilaiExist = null;
            $this->loadNilaiPelajar();

            $this->dispatchSuccess("Berhasil menyimpan {$savedCount} nilai untuk mata pelajaran '{$mataPelajaran}'.");
        } catch (\Exception $e) {
            DB::rollback();
            $this->logError('saving nilai', $e);
            $this->dispatchError('Gagal menyimpan nilai. Silakan coba lagi.');
        }
    }

    public function confirmResetNilai(): void
    {
        $this->dispatch('swal:confirm', [
            'title'     => 'Reset Input Nilai?',
            'text'      => 'Input nilai akan dikembalikan ke nilai yang tersimpan di database.',
            'nextEvent' => 'resetNilaiConfirmed',
        ]);
    }

    public function resetNilai(): void
    {
        if ($this->cachedNilaiExist === null) {
            $this->loadNilaiPelajar();
        }

        foreach ($this->nilaiInput as $pelajarId => $nilai) {
            $this->nilaiInput[$pelajarId] = $this->cachedNilaiExist->get($pelajarId)->nilai_angka ?? null;
        }

        $this->dispatchInfo('Input nilai telah dikembalikan ke nilai tersimpan.');
    }

    public function deleteNilai($pelajarId = null): void
    {
        if (is_array($pelajarId) && isset($pelajarId[0])) {
            $pelajarId = $pelajarId[0];
        }

        if (!$pelajarId || !$this->selectedRombelPengajarId || !$this->semesterId) {
            $this->dispatchError('ID Pelajar tidak ditemukan atau data rombel/semester tidak valid.', 'Gagal!');
            return;
        }

        try {
            $rombelPengajar = $this->getRombelPengajar();
            $deleted = Nilai::where('pelajar_id', $pelajarId)
                ->where('mata_pelajaran_id', $rombelPengajar->mata_pelajaran_id)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->delete();

            if ($deleted) {
                if (isset($this->nilaiInput[$pelajarId])) {
                    unset($this->nilaiInput[$pelajarId]);
                }

                $this->cachedNilaiExist = null;
                $this->loadNilaiPelajar();

                $this->dispatchSuccess('Nilai berhasil dihapus.');
            } else {
                $this->dispatchInfo('Nilai tidak ditemukan.');
            }
        } catch (\Exception $e) {
            $this->logError('deleting nilai', $e, ['pelajar_id' => $pelajarId]);
            $this->dispatchError('Terjadi kesalahan saat menghapus nilai.');
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
            $this->cachedNilaiExist = null;
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
                    'nama'  => $nilai->pelajar->nama_lengkap ?? 'N/A',
                    'nilai' => $nilai->nilai_angka,
                ];
            }
        }

        return ['successCount' => $successCount, 'errorList' => $errorList];
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
            $message .= "- {$error['nama']} (nilai: {$error['nilai']}) - Template tidak ditemukan\n";
        }

        return $message;
    }

    // ========================================
    // UTILITY / VALIDATION METHODS
    // ========================================

    private function resetFilters(): void
    {
        $this->semesterId = null;
        $this->selectedRombelPengajarId = null;
        $this->semesterList = [];
        $this->mataPelajaranList = [];
        $this->nilaiInput = [];
        $this->cachedNilaiExist = null;
        $this->guruName = null;
        $this->agamaTerkait = null;
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

    private function validateGenerateContext(): bool
    {
        if (!$this->selectedRombelPengajarId || !$this->semesterId || !$this->rombel) {
            $this->dispatchError('Pastikan semua filter sudah dipilih.');
            return false;
        }
        return true;
    }

    // ========================================
    // DISPATCH & LOG HELPER METHODS
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
            'semester_id'        => $this->semesterId ?? 'N/A',
            'user_id'            => Auth::id(),
        ]));
    }

    // ========================================
    // RENDER METHOD
    // ========================================

    public function render()
    {
        $pelajarData = collect();
        $totalSiswa  = 0;

        if ($this->rombel) {
            $totalSiswa = RombelPelajar::where('rombel_id', $this->rombel->id)->count();
        }

        if ($this->selectedRombelPengajarId && $this->semesterId) {
            if ($this->cachedNilaiExist === null) {
                $this->loadNilaiPelajar();
            }

            $nilaiExist = $this->cachedNilaiExist ?? collect();

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy(
                    Pelajar::select('nama_lengkap')
                        ->whereColumn('pelajars.id', 'rombel_pelajars.pelajar_id')
                )
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($nilaiExist) {
                $pelajarId   = $rombelPelajar->pelajar_id;
                $nilaiRecord = $nilaiExist->get($pelajarId);

                if (!array_key_exists($pelajarId, $this->nilaiInput)) {
                    $this->nilaiInput[$pelajarId] = $nilaiRecord->nilai_angka ?? null;
                }

                return (object) [
                    'rombel_pelajar_id'  => $rombelPelajar->id,
                    'pelajar_id'         => $pelajarId,
                    'nama_lengkap'       => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk'        => $rombelPelajar->pelajar->nomor_induk,
                    'nisn'               => $rombelPelajar->pelajar->nisn,
                    'nilai_sekarang'     => $nilaiRecord->nilai_angka ?? null,
                    'capaian_kompetensi' => $nilaiRecord->capaian_kompetensi ?? null,
                ];
            });
        }

        // Resolve selected semester label for display
        $selectedSemesterObj = $this->semesterId
            ? collect($this->semesterList)->firstWhere('id', $this->semesterId)
            : null;

        return view('livewire.wali.entri-nilai', [
            'pelajarData'          => $pelajarData,
            'totalSiswa'           => $totalSiswa,
            'selectedSemesterObj'  => $selectedSemesterObj,
        ]);
    }
}
