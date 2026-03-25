<?php

namespace App\Livewire\Wali;

use App\Models\Rombel;
use Livewire\Component;
use App\Models\Pelajar;
use Livewire\WithPagination;
use App\Models\Kokurikuler;
use App\Models\RombelPelajar;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\TemplateKokurikulerCapaian; // DITAMBAHKAN
use Illuminate\Database\Eloquent\Builder;

class EntriKokurikuler extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Properties
    public $rombel;
    public $semesterAktif;

    // Search & Pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // Input kokurikuler (DIUBAH MENJADI SATU ARRAY MULTIDIMENSI SEPERTI INPUTKOKURIKULER)
    public $kokurikulerInput = [];
    public $generateMode = 'empty'; // DITAMBAHKAN

    // Cache
    private $cachedKokurikulerExist = null;

    // Predikat Options (DIUBAH SESUAI ACUAN: A, B, C)
    public $predikatOptions = [
        'A' => 'Mahir',
        'B' => 'Cakap',
        'C' => 'Berkembang',
    ];

    // Query string
    protected $queryString = [
        'searchPelajar' => ['except' => ''],
    ];

    // Validation
    protected $rules = [
        // DIUBAH MENYESUAIKAN STRUKTUR BARU DAN PILIHAN PREDIIKAT (A, B, C)
        'kokurikulerInput.*.predikat' => 'nullable|string|in:A,B,C',
        'kokurikulerInput.*.capaian' => 'nullable|string|max:1000',
        'generateMode' => 'required|in:empty,all', // DITAMBAHKAN
    ];

    protected $messages = [
        'kokurikulerInput.*.predikat.in' => 'Predikat harus salah satu dari: A, B, C',
        'kokurikulerInput.*.capaian.string' => 'Capaian harus berupa teks',
        'kokurikulerInput.*.capaian.max' => 'Capaian maksimal 1000 karakter',
        'generateMode.required' => 'Pilih mode generate', // DITAMBAHKAN
        'generateMode.in' => 'Mode generate tidak valid', // DITAMBAHKAN
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

        $this->loadKokurikulerPelajar();
    }

    protected $listeners = [
        'deleteKokurikuler',
        'confirmResetKokurikuler' => 'resetKokurikuler',
        // DITAMBAHKAN UNTUK MODAL
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

    public function updatingSearchPelajar(): void
    {
        $this->resetPage();
    }

    private function loadKokurikulerPelajar(): void
    {
        if (!$this->semesterAktif || !$this->rombel) {
            $this->kokurikulerInput = [];
            $this->cachedKokurikulerExist = null;
            return;
        }

        // AMBIL SEMUA data kokurikuler di rombel ini untuk semester aktif
        $kokurikulerData = Kokurikuler::where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->whereIn('pelajar_id', function ($q) {
                $q->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombel->id);
            })
            ->get()
            ->keyBy('pelajar_id');

        $this->cachedKokurikulerExist = $kokurikulerData;
        $this->kokurikulerInput = [];

        // Mengisi input array dari cache
        foreach ($kokurikulerData as $pelajarId => $data) {
            $this->kokurikulerInput[$pelajarId] = [
                'predikat' => $data->predikat ?? null,
                'capaian' => $data->capaian ?? '',
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

    public function saveKokurikuler(): void
    {
        if (!$this->semesterAktif || !$this->rombel) {
            $this->dispatchError('Tidak ada semester aktif atau kelas binaan.');
            return;
        }

        try {
            // Menggunakan rules yang disederhanakan
            $this->validate([
                'kokurikulerInput.*.predikat' => 'nullable|string|in:A,B,C',
                'kokurikulerInput.*.capaian' => 'nullable|string|max:1000',
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
            $result = $this->processKokurikulerSaving($validPelajarIds); // Panggil processSaving

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
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'semester_id' => $this->semesterAktif->id,
            ]);

            $this->dispatchError('Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    // DIADOPSI DARI InputKokurikuler.php
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
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                ->first();

            // Logic untuk menghapus jika predikat dikosongkan (Mengikuti acuan)
            if (empty($predikat) && empty($capaian)) {
                if ($existingData) {
                    $existingData->delete();
                    $deletedCount++;
                }
                continue;
            }

            // Memastikan predikat yang diinput valid (A, B, atau C)
            if (!in_array($predikat, array_keys($this->predikatOptions)) && !empty($predikat)) {
                continue;
            }

            // Jika ada predikat yang terisi, simpan/update
            if ($existingData) {
                $existingData->update([
                    'predikat' => $predikat,
                    'capaian' => $capaian,
                    'guru_id' => $guruId,
                    'tanggal_input' => $tanggalInput,
                    'updated_by' => $guruId,
                ]);
                $updatedCount++;
            } else {
                // Hanya buat baru jika ada predikat yang dipilih
                if (!empty($predikat)) {
                    Kokurikuler::create([
                        'pelajar_id' => $pelajarId,
                        'guru_id' => $guruId,
                        'tahun_ajaran_semester_id' => $this->semesterAktif->id,
                        'predikat' => $predikat,
                        'capaian' => $capaian,
                        'tanggal_input' => $tanggalInput,
                        'created_by' => $guruId,
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

    // DIADOPSI DARI InputKokurikuler.php
    private function buildSaveSuccessMessage(string $rombelNama, array $result): string
    {
        $messages = [];
        if ($result['savedCount'] > 0) $messages[] = "{$result['savedCount']} baru";
        if ($result['updatedCount'] > 0) $messages[] = "{$result['updatedCount']} diperbarui";
        if ($result['deletedCount'] > 0) $messages[] = "{$result['deletedCount']} dihapus";

        return "Berhasil menyimpan data kokurikuler untuk {$rombelNama}: " . implode(", ", $messages);
    }

    public function resetKokurikuler(): void
    {
        if ($this->cachedKokurikulerExist === null) {
            $this->loadKokurikulerPelajar();
        }

        // Mengembalikan input ke nilai tersimpan (cache)
        foreach ($this->cachedKokurikulerExist as $pelajarId => $data) {
            $this->kokurikulerInput[$pelajarId] = [
                'predikat' => $data->predikat ?? null,
                'capaian' => $data->capaian ?? '',
            ];
        }

        // Mengosongkan input yang tidak ada di cache
        foreach ($this->kokurikulerInput as $pelajarId => $input) {
            if (!isset($this->cachedKokurikulerExist[$pelajarId])) {
                $this->kokurikulerInput[$pelajarId] = [
                    'predikat' => null,
                    'capaian' => ''
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

        if (!$pelajarId || !$this->semesterAktif) {
            $this->dispatchError('ID Pelajar tidak ditemukan atau data tidak valid.', 'Gagal!');
            return;
        }

        $userId = Auth::id();

        try {
            $deleted = Kokurikuler::where('pelajar_id', $pelajarId)
                // Hapus data kokurikuler yang dibuat oleh guru ini (Wali Kelas)
                ->where('guru_id', $userId)
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                ->delete();

            if ($deleted) {
                // Menghapus data dari input array
                if (isset($this->kokurikulerInput[$pelajarId])) {
                    $this->kokurikulerInput[$pelajarId] = [
                        'predikat' => null,
                        'capaian' => ''
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
    // GENERATE CAPAIAN METHODS (DIADOPSI)
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
        // Mengikuti logika acuan yang dioptimalkan untuk menghitung Capaian Kosong
        $countPelajarWithKokurikuler = Kokurikuler::where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->whereIn('pelajar_id', function ($query) {
                $query->select('pelajar_id')
                    ->from('rombel_pelajars')
                    ->where('rombel_id', $this->rombel->id);
            })
            ->count();

        $countCapaianKosong = Kokurikuler::where('tahun_ajaran_semester_id', $this->semesterAktif->id)
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
            'countCapaianKosong' => $countCapaianKosong,
            'countTemplateAvailable' => TemplateKokurikulerCapaian::where('tahun_ajaran_semester_id', $this->semesterAktif->id)
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
        // Reset generate mode saat modal ditutup
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
            $this->logError('generating capaian kokurikuler', $e);
            $this->dispatchError('Terjadi kesalahan saat generate capaian.');
        }
    }

    private function processCapaianGeneration(): array
    {
        $query = Kokurikuler::where('tahun_ajaran_semester_id', $this->semesterAktif->id)
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
        $successCount = 0;
        $errorList = [];

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
                $pelajar = $kokurikuler->pelajar;
                $errorList[] = [
                    'nama' => $pelajar->nama_lengkap ?? 'N/A',
                    'predikat' => $this->predikatOptions[$kokurikuler->predikat] ?? $kokurikuler->predikat,
                ];
            }
        }

        return ['successCount' => $successCount, 'errorList' => $errorList];
    }

    private function getMatchingTemplate(string $predikat): ?TemplateKokurikulerCapaian
    {
        return TemplateKokurikulerCapaian::where('tahun_ajaran_semester_id', $this->semesterAktif->id)
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

    // ========================================
    // VALIDATION METHODS
    // ========================================

    private function validateGenerateContext(): bool
    {
        if (!$this->rombel || !$this->semesterAktif) {
            $this->dispatchError('Pastikan Anda memiliki kelas binaan dan semester aktif.');
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

    // LOG ERROR METHOD TETAP, HANYA DIPASTIKAN MENGGUNAKAN PROPERTY ROMBEL YANG TEPAT

    // ========================================
    // RENDER METHOD
    // ========================================

    public function render()
    {
        $pelajarData = collect();

        if ($this->rombel && $this->semesterAktif) {

            // Pastikan cache sudah dimuat
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

                // Memastikan input array terisi dengan nilai tersimpan jika belum diubah
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

        return view('livewire.wali.entri-kokurikuler', [
            'pelajarData' => $pelajarData,
        ]);
    }
}
