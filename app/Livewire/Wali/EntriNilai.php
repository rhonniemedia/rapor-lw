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
    ];

    protected $messages = [
        'nilaiInput.*.numeric' => 'Nilai harus berupa angka',
        'nilaiInput.*.min' => 'Nilai minimal adalah 0',
        'nilaiInput.*.max' => 'Nilai maksimal adalah 100',
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

        $this->loadMataPelajaran();

        if ($this->selectedRombelPengajarId) {
            $this->loadNilaiPelajar();
        }
    }

    protected $listeners = ['deleteNilai'];

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
            ->pluck('nilai_angka', 'pelajar_id');
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
        if ($nilai >= 91) return 'A'; // Sangat Baik
        if ($nilai >= 83) return 'B'; // Baik
        if ($nilai >= 75) return 'C'; // Cukup / Tuntas
        return 'D'; // Kurang / Belum Tuntas
    }


    public function saveNilai(): void
    {
        if (!$this->selectedRombelPengajarId || !$this->semesterAktif) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Silakan pilih mata pelajaran terlebih dahulu.',
            ]);
            return;
        }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            $this->dispatch('swal:error', [
                'title' => 'Validasi Gagal!',
                'text' => 'Periksa input nilai Anda. ' . implode(', ', array_map(fn($err) => implode(', ', $err), $e->errors())),
            ]);
            return;
        }

        $rombelPengajar = RombelPengajar::with('mataPelajaran')
            ->find($this->selectedRombelPengajarId);

        if (!$rombelPengajar) {
            Log::error('RombelPengajar not found', ['id' => $this->selectedRombelPengajarId]);
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Data mata pelajaran tidak ditemukan.',
            ]);
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

                if ($nilai === null || $nilai === '') {
                    continue;
                }

                $nilaiBersih = is_numeric($nilai) ? floatval($nilai) : null;

                if ($nilaiBersih === null || $nilaiBersih < 0 || $nilaiBersih > 100) {
                    continue;
                }

                $predikat = $this->hitungPredikat($nilaiBersih);

                try {
                    $existingNilai = Nilai::where([
                        'pelajar_id' => $pelajarId,
                        'mata_pelajaran_id' => $mataPelajaranId,
                        'tahun_ajaran_semester_id' => $this->semesterAktif->id,
                    ])->lockForUpdate()->first();

                    $dataToSave = [
                        'rombel_pengajar_id' => $this->selectedRombelPengajarId,
                        'guru_id' => $guruId,
                        'nilai_angka' => $nilaiBersih,
                        'predikat' => $predikat,
                        'updated_by' => $userId,
                    ];

                    if ($existingNilai) {
                        $existingNilai->update($dataToSave);
                    } else {
                        $dataToSave['pelajar_id'] = $pelajarId;
                        $dataToSave['mata_pelajaran_id'] = $mataPelajaranId;
                        $dataToSave['tahun_ajaran_semester_id'] = $this->semesterAktif->id;
                        $dataToSave['created_by'] = $userId;

                        Nilai::create($dataToSave);
                    }

                    $savedCount++;
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->errorInfo[1] == 1062) {
                        Nilai::where([
                            'pelajar_id' => $pelajarId,
                            'mata_pelajaran_id' => $mataPelajaranId,
                            'tahun_ajaran_semester_id' => $this->semesterAktif->id,
                        ])->update([
                            'rombel_pengajar_id' => $this->selectedRombelPengajarId,
                            'guru_id' => $guruId,
                            'nilai_angka' => $nilaiBersih,
                            'predikat' => $predikat,
                            'updated_by' => $userId,
                        ]);

                        $savedCount++;
                    } else {
                        throw $e;
                    }
                }
            }

            DB::commit();

            $this->nilaiInput = [];
            $this->cachedNilaiExist = null;
            $this->loadNilaiPelajar();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => "Berhasil menyimpan {$savedCount} nilai untuk mata pelajaran '{$mataPelajaran}'.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving nilai', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'rombel_pengajar_id' => $this->selectedRombelPengajarId,
                'semester_id' => $this->semesterAktif->id,
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Gagal menyimpan nilai: ' . $e->getMessage(),
            ]);
        }
    }

    public function resetNilai(): void
    {
        if ($this->cachedNilaiExist === null) {
            $this->loadNilaiPelajar();
        }

        foreach ($this->nilaiInput as $pelajarId => $nilai) {
            if ($this->cachedNilaiExist && $this->cachedNilaiExist->has($pelajarId)) {
                $this->nilaiInput[$pelajarId] = $this->cachedNilaiExist->get($pelajarId);
            } else {
                $this->nilaiInput[$pelajarId] = null;
            }
        }

        $this->dispatch('swal:info', [
            'title' => 'Direset!',
            'text' => 'Input nilai telah dikembalikan ke nilai tersimpan.',
        ]);
    }

    // ✅ PERBAIKAN: Jangan ubah state, langsung trigger SweetAlert dari frontend
    public function confirmDelete($pelajarId): void
    {
        // Hanya dispatch event, TIDAK ubah state apapun
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Nilai Pelajar?',
            'text' => 'Anda yakin ingin menghapus nilai ini?',
            'icon' => 'warning',
            'confirmButtonText' => 'Ya, Hapus!',
            'nextEvent' => 'deleteNilai',
            'id' => $pelajarId
        ]);
    }

    public function deleteNilai($pelajarId = null): void
    {
        if (is_array($pelajarId) && isset($pelajarId[0])) {
            $pelajarId = $pelajarId[0];
        }

        if (!$pelajarId || !$this->selectedRombelPengajarId || !$this->semesterAktif) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'ID Pelajar tidak ditemukan atau data rombel/semester tidak valid.',
            ]);
            return;
        }

        try {
            $rombelPengajar = RombelPengajar::find($this->selectedRombelPengajarId);
            $deleted = Nilai::where('pelajar_id', $pelajarId)
                ->where('mata_pelajaran_id', $rombelPengajar->mata_pelajaran_id)
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                ->delete();

            if ($deleted) {
                // ✅ Reset input nilai untuk pelajar yang dihapus
                if (isset($this->nilaiInput[$pelajarId])) {
                    unset($this->nilaiInput[$pelajarId]);
                }

                // ✅ Reload cache dan data
                $this->cachedNilaiExist = null;
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
            Log::error('Error deleting nilai: ' . $e->getMessage(), ['pelajar_id' => $pelajarId]);
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat menghapus nilai.',
            ]);
        }
    }

    public function render()
    {
        $pelajarData = collect();
        $totalSiswa = 0;

        if ($this->rombel) {
            $totalSiswa = RombelPelajar::where('rombel_id', $this->rombel->id)->count();
        }

        if ($this->selectedRombelPengajarId && $this->semesterAktif) {
            $nilaiExist = $this->cachedNilaiExist ?? collect();

            $pelajarPaginated = $this->getPelajarQuery()
                ->orderBy('id', 'asc')
                ->paginate($this->perPagePelajar);

            $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($nilaiExist) {
                $pelajarId = $rombelPelajar->pelajar_id;

                if (!array_key_exists($pelajarId, $this->nilaiInput)) {
                    $this->nilaiInput[$pelajarId] = $nilaiExist->get($pelajarId);
                }

                return (object) [
                    'rombel_pelajar_id' => $rombelPelajar->id,
                    'pelajar_id' => $pelajarId,
                    'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                    'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                    'nisn' => $rombelPelajar->pelajar->nisn,
                    'nilai_sekarang' => $nilaiExist->get($pelajarId),
                ];
            });
        }

        return view('livewire.wali.entri-nilai', [
            'pelajarData' => $pelajarData,
            'totalSiswa' => $totalSiswa,
        ]);
    }
}
