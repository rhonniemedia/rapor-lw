<?php

namespace App\Livewire\Wali;

use App\Models\Nilai;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use App\Models\RombelPengajar;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class KelasAjar extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Search & Pagination
    public $searchRombel = '';
    public $perPageRombel = 10;

    // Query string
    protected $queryString = [
        'searchRombel' => ['except' => ''],
    ];

    public function updatingSearchRombel(): void
    {
        $this->resetPage();
    }

    /**
     * Mengambil query dasar Rombel Pengajar dengan filter Tahun Ajaran & Semester Aktif
     */
    private function getRombelQuery(): Builder
    {
        $user = Auth::user();

        // 1. Inisialisasi Query dengan Relasi
        $query = RombelPengajar::where('guru_id', $user->id)
            ->with([
                'rombel.jurusan',
                'rombel.tahunAjaranKurikulum.tahunAjaran',
                'rombel.tahunAjaranKurikulum.kurikulum',
                'rombel.waliKelas',
                'mataPelajaran'
            ])
            // 2. Filter Tahun Ajaran Aktif (berdasarkan tabel tahun_ajarans)
            ->whereHas('rombel.tahunAjaranKurikulum.tahunAjaran', function ($q) {
                $q->where('status', 'aktif');
            })
            // 3. Filter Semester Aktif (berdasarkan tabel tahun_ajaran_semesters)
            ->whereHas('rombel.tahunAjaranKurikulum.tahunAjaran.tahunAjaranSemesters', function ($q) {
                $q->where('status', 'aktif');
            })
            ->whereHas('rombel');

        // 4. Implementasi Search Functionality
        if (!empty($this->searchRombel)) {
            $query->where(function ($q) {
                $search = $this->searchRombel;

                // Cari berdasarkan nama rombel
                $q->whereHas('rombel', function ($subQ) use ($search) {
                    $subQ->where('nama', 'like', "%{$search}%");
                })
                    // Cari berdasarkan nama mata pelajaran
                    ->orWhereHas('mataPelajaran', function ($subQ) use ($search) {
                        $subQ->where('nama', 'like', "%{$search}%");
                    })
                    // Cari berdasarkan nama wali kelas
                    ->orWhereHas('rombel.waliKelas', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query;
    }

    public function detail($rombelId, $mataPelajaranId)
    {
        if (!$rombelId || !$mataPelajaranId) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Parameter tidak lengkap.'
            ]);
            return;
        }

        $this->redirect(route('walikelas.class.detail', [
            'rombelId' => $rombelId,
            'mataPelajaranId' => $mataPelajaranId
        ]));
    }

    public function render()
    {
        // Ambil data semester yang sedang aktif untuk memfilter perhitungan nilai
        $semesterAktif = TahunAjaranSemester::where('status', 'aktif')->first();

        $rombelPengajars = $this->getRombelQuery()
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPageRombel);

        $rombels = $rombelPengajars->through(function ($rombelPengajar) use ($semesterAktif) {
            $rombel = $rombelPengajar->rombel;
            $mataPelajaran = $rombelPengajar->mataPelajaran;

            // 1. Logika Filter Agama (Mapping Mapel Agama ke Pelajar yang sesuai)
            $agamaHash = null;
            if ($mataPelajaran->agama_terkait) {
                $agamaHash = hash('sha256', Str::lower($mataPelajaran->agama_terkait));
            }

            // 2. Ambil ID Pelajar yang relevan dalam rombel ini
            $relevantPelajarIds = $rombel->pelajars()
                ->when($agamaHash, function ($q) use ($agamaHash) {
                    return $q->where('agama_hash', $agamaHash);
                })
                ->pluck('pelajars.id');

            $totalPelajar = $relevantPelajarIds->count();

            // 3. Hitung statistik progres penilaian berdasarkan SEMESTER AKTIF
            $selesaiDinilai = 0;
            if ($semesterAktif && $totalPelajar > 0) {
                $selesaiDinilai = Nilai::where('mata_pelajaran_id', $rombelPengajar->mata_pelajaran_id)
                    ->where('tahun_ajaran_semester_id', $semesterAktif->id) // Filter krusial agar tidak muncul nilai semester lalu
                    ->whereIn('pelajar_id', $relevantPelajarIds)
                    ->where('guru_id', Auth::id())
                    ->distinct('pelajar_id')
                    ->count('pelajar_id');
            }

            return (object) [
                'id' => $rombel->id,
                'nama' => $rombel->nama,
                'tingkat' => $rombel->tingkat,
                'jurusan_nama' => $rombel->jurusan->nama ?? '-',
                'mata_pelajaran_nama' => $mataPelajaran->nama ?? '-',
                'mata_pelajaran_id' => $rombelPengajar->mata_pelajaran_id,
                'walikelas_name' => $rombel->waliKelas->name ?? 'Belum Ditentukan',
                'walikelas_telephone' => $rombel->waliKelas->telephone ?? '~',
                'total_pelajar' => $totalPelajar,
                'selesai_dinilai' => $selesaiDinilai,
                'tahun_ajaran' => $rombel->tahunAjaranKurikulum->tahunAjaran->nama ?? '-',
                'semester_aktif' => $semesterAktif->semester->nama ?? '-',
                'kurikulum' => $rombel->tahunAjaranKurikulum->kurikulum->nama ?? '-',
            ];
        });

        return view('livewire.wali.kelas-ajar', [
            'rombels' => $rombels,
            'semesterAktif' => $semesterAktif
        ]);
    }
}
