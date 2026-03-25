<?php

namespace App\Livewire\Wali;

use App\Models\MataPelajaran;
use App\Models\Nilai;
use App\Models\Pelajar;
use App\Models\Rombel;
use App\Models\RombelPelajar;
use App\Models\RombelPengajar;
use App\Models\TahunAjaranSemester;
use App\Models\TemplateNilaiCapaian; // Pastikan model ini ada
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class KelasAjarNilai extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Properties
    public $rombelId;
    public $mataPelajaranId;
    public $rombel;
    public $mataPelajaran;
    public $rombelPengajar;
    public $semesterAktif;
    public $agamaTerkait = null;

    // Search & Pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // Input nilai & Generate
    public $nilaiInput = [];
    public $generateMode = 'empty'; // Tambahkan properti ini

    // Cache
    private $cachedNilaiExist = null;

    // Query string
    protected $queryString = [
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

    protected $listeners = ['deleteNilai', 'closeGenerateModal'];

    public function mount($rombelId, $mataPelajaranId)
    {
        $this->rombelId = $rombelId;
        $this->mataPelajaranId = $mataPelajaranId;

        $this->loadRombelData();
        $this->loadSemesterAktif();
        $this->validateAccess();
        $this->loadNilaiPelajar();
    }

    // ========================================
    // DATA LOADING & INITIALIZATION
    // ========================================

    private function loadRombelData()
    {
        $this->rombel = Rombel::with([
            'tahunAjaranKurikulum.tahunAjaran',
            'tahunAjaranKurikulum.kurikulum',
            'waliKelas',
            'jurusan'
        ])->find($this->rombelId);

        $this->mataPelajaran = MataPelajaran::find($this->mataPelajaranId);

        // 🚨 MODIFIKASI: Dapatkan agama terkait
        $this->agamaTerkait = $this->mataPelajaran->agama_terkait ?? null;

        if ($this->rombel && $this->mataPelajaran) {
            $this->rombelPengajar = RombelPengajar::where('rombel_id', $this->rombelId)
                ->where('mata_pelajaran_id', $this->mataPelajaranId)
                ->where('guru_id', Auth::id())
                ->with('guru')
                ->first();
        }
    }

    private function loadSemesterAktif()
    {
        $this->semesterAktif = TahunAjaranSemester::where('status', 'aktif')
            ->with(['semester', 'tahunAjaran'])
            ->first();
    }

    private function validateAccess()
    {
        if (!$this->rombel) {
            session()->flash('error', 'Rombongan belajar tidak ditemukan.');
            return redirect()->route('guru.kelas-ajar');
        }

        if (!$this->mataPelajaran) {
            session()->flash('error', 'Mata pelajaran tidak ditemukan.');
            return redirect()->route('guru.kelas-ajar');
        }

        if (!$this->rombelPengajar) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengajar kelas ini.');
            return redirect()->route('guru.kelas-ajar');
        }

        if (!$this->semesterAktif) {
            session()->flash('warning', 'Tidak ada semester aktif saat ini.');
        }
    }

    public function updatingSearchPelajar()
    {
        $this->resetPage();
    }

    private function loadNilaiPelajar()
    {
        if (!$this->rombelPengajar || !$this->semesterAktif) {
            $this->nilaiInput = [];
            $this->cachedNilaiExist = null;
            return;
        }

        // Muat seluruh objek Nilai, bukan hanya nilai_angka
        $this->cachedNilaiExist = Nilai::where('rombel_pengajar_id', $this->rombelPengajar->id)
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
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

        // 🚨 MODIFIKASI: Filter Pelajar berdasarkan Hash Agama
        if ($this->agamaTerkait) {
            // 1. Normalisasi string agama dari Mata Pelajaran
            $agamaFilterNormalized = Str::lower($this->agamaTerkait);

            // 2. Hitung hash yang dicari
            $agamaHashToFind = hash('sha256', $agamaFilterNormalized);

            $query->whereHas('pelajar', function ($q) use ($agamaHashToFind) {
                // 3. Bandingkan dengan kolom agama_hash di tabel pelajars
                $q->where('agama_hash', $agamaHashToFind);
            });
        }

        if (!empty($this->searchPelajar)) {
            $query->whereHas('pelajar', function ($q) {
                $search = $this->searchPelajar;
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nisn_hash', 'like', "%{$search}%")
                    ->orWhere('nomor_induk', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    private function hitungPredikat(float $nilai): string
    {
        // Sesuaikan rentang predikat
        if ($nilai >= 92) return 'A';
        if ($nilai >= 84) return 'B';
        if ($nilai >= 75) return 'C';
        return 'D';
    }

    // ========================================
    // SAVE & RESET NILAI
    // ========================================

    public function saveNilai()
    {
        if (!$this->rombelPengajar || !$this->semesterAktif) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Data tidak lengkap untuk menyimpan nilai.',
            ]);
            return;
        }

        try {
            // Hanya validasi nilai angka
            $this->validate(['nilaiInput.*' => $this->rules['nilaiInput.*']]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            $this->dispatch('swal:error', [
                'title' => 'Validasi Gagal!',
                'text' => 'Periksa input nilai Anda. ' . implode(', ', array_map(fn($err) => implode(', ', $err), $e->errors())),
            ]);
            return;
        }

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
                $predikat = $nilaiBersih !== null ? $this->hitungPredikat($nilaiBersih) : null;
                $isNilaiValid = $nilaiBersih !== null && $nilaiBersih >= 0 && $nilaiBersih <= 100;

                $existingNilai = Nilai::where([
                    'pelajar_id' => $pelajarId,
                    'mata_pelajaran_id' => $this->mataPelajaranId,
                    'tahun_ajaran_semester_id' => $this->semesterAktif->id,
                ])->first();

                if (!$isNilaiValid) {
                    // Jika input kosong atau tidak valid, hapus data yang sudah ada
                    if ($existingNilai) {
                        $existingNilai->delete();
                    }
                    continue;
                }

                $dataToSave = [
                    'rombel_pengajar_id' => $this->rombelPengajar->id,
                    'guru_id' => $userId,
                    'nilai_angka' => $nilaiBersih,
                    'predikat' => $predikat,
                    'updated_by' => $userId,
                ];

                if ($existingNilai) {
                    $existingNilai->update($dataToSave);
                } else {
                    $dataToSave['pelajar_id'] = $pelajarId;
                    $dataToSave['mata_pelajaran_id'] = $this->mataPelajaranId;
                    $dataToSave['tahun_ajaran_semester_id'] = $this->semesterAktif->id;
                    $dataToSave['created_by'] = $userId;

                    Nilai::create($dataToSave);
                }
                $savedCount++;
            }

            DB::commit();

            $this->nilaiInput = [];
            $this->cachedNilaiExist = null;
            $this->loadNilaiPelajar();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => "Berhasil menyimpan {$savedCount} nilai untuk mata pelajaran '{$this->mataPelajaran->nama}'.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving nilai', [
                'message' => $e->getMessage(),
                'rombel_pengajar_id' => $this->rombelPengajar->id,
                'semester_id' => $this->semesterAktif->id,
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Gagal menyimpan nilai: ' . $e->getMessage(),
            ]);
        }
    }

    public function resetNilai()
    {
        if ($this->cachedNilaiExist === null) {
            $this->loadNilaiPelajar();
        }

        // Reset input ke nilai yang ada di cache
        foreach ($this->nilaiInput as $pelajarId => $nilai) {
            $existing = $this->cachedNilaiExist->get($pelajarId);
            $this->nilaiInput[$pelajarId] = $existing ? $existing->nilai_angka : null;
        }

        $this->dispatch('swal:info', [
            'title' => 'Direset!',
            'text' => 'Input nilai telah dikembalikan ke nilai tersimpan.',
        ]);
    }

    public function deleteNilai($pelajarId = null): void
    {
        if (is_array($pelajarId) && isset($pelajarId[0])) {
            $pelajarId = $pelajarId[0];
        }

        if (!$pelajarId || !$this->rombelPengajar || !$this->semesterAktif) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'ID Pelajar tidak ditemukan atau data tidak valid.',
            ]);
            return;
        }

        try {
            $deleted = Nilai::where('pelajar_id', $pelajarId)
                ->where('mata_pelajaran_id', $this->mataPelajaranId)
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                ->where('guru_id', Auth::id())
                ->delete();

            if ($deleted) {
                if (isset($this->nilaiInput[$pelajarId])) {
                    $this->nilaiInput[$pelajarId] = null;
                }

                $this->cachedNilaiExist = null;
                $this->loadNilaiPelajar();

                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text' => 'Nilai berhasil dihapus.',
                ]);
            } else {
                $this->dispatch('swal:info', [
                    'title' => 'Info',
                    'text' => 'Nilai tidak ditemukan atau Anda tidak memiliki akses.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting nilai: ' . $e->getMessage());

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat menghapus nilai.',
            ]);
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

    private function validateGenerateContext(): bool
    {
        if (!$this->rombelPengajar || !$this->semesterAktif) {
            $this->dispatch('swal:error', ['title' => 'Error!', 'text' => 'Data Rombel atau Semester tidak valid.']);
            return false;
        }
        return true;
    }

    private function calculateGenerateStatistics(): array
    {
        if (!$this->cachedNilaiExist) {
            $this->loadNilaiPelajar();
        }

        $countPelajarWithNilai = $this->cachedNilaiExist->filter(fn($nilai) => $nilai->nilai_angka !== null)->count();

        $countDeskripsiKosong = $this->cachedNilaiExist->filter(
            fn($nilai) =>
            $nilai->nilai_angka !== null && (empty($nilai->deskripsi) || $nilai->deskripsi === null)
        )->count();

        $countTemplateAvailable = TemplateNilaiCapaian::where('mata_pelajaran_id', $this->mataPelajaranId)
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
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
            $this->dispatch('swal:error', ['title' => 'Template Tidak Ditemukan!', 'text' => 'Belum ada template deskripsi untuk mata pelajaran ini.']);
            return false;
        }

        if ($statistics['countPelajarWithNilai'] === 0) {
            $this->dispatch('swal:error', ['title' => 'Tidak Ada Data!', 'text' => 'Belum ada pelajar yang memiliki nilai angka tersimpan.']);
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
        $this->validate(['generateMode' => 'required|in:empty,all']);

        if (!$this->validateGenerateContext()) {
            return;
        }

        DB::beginTransaction();
        try {
            $result = $this->processDeskripsiGeneration();

            DB::commit();

            $this->closeGenerateModal();
            $this->cachedNilaiExist = null;
            $this->loadNilaiPelajar();

            $this->dispatchGenerateResult($result);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error generating deskripsi nilai', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $this->dispatch('swal:error', ['title' => 'Gagal!', 'text' => 'Terjadi kesalahan saat generate deskripsi.']);
        }
    }

    private function processDeskripsiGeneration(): array
    {
        $query = Nilai::where('rombel_pengajar_id', $this->rombelPengajar->id)
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->whereNotNull('nilai_angka');

        if ($this->generateMode === 'empty') {
            $query->where(function (Builder $q) {
                $q->whereNull('capaian_kompetensi')
                    ->orWhere('capaian_kompetensi', '');
            });
        }

        $nilaiList = $query->get();
        $successCount = 0;
        $errorList = [];

        foreach ($nilaiList as $nilai) {
            $template = $this->getMatchingTemplate($nilai->predikat);

            if ($template) {
                $deskripsi = $this->replacePlaceholders($template->deskripsi, $nilai);

                $nilai->capaian_kompetensi = $deskripsi;
                $nilai->updated_by = Auth::id();
                $nilai->save();
                $successCount++;
            } else {
                $pelajar = $nilai->pelajar()->first();
                $errorList[] = [
                    'nama' => $pelajar->nama_lengkap ?? 'N/A',
                    'predikat' => $nilai->predikat,
                ];
            }
        }

        return ['successCount' => $successCount, 'errorList' => $errorList];
    }

    private function getMatchingTemplate(string $predikat): ?TemplateNilaiCapaian
    {
        return TemplateNilaiCapaian::where('mata_pelajaran_id', $this->mataPelajaranId)
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->where('predikat', $predikat)
            ->first();
    }

    private function replacePlaceholders(string $deskripsi, $nilai): string
    {
        $predikatLabel = match ($nilai->predikat) {
            'A' => 'Sangat Baik',
            'B' => 'Baik',
            'C' => 'Cukup',
            default => 'Kurang'
        };

        $placeholders = [
            '{NILAI_ANGKA}' => number_format($nilai->nilai_angka, 2),
            '{PREDIKAT}' => $nilai->predikat,
            '{PREDIKAT_LABEL}' => $predikatLabel,
            '{MATA_PELAJARAN}' => $this->mataPelajaran->nama ?? '',
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $deskripsi);
    }

    private function dispatchGenerateResult(array $result): void
    {
        if (empty($result['errorList'])) {
            $this->dispatch('swal:success', ['title' => 'Berhasil!', 'text' => "Berhasil generate deskripsi untuk {$result['successCount']} pelajar."]);
            return;
        }

        $errorMessage = $this->buildGenerateErrorMessage($result);
        $this->dispatch('swal:error', ['title' => 'Generate Selesai dengan Error!', 'text' => $errorMessage]);
    }

    private function buildGenerateErrorMessage(array $result): string
    {
        $message = "Generate selesai dengan catatan:\n";
        $message .= "- Berhasil: {$result['successCount']} pelajar\n";
        $message .= "- Gagal: " . count($result['errorList']) . " pelajar (Template tidak ditemukan untuk predikat)\n\n";
        $message .= "Detail error:\n";

        foreach ($result['errorList'] as $error) {
            $message .= "- {$error['nama']} (Predikat: {$error['predikat']}) \n";
        }

        return $message;
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

        if ($this->rombelPengajar && $this->semesterAktif) {
            $nilaiExist = $this->cachedNilaiExist ?? collect();

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy(
                    Pelajar::select('nama_lengkap')
                        ->whereColumn('pelajars.id', 'rombel_pelajars.pelajar_id')
                )
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($nilaiExist) {
                $pelajarId = $rombelPelajar->pelajar_id;
                $existingNilai = $nilaiExist->get($pelajarId);

                if (!array_key_exists($pelajarId, $this->nilaiInput)) {
                    $this->nilaiInput[$pelajarId] = $existingNilai ? $existingNilai->nilai_angka : null;
                }

                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id' => $pelajarId,
                    'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                    'nisn' => $rombelPelajar->pelajar->nisn,
                    'nilai_sekarang' => $existingNilai ? $existingNilai->nilai_angka : null,
                    'predikat_sekarang' => $existingNilai ? $existingNilai->predikat : null,
                    'deskripsi_sekarang' => $existingNilai ? $existingNilai->capaian_kompetensi : null,
                ];
            });
        }

        return view('livewire.wali.kelas-ajar-nilai', [
            'pelajarData' => $pelajarData,
            'totalSiswa' => $totalSiswa,
            'cachedNilaiExist' => $this->cachedNilaiExist ?? collect(),
        ]);
    }
}
