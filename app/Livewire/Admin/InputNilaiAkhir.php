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

    // 🔹 Properti filter
    public $tahunAjaranId = null;
    public $semesterId = null;
    public $rombelId = null;

    // 🔹 Properti utama
    public $rombel;

    // 🔹 Properti pencarian & pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // 🔹 Data dropdown & display
    public $tahunAjaranList = [];
    public $semesterList = [];
    public $rombelList = [];
    public $mataPelajaranList = [];
    public $selectedRombelPengajarId = null;
    public $guruName = null;

    // 🔹 Input data nilai
    public $nilaiInput = [];

    // 🔹 Cache untuk optimasi
    // private $cachedNilaiExist = null;

    // 🔹 Properti untuk Generate Modal
    public $generateMode = 'empty'; // 'empty' atau 'all'
    // public $countPelajarWithNilai = 0;
    // public $countCapaianKosong = 0;
    // public $countTemplateAvailable = 0;

    // 🔹 Query string untuk persistensi state
    protected $queryString = [
        'tahunAjaranId' => ['except' => null],
        'semesterId' => ['except' => null],
        'rombelId' => ['except' => null],
        'selectedRombelPengajarId' => ['except' => null],
        'searchPelajar' => ['except' => ''],
    ];

    // 🔹 Event listener
    protected $listeners = [
        'saveNilaiConfirmed' => 'saveNilai',
        'resetNilaiConfirmed' => 'resetNilai',
        'deleteNilai' => 'deleteNilai',
        // 'resetModalState' => 'resetModalState', // Listener untuk membersihkan properti
    ];

    // 🔹 Validation rules
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
        $this->loadTahunAjaran();

        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        if ($activeTahunAjaran && !$this->tahunAjaranId) {
            $this->tahunAjaranId = $activeTahunAjaran->id;
        }

        if ($this->tahunAjaranId) {
            $this->loadSemester();

            $activeSemester = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahunAjaranId)
                ->where('status', 'aktif')
                ->first();
            if ($activeSemester && !$this->semesterId) {
                $this->semesterId = $activeSemester->id;
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
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Rombel tidak ditemukan.',
            ]);
        }
    }

    public function updatedTahunAjaranId(): void
    {
        $this->resetFilters();
        $this->loadSemester();

        $activeSemester = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahunAjaranId)
            ->where('status', 'aktif')
            ->first();
        if ($activeSemester) {
            $this->semesterId = $activeSemester->id;
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

    private function loadMataPelajaran(): void
    {
        if (!$this->rombelId) {
            $this->mataPelajaranList = [];
            return;
        }

        $query = RombelPengajar::with(['mataPelajaran', 'guru'])
            ->where('rombel_id', $this->rombelId);

        $this->mataPelajaranList = $query->orderBy('mata_pelajaran_id')->get();
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
            $this->nilaiInput = [];
            $this->guruName = null;
            $this->selectedRombelPengajarId = null;
            return;
        }

        $this->guruName = $rombelPengajar->guru->name ?? 'N/A';

        // Populate input form dengan nilai existing
        $nilaiExisting = Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->pluck('nilai_angka', 'pelajar_id');

        $this->nilaiInput = $nilaiExisting->toArray();
    }

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

    public function confirmSaveNilai(): void
    {
        if (!$this->selectedRombelPengajarId) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Silakan pilih mata pelajaran terlebih dahulu.',
            ]);
            return;
        }

        $this->validate();

        $this->dispatch('swal:confirm', [
            'title' => 'Simpan Nilai?',
            'text' => 'Semua nilai yang diinput akan disimpan.',
            'nextEvent' => 'saveNilaiConfirmed',
        ]);
    }

    private function hitungPredikat(float $nilai): string
    {
        if ($nilai >= 91) {
            return 'A';
        } elseif ($nilai >= 83) {
            return 'B';
        } elseif ($nilai >= 75) {
            return 'C';
        } else {
            return 'D';
        }
    }

    public function saveNilai(): void
    {
        if (!$this->selectedRombelPengajarId || !$this->semesterId) {
            return;
        }

        $rombelPengajar = RombelPengajar::with('mataPelajaran')
            ->find($this->selectedRombelPengajarId);

        if (!$rombelPengajar) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Data mata pelajaran tidak ditemukan.',
            ]);
            return;
        }

        $mataPelajaran = $rombelPengajar->mataPelajaran->nama;
        $mataPelajaranId = $rombelPengajar->mata_pelajaran_id;
        $guruId = $rombelPengajar->guru_id;

        $validPelajarIds = RombelPelajar::where('rombel_id', $this->rombelId)
            ->pluck('pelajar_id')
            ->toArray();

        DB::beginTransaction();
        try {
            $savedCount = 0;

            foreach ($this->nilaiInput as $pelajarId => $nilai) {
                if (!in_array($pelajarId, $validPelajarIds)) {
                    continue;
                }

                if ($nilai === null || $nilai === '') {
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
                        'rombel_pengajar_id' => $this->selectedRombelPengajarId,
                        'tahun_ajaran_semester_id' => $this->semesterId,
                    ],
                    [
                        'guru_id' => $guruId,
                        'nilai_angka' => $nilaiBersih,
                        'predikat' => $predikat,
                        'updated_by' => Auth::id(),
                        'created_by' => Auth::id(),
                    ]
                );

                $savedCount++;
            }

            DB::commit();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => "Berhasil menyimpan {$savedCount} nilai untuk mata pelajaran '{$mataPelajaran}'.",
            ]);

            $this->loadNilaiPelajar();
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving nilai: ' . $e->getMessage(), [
                'rombel_pengajar_id' => $this->selectedRombelPengajarId,
                'semester_id' => $this->semesterId,
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Gagal menyimpan nilai. Silakan coba lagi.',
            ]);
        }
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
        if (is_array($pelajarId) && isset($pelajarId[0])) {
            $pelajarId = $pelajarId[0];
        }

        if (!$pelajarId || !$this->selectedRombelPengajarId || !$this->semesterId) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'ID Pelajar tidak ditemukan atau data tidak valid.',
            ]);
            return;
        }

        try {
            $rombelPengajar = RombelPengajar::find($this->selectedRombelPengajarId);

            if (!$rombelPengajar) {
                throw new \Exception('Data mata pelajaran tidak ditemukan');
            }

            $deleted = Nilai::where('pelajar_id', $pelajarId)
                ->where('mata_pelajaran_id', $rombelPengajar->mata_pelajaran_id)
                ->where('rombel_pengajar_id', $this->selectedRombelPengajarId)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->delete();

            if ($deleted) {
                if (isset($this->nilaiInput[$pelajarId])) {
                    unset($this->nilaiInput[$pelajarId]);
                }

                $this->loadNilaiPelajar();

                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text' => 'Nilai berhasil dihapus.',
                ]);
            } else {
                $this->dispatch('swal:info', [
                    'title' => 'Info',
                    'text' => 'Nilai tidak ditemukan.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting nilai: ' . $e->getMessage(), [
                'pelajar_id' => $pelajarId,
                'user_id' => Auth::id(),
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat menghapus nilai.',
            ]);
        }
    }

    public function openGenerateModal(): void
    {
        if (!$this->selectedRombelPengajarId || !$this->semesterId || !$this->rombel) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Pastikan semua filter sudah dipilih.',
            ]);
            return;
        }

        $rombelPengajar = RombelPengajar::find($this->selectedRombelPengajarId);
        if (!$rombelPengajar) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Data mata pelajaran tidak ditemukan.',
            ]);
            return;
        }

        // ✅ HITUNG DI SINI tapi JANGAN simpan ke properti publik
        $countPelajarWithNilai = Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->count();

        $countCapaianKosong = Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->whereNull('capaian_kompetensi')
            ->count();

        $countTemplateAvailable = TemplateNilaiCapaian::where('tahun_ajaran_semester_id', $this->semesterId)
            ->where('mata_pelajaran_id', $rombelPengajar->mata_pelajaran_id)
            ->where('tingkat', $this->rombel->tingkat)
            ->where('aktif', true)
            ->count();

        if ($countTemplateAvailable === 0) {
            $this->dispatch('swal:error', [
                'title' => 'Template Tidak Ditemukan!',
                'text' => 'Belum ada template capaian untuk mata pelajaran dan tingkat kelas ini.',
            ]);
            return;
        }

        if ($countPelajarWithNilai === 0) {
            $this->dispatch('swal:error', [
                'title' => 'Tidak Ada Data!',
                'text' => 'Belum ada pelajar yang memiliki nilai tersimpan.',
            ]);
            return;
        }

        // ✅ Kirim data langsung ke event, bukan simpan di properti
        $this->dispatch('show-generate-modal', [
            'countPelajarWithNilai' => $countPelajarWithNilai,
            'countCapaianKosong' => $countCapaianKosong,
            'countTemplateAvailable' => $countTemplateAvailable,
        ]);
    }

    public function closeGenerateModal(): void
    {
        // Dispatch event ke JS untuk menutup modal
        $this->dispatch('hide-generate-modal');
    }

    // 🆕 NEW: Reset State Modal (Dipanggil dari JS)
    // public function resetModalState(): void
    // {
    //     $this->generateMode = 'empty';
    //     $this->countPelajarWithNilai = 0;
    //     $this->countCapaianKosong = 0;
    //     $this->countTemplateAvailable = 0;
    // }


    public function generateCapaian(): void
    {
        $this->validate([
            'generateMode' => 'required|in:empty,all',
        ]);

        if (!$this->selectedRombelPengajarId || !$this->semesterId || !$this->rombel) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Data tidak lengkap.',
            ]);
            return;
        }

        $rombelPengajar = RombelPengajar::find($this->selectedRombelPengajarId);
        if (!$rombelPengajar) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Data mata pelajaran tidak ditemukan.',
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            $query = Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
                ->where('tahun_ajaran_semester_id', $this->semesterId);

            if ($this->generateMode === 'empty') {
                $query->whereNull('capaian_kompetensi');
            }

            $nilaiList = $query->get();

            $successCount = 0;
            $errorList = [];

            foreach ($nilaiList as $nilai) {
                $template = $this->getMatchingTemplate(
                    $nilai->nilai_angka,
                    $rombelPengajar->mata_pelajaran_id
                );

                if ($template) {
                    $nilai->capaian_kompetensi = $template->deskripsi;
                    $nilai->updated_by = Auth::id();
                    $nilai->save();
                    $successCount++;
                } else {
                    $pelajar = $nilai->pelajar;
                    $errorList[] = [
                        'nama' => $pelajar->nama_lengkap ?? 'N/A',
                        'nilai' => $nilai->nilai_angka,
                    ];
                }
            }

            DB::commit();

            // Tutup modal: Gunakan closeGenerateModal yang akan dispatch event hide
            $this->closeGenerateModal();

            $this->loadNilaiPelajar();

            if (count($errorList) === 0) {
                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text' => "Berhasil generate capaian untuk {$successCount} pelajar.",
                ]);
            } else {
                $errorMessage = "Generate selesai dengan catatan:\n";
                $errorMessage .= "- Berhasil: {$successCount} pelajar\n";
                $errorMessage .= "- Gagal: " . count($errorList) . " pelajar (tidak ada template yang cocok)\n\n";
                $errorMessage .= "Detail error:\n";

                foreach ($errorList as $error) {
                    $errorMessage .= "- {$error['nama']} (nilai: {$error['nilai']}) - Template tidak ditemukan\n";
                }

                $this->dispatch('swal:warning', [
                    'title' => 'Generate Selesai!',
                    'text' => $errorMessage,
                ]);
            }
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error generating capaian: ' . $e->getMessage(), [
                'rombel_pengajar_id' => $this->selectedRombelPengajarId,
                'semester_id' => $this->semesterId,
                'user_id' => Auth::id(),
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat generate capaian.',
            ]);
        }
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

    // private function getCapaianKompetensi($pelajarId): ?string
    // {
    //     if (!$this->selectedRombelPengajarId || !$this->semesterId) {
    //         return null;
    //     }

    //     $nilai = Nilai::where('pelajar_id', $pelajarId)
    //         ->where('rombel_pengajar_id', $this->selectedRombelPengajarId)
    //         ->where('tahun_ajaran_semester_id', $this->semesterId)
    //         ->first();

    //     return $nilai->capaian_kompetensi ?? null;
    // }

    public function render()
    {
        $pelajarData = collect();

        if ($this->selectedRombelPengajarId && $this->semesterId) {
            // ✅ Query sekali saja untuk semua data nilai
            $allNilai = Nilai::where('rombel_pengajar_id', $this->selectedRombelPengajarId)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->get()
                ->keyBy('pelajar_id'); // Index by pelajar_id

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($allNilai) {
                $pelajarId = $rombelPelajar->pelajar_id;
                $nilaiRecord = $allNilai->get($pelajarId);

                // Inisialisasi input nilai jika belum ada
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
