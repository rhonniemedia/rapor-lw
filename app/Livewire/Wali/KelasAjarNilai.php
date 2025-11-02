<?php

namespace App\Livewire\Wali;

use App\Models\Nilai;
use App\Models\Rombel;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RombelPelajar;
use App\Models\RombelPengajar;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

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

    // Search & Pagination
    public $searchPelajar = '';
    public $perPagePelajar = 50;

    // Input nilai
    public $nilaiInput = [];

    // Cache
    private $cachedNilaiExist = null;

    // Query string
    protected $queryString = [
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

    protected $listeners = ['deleteNilai'];

    public function mount($rombelId, $mataPelajaranId)
    {
        $this->rombelId = $rombelId;
        $this->mataPelajaranId = $mataPelajaranId;

        $this->loadRombelData();
        $this->loadSemesterAktif();
        $this->validateAccess();
        $this->loadNilaiPelajar();
    }

    private function loadRombelData()
    {
        $this->rombel = Rombel::with([
            'tahunAjaranKurikulum.tahunAjaran',
            'tahunAjaranKurikulum.kurikulum',
            'waliKelas',
            'jurusan'
        ])->find($this->rombelId);

        $this->mataPelajaran = MataPelajaran::find($this->mataPelajaranId);

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

        $this->cachedNilaiExist = Nilai::where('rombel_pengajar_id', $this->rombelPengajar->id)
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
        if ($nilai >= 90) return 'A';
        if ($nilai >= 75) return 'B';
        if ($nilai >= 60) return 'C';
        return 'D';
    }

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
            $this->validate();
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
                        'mata_pelajaran_id' => $this->mataPelajaranId,
                        'tahun_ajaran_semester_id' => $this->semesterAktif->id,
                    ])->lockForUpdate()->first();

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
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->errorInfo[1] == 1062) {
                        Nilai::where([
                            'pelajar_id' => $pelajarId,
                            'mata_pelajaran_id' => $this->mataPelajaranId,
                            'tahun_ajaran_semester_id' => $this->semesterAktif->id,
                        ])->update([
                            'rombel_pengajar_id' => $this->rombelPengajar->id,
                            'guru_id' => $userId,
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
                'text' => "Berhasil menyimpan {$savedCount} nilai untuk mata pelajaran '{$this->mataPelajaran->nama}'.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error saving nilai', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
                ->where('guru_id', Auth::id()) // ✅ Pastikan hanya guru yang bersangkutan
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
                    'text' => 'Nilai tidak ditemukan atau Anda tidak memiliki akses.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting nilai: ' . $e->getMessage(), [
                'pelajar_id' => $pelajarId,
                'mata_pelajaran_id' => $this->mataPelajaranId,
                'guru_id' => Auth::id(),
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat menghapus nilai.',
            ]);
        }
    }

    public function kembali()
    {
        return redirect()->route('guru.kelas-ajar');
    }

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

        return view('livewire.wali.kelas-ajar-nilai', [
            'pelajarData' => $pelajarData,
            'totalSiswa' => $totalSiswa,
            'cachedNilaiExist' => $this->cachedNilaiExist ?? collect(), // ✅ TAMBAHKAN INI
        ]);
    }
}
