<?php

namespace App\Livewire\Guru;

use App\Models\Nilai;
use App\Models\Rombel;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use App\Models\RombelPengajar;
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

    public function mount()
    {
        // Initialization if needed
    }

    public function updatingSearchRombel(): void
    {
        $this->resetPage();
    }

    private function getRombelQuery(): Builder
    {
        $user = Auth::user();

        // Query rombel yang diampu oleh guru yang sedang login
        $query = RombelPengajar::where('guru_id', $user->id)
            ->with([
                'rombel.jurusan',
                'rombel.tahunAjaranKurikulum.tahunAjaran',
                'rombel.tahunAjaranKurikulum.kurikulum',
                'rombel.waliKelas',
                'mataPelajaran'
            ])
            ->whereHas('rombel'); // Pastikan rombel masih ada

        // Search functionality
        if (!empty($this->searchRombel)) {
            $query->where(function ($q) {
                $search = $this->searchRombel;

                // Search by rombel name
                $q->whereHas('rombel', function ($subQ) use ($search) {
                    $subQ->where('nama', 'like', "%{$search}%");
                })
                    // Search by mata pelajaran name
                    ->orWhereHas('mataPelajaran', function ($subQ) use ($search) {
                        $subQ->where('nama', 'like', "%{$search}%");
                    })
                    // Search by wali kelas name
                    ->orWhereHas('rombel.waliKelas', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query;
    }

    public function detail($rombelId, $mataPelajaranId)
    {
        // Validasi parameter
        if (!$rombelId || !$mataPelajaranId) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Parameter tidak lengkap.'
            ]);
            return;
        }

        // Redirect tanpa dependency injection
        $this->redirect(route('walikelas.class.detail', [
            'rombelId' => $rombelId,
            'mataPelajaranId' => $mataPelajaranId
        ]));
    }

    public function render()
    {
        $rombelPengajars = $this->getRombelQuery()
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPageRombel);

        // Transform data untuk menambahkan informasi jumlah pelajar dan statistik penilaian
        $rombels = $rombelPengajars->through(function ($rombelPengajar) {
            $rombel = $rombelPengajar->rombel;
            $mataPelajaran = $rombelPengajar->mataPelajaran;

            // 1. Tentukan Agama Terkait & Hash
            $agamaTerkait = $mataPelajaran->agama_terkait ?? null;
            $agamaHash = null;

            if ($agamaTerkait) {
                $agamaHash = hash('sha256', Str::lower($agamaTerkait));
            }

            // 2. Base query untuk pelajar di rombel ini
            $basePelajarQuery = $rombel->pelajars();

            // 3. Terapkan Filter Agama jika ada
            if ($agamaHash) {
                // Filter pelajar yang agamanya cocok dengan Mata Pelajaran
                $basePelajarQuery->where('agama_hash', $agamaHash);
            }

            // Hitung total pelajar yang relevan (sudah difilter jika Mapel Agama)
            $relevantPelajarIds = $basePelajarQuery->pluck('pelajars.id');
            $totalPelajar = $relevantPelajarIds->count();

            // Hitung pelajar yang sudah dinilai untuk mata pelajaran ini
            $selesaiDinilai = Nilai::where('mata_pelajaran_id', $rombelPengajar->mata_pelajaran_id)
                ->whereIn('pelajar_id', $relevantPelajarIds)
                ->where('guru_id', Auth::id())
                ->distinct('pelajar_id')
                ->count('pelajar_id');

            return (object) [
                'id' => $rombel->id,
                'nama' => $rombel->nama,
                'tingkat' => $rombel->tingkat,
                'jurusan_nama' => $rombel->jurusan->nama ?? '-',
                'mata_pelajaran_nama' => $rombelPengajar->mataPelajaran->nama ?? '-',
                'mata_pelajaran_id' => $rombelPengajar->mata_pelajaran_id,
                'walikelas_name' => $rombel->waliKelas->name ?? 'Belum Ditentukan',
                'walikelas_telephone' => $rombel->waliKelas->telephone ?? '~',
                'total_pelajar' => $totalPelajar,
                'selesai_dinilai' => $selesaiDinilai,
                'tahun_ajaran' => $rombel->tahunAjaranKurikulum->tahunAjaran->nama ?? '-',
                'kurikulum' => $rombel->tahunAjaranKurikulum->kurikulum->nama ?? '-',
            ];
        });

        return view('livewire.guru.kelas-ajar', [
            'rombels' => $rombels,
        ]);
    }
}
