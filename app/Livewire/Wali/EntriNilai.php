<?php

namespace App\Livewire\Wali;

use App\Models\Nilai;
use App\Models\Rombel;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RombelPelajar;
use App\Models\RombelPengajar;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\TemplateNilaiCapaian;
use Illuminate\Database\Eloquent\Builder;

class EntriNilai extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Properties
    public $rombel;
    public $semesterAktif;
    public $selectedRombelPengajarId = null;
    public $mataPelajaranList = [];
    public $guruName = null;

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
        'selectedRombelPengajarId' => ['except' => null],
        'searchPelajar' => ['except' => ''],
    ];

    // Validation
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
        $this->loadRombelWaliKelas();

        if (!$this->rombel) {
            session()->flash('error', 'Anda tidak memiliki kelas binaan.');
            // Mengarahkan ke dashboard walikelas.
            return;
        }

        $this->loadSemesterAktif();

        if (!$this->semesterAktif) {
            session()->flash('warning', 'Tidak ada semester aktif saat ini.');
        }

        $this->loadMataPelajaran();

        if ($this->selectedRombelPengajarId) {
            $this->loadNilaiPelajar();
        }
    }

    // LISTENER DIUBAH: Menghapus 'saveNilaiConfirmed'
    protected $listeners = [
        'deleteNilai',
        'resetNilaiConfirmed' => 'resetNilai',
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

    private function loadNilaiPelajar(): void
    {
        if (!$this->selectedRombelPengajarId || !$this->semesterAktif) {
            $this->nilaiInput = [];
            $this->guruName = null;
            $this->cachedNilaiExist = null;
            return;
        }

        $rombelPengajar = RombelPengajar::with('guru', 'mataPelajaran')
            ->find($this->selectedRombelPengajarId);

        if (!$rombelPengajar) {
            $this->nilaiInput = [];
            $this->guruName = null;
            $this->cachedNilaiExist = null;
            $this->selectedRombelPengajarId = null;
            return;
        }

        $this->guruName = $rombelPengajar->guru->name ?? 'N/A';

        $this->cachedNilaiExist = Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->select('pelajar_id', 'nilai_angka', 'capaian_kompetensi')
            ->get()
            ->keyBy('pelajar_id');
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

    private function hitungPredikat(float $nilai): string
    {
        if ($nilai >= 92) return 'A'; // Sangat Baik
        if ($nilai >= 84) return 'B'; // Baik
        if ($nilai >= 75) return 'C'; // Cukup / Tuntas
        return 'D'; // Kurang / Belum Tuntas
    }

    // METODE confirmSaveNilai() DIHAPUS

    public function saveNilai(): void
    {
        if (!$this->selectedRombelPengajarId || !$this->semesterAktif) {
            $this->dispatchError('Silakan pilih mata pelajaran terlebih dahulu.');
            return;
        }

        // Validasi Langsung
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

        $mataPelajaran = $rombelPengajar->mataPelajaran->nama;
        $mataPelajaranId = $rombelPengajar->mata_pelajaran_id;
        $guruId = $rombelPengajar->guru_id;

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
                        'pelajar_id' => $pelajarId,
                        'mata_pelajaran_id' => $mataPelajaranId,
                        'tahun_ajaran_semester_id' => $this->semesterAktif->id,
                    ],
                    [
                        'rombel_pengajar_id' => $this->selectedRombelPengajarId,
                        'guru_id' => $guruId,
                        'nilai_angka' => $nilaiBersih,
                        'predikat' => $predikat,
                        'updated_by' => $userId,
                        'created_by' => $userId,
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
            'title' => 'Reset Input Nilai?',
            'text' => 'Input nilai akan dikembalikan ke nilai yang tersimpan di database.',
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

        if (!$pelajarId || !$this->selectedRombelPengajarId || !$this->semesterAktif) {
            $this->dispatchError('ID Pelajar tidak ditemukan atau data rombel/semester tidak valid.', 'Gagal!');
            return;
        }

        try {
            $rombelPengajar = $this->getRombelPengajar();
            $deleted = Nilai::where('pelajar_id', $pelajarId)
                ->where('mata_pelajaran_id', $rombelPengajar->mata_pelajaran_id)
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
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
        if (!$this->selectedRombelPengajarId || !$this->semesterAktif || !$this->rombel) {
            $this->dispatchError('Pastikan semua filter sudah dipilih.');
            return false;
        }
        return true;
    }

    private function calculateGenerateStatistics($rombelPengajar): array
    {
        return [
            'countPelajarWithNilai' => Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                ->count(),
            'countCapaianKosong' => Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                ->whereNull('capaian_kompetensi')
                ->count(),
            'countTemplateAvailable' => TemplateNilaiCapaian::where('tahun_ajaran_semester_id', $this->semesterAktif->id)
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
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id);

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

    private function getMatchingTemplate(float $nilaiAngka, string $mataPelajaranId): ?TemplateNilaiCapaian
    {
        return TemplateNilaiCapaian::where('tahun_ajaran_semester_id', $this->semesterAktif->id)
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
            'semester_id' => $this->semesterAktif->id ?? 'N/A',
            'user_id' => Auth::id(),
        ]));
    }

    public function render()
    {
        $pelajarData = collect();
        $totalSiswa = 0;

        if ($this->rombel) {
            $totalSiswa = RombelPelajar::where('rombel_id', $this->rombel->id)->count();
        }

        if ($this->selectedRombelPengajarId && $this->semesterAktif) {
            if ($this->cachedNilaiExist === null) {
                $this->loadNilaiPelajar();
            }

            $nilaiExist = $this->cachedNilaiExist ?? collect();

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($nilaiExist) {
                $pelajarId = $rombelPelajar->pelajar_id;
                $nilaiRecord = $nilaiExist->get($pelajarId);

                if (!array_key_exists($pelajarId, $this->nilaiInput)) {
                    $this->nilaiInput[$pelajarId] = $nilaiRecord->nilai_angka ?? null;
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

        return view('livewire.wali.entri-nilai', [
            'pelajarData' => $pelajarData,
            'totalSiswa' => $totalSiswa,
        ]);
    }
}
