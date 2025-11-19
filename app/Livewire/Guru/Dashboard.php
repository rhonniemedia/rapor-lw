<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Rombel;
use App\Models\RombelPengajar;
use App\Models\RombelPelajar;
use App\Models\Pelajar;
use App\Models\Nilai;
use App\Models\MataPelajaran;
use App\Models\TahunAjaranSemester;

class Dashboard extends Component
{
    public $guru;
    public $tahunAjaranSemester;

    // Data Header
    public $namaGuru = '';
    public $mataPelajaranUtama = '';
    public $tahunAjaran = '';
    public $semesterNama = '';

    // Statistik Card
    public $jumlahKelas = 0;
    public $progressInputNilai = 0;
    public $kelasBelumLengkap = 0;
    public $totalPelajar = 0;
    public $nilaiBelumDiinput = 0;

    // Daftar kelas untuk subtitle
    public $daftarKelasText = '';

    // Distribusi Nilai untuk Chart
    public $distribusiNilai = [
        'A' => ['persentase' => 0, 'jumlah' => 0],
        'B' => ['persentase' => 0, 'jumlah' => 0],
        'C' => ['persentase' => 0, 'jumlah' => 0],
        'D' => ['persentase' => 0, 'jumlah' => 0],
    ];

    // Progress per Kelas
    public $progressPerKelas = [];

    // Pelajar Belum Dinilai
    public $pelajarBelumDinilai = [];

    public function mount()
    {
        $this->guru = Auth::guard('web')->user();
        $this->loadTahunAjaranSemester();

        if ($this->guru && $this->tahunAjaranSemester) {
            $this->loadDataHeader();
            $this->loadStatistikCard();
            $this->loadDistribusiNilai();
            $this->loadProgressPerKelas();
            $this->loadPelajarBelumDinilai();
        }
    }

    /**
     * Load tahun ajaran semester yang aktif
     */
    protected function loadTahunAjaranSemester()
    {
        $this->tahunAjaranSemester = TahunAjaranSemester::where('status', 'aktif')
            ->with(['tahunAjaran', 'semester'])
            ->first();
    }

    /**
     * Load data untuk header
     */
    protected function loadDataHeader()
    {
        $this->namaGuru = $this->guru->name ?? 'Guru';

        // Ambil mata pelajaran pertama/utama yang diajar
        $rombelPengajar = RombelPengajar::where('guru_id', $this->guru->id)
            ->with('mataPelajaran')
            ->first();

        $this->mataPelajaranUtama = $rombelPengajar->mataPelajaran->nama ?? 'Mata Pelajaran';

        // Tahun ajaran dan semester
        if ($this->tahunAjaranSemester) {
            $this->tahunAjaran = $this->tahunAjaranSemester->tahunAjaran->nama ?? '2024/2025';
            $this->semesterNama = $this->tahunAjaranSemester->semester->nama ?? 'Ganjil';
        }
    }

    /**
     * Load statistik untuk 4 card
     */
    protected function loadStatistikCard()
    {
        $guruId = $this->guru->id;
        $semesterId = $this->tahunAjaranSemester->id;

        // 1. Jumlah Kelas yang Diampu
        $rombelIds = RombelPengajar::where('guru_id', $guruId)
            ->distinct('rombel_id')
            ->pluck('rombel_id');

        $this->jumlahKelas = $rombelIds->count();

        // Buat text daftar kelas (contoh: "X IPA 1, X IPA 2, XI IPA 1")
        $rombels = Rombel::whereIn('id', $rombelIds)->take(6)->pluck('nama');
        $this->daftarKelasText = $rombels->implode(', ');

        // 2. Total Pelajar dari semua kelas yang diampu
        $this->totalPelajar = RombelPelajar::whereIn('rombel_id', $rombelIds)
            ->distinct('pelajar_id')
            ->count('pelajar_id');

        // 3. Progress Input Nilai & Kelas Belum Lengkap
        $this->calculateProgressInput($guruId, $semesterId, $rombelIds);

        // 4. Nilai Belum Diinput
        $totalYangHarusDiinput = 0;
        $totalSudahDiinput = 0;

        $rombelPengajarIds = RombelPengajar::where('guru_id', $guruId)->pluck('id');

        foreach ($rombelPengajarIds as $rpId) {
            $rombelPengajar = RombelPengajar::find($rpId);
            if ($rombelPengajar) {
                $jumlahPelajar = RombelPelajar::where('rombel_id', $rombelPengajar->rombel_id)->count();
                $totalYangHarusDiinput += $jumlahPelajar;

                $nilaiSelesai = Nilai::where('rombel_pengajar_id', $rpId)
                    ->where('tahun_ajaran_semester_id', $semesterId)
                    ->whereNotNull('nilai_angka')
                    ->count();

                $totalSudahDiinput += $nilaiSelesai;
            }
        }

        $this->nilaiBelumDiinput = $totalYangHarusDiinput - $totalSudahDiinput;
    }

    /**
     * Hitung progress input nilai dan kelas belum lengkap
     */
    protected function calculateProgressInput($guruId, $semesterId, $rombelIds)
    {
        $rombelPengajars = RombelPengajar::where('guru_id', $guruId)->get();

        $totalYangHarusDiinput = 0;
        $totalSudahDiinput = 0;
        $kelasBelumLengkap = 0;

        foreach ($rombelPengajars as $rp) {
            $jumlahPelajar = RombelPelajar::where('rombel_id', $rp->rombel_id)->count();
            $totalYangHarusDiinput += $jumlahPelajar;

            $nilaiSelesai = Nilai::where('rombel_pengajar_id', $rp->id)
                ->where('tahun_ajaran_semester_id', $semesterId)
                ->whereNotNull('nilai_angka')
                ->count();

            $totalSudahDiinput += $nilaiSelesai;

            // Jika belum semua dinilai, kelas belum lengkap
            if ($nilaiSelesai < $jumlahPelajar) {
                $kelasBelumLengkap++;
            }
        }

        $this->progressInputNilai = $totalYangHarusDiinput > 0
            ? round(($totalSudahDiinput / $totalYangHarusDiinput) * 100, 0)
            : 0;

        $this->kelasBelumLengkap = $kelasBelumLengkap;
    }

    /**
     * Load distribusi nilai untuk chart
     */
    protected function loadDistribusiNilai()
    {
        $guruId = $this->guru->id;
        $semesterId = $this->tahunAjaranSemester->id;

        $nilaiData = Nilai::whereHas('rombelPengajar', function ($query) use ($guruId) {
            $query->where('guru_id', $guruId);
        })
            ->where('tahun_ajaran_semester_id', $semesterId)
            ->whereNotNull('predikat')
            ->selectRaw('predikat, COUNT(*) as jumlah')
            ->groupBy('predikat')
            ->get();

        $totalNilai = $nilaiData->sum('jumlah');

        if ($totalNilai > 0) {
            foreach ($nilaiData as $data) {
                $predikat = $data->predikat;
                if (isset($this->distribusiNilai[$predikat])) {
                    $this->distribusiNilai[$predikat]['jumlah'] = $data->jumlah;
                    $this->distribusiNilai[$predikat]['persentase'] = round(($data->jumlah / $totalNilai) * 100, 0);
                }
            }
        }
    }

    /**
     * Load progress per kelas
     */
    protected function loadProgressPerKelas()
    {
        $guruId = $this->guru->id;
        $semesterId = $this->tahunAjaranSemester->id;

        $rombelPengajars = RombelPengajar::where('guru_id', $guruId)
            ->with(['rombel', 'mataPelajaran'])
            ->get();

        $progressData = [];

        foreach ($rombelPengajars as $rombelPengajar) {
            $rombelId = $rombelPengajar->rombel_id;
            $rombel = $rombelPengajar->rombel;
            $mapel = $rombelPengajar->mataPelajaran;

            if (!$rombel) continue;

            // Jumlah pelajar di kelas
            $totalPelajar = RombelPelajar::where('rombel_id', $rombelId)->count();

            if ($totalPelajar == 0) continue;

            // Jumlah nilai yang sudah diinput
            $nilaiSelesai = Nilai::where('rombel_pengajar_id', $rombelPengajar->id)
                ->where('tahun_ajaran_semester_id', $semesterId)
                ->whereNotNull('nilai_angka')
                ->count();

            $pelajarBelumDinilai = $totalPelajar - $nilaiSelesai;
            $progressPersen = round(($nilaiSelesai / $totalPelajar) * 100, 0);

            // Tentukan badge dan status
            $badge = 'success';
            $status = 'LENGKAP';

            if ($progressPersen < 100) {
                if ($pelajarBelumDinilai > 10) {
                    $badge = 'danger';
                    $status = 'URGENT';
                } else {
                    $badge = 'warning';
                    $status = 'PRIORITAS';
                }
            }

            $progressData[] = [
                'nama' => $rombel->nama,
                'mata_pelajaran' => $mapel->nama ?? '-',
                'progress_persen' => $progressPersen,
                'nilai_selesai' => $nilaiSelesai,
                'total_pelajar' => $totalPelajar,
                'pelajar_belum_dinilai' => $pelajarBelumDinilai,
                'badge' => $badge,
                'status' => $status
            ];
        }

        // Urutkan berdasarkan priority (urgent dulu)
        usort($progressData, function ($a, $b) {
            $priority = ['danger' => 0, 'warning' => 1, 'success' => 2];
            return $priority[$a['badge']] <=> $priority[$b['badge']];
        });

        $this->progressPerKelas = $progressData;
    }

    /**
     * Load pelajar yang belum dinilai (limit 5 untuk tabel)
     */
    protected function loadPelajarBelumDinilai()
    {
        $guruId = $this->guru->id;
        $semesterId = $this->tahunAjaranSemester->id;

        // Ambil semua rombel yang diajar
        $rombelPengajars = RombelPengajar::where('guru_id', $guruId)->get();

        $pelajarData = [];

        foreach ($rombelPengajars as $rp) {
            // Ambil semua pelajar di rombel
            $pelajarIds = RombelPelajar::where('rombel_id', $rp->rombel_id)
                ->pluck('pelajar_id');

            // Ambil pelajar yang sudah dinilai
            $pelajarSudahDinilai = Nilai::where('rombel_pengajar_id', $rp->id)
                ->where('tahun_ajaran_semester_id', $semesterId)
                ->whereNotNull('nilai_angka')
                ->pluck('pelajar_id');

            // Pelajar yang belum dinilai
            $pelajarBelumDinilaiIds = $pelajarIds->diff($pelajarSudahDinilai);

            if ($pelajarBelumDinilaiIds->count() > 0) {
                $pelajars = Pelajar::whereIn('id', $pelajarBelumDinilaiIds)
                    ->limit(5 - count($pelajarData)) // Batasi total 5
                    ->get();

                foreach ($pelajars as $pelajar) {
                    $pelajarData[] = [
                        'nama' => $pelajar->nama_lengkap ?? $pelajar->nama,
                        'nis' => $pelajar->nomor_induk ?? '-',
                        'jenis_kelamin' => $pelajar->jenis_kelamin ?? 'L',
                        'foto' => $pelajar->foto,
                        'kelas' => $rp->rombel->nama ?? '-',
                    ];

                    if (count($pelajarData) >= 5) break 2;
                }
            }
        }

        $this->pelajarBelumDinilai = $pelajarData;
    }

    /**
     * Refresh data
     */
    public function refreshData()
    {
        $this->loadDataHeader();
        $this->loadStatistikCard();
        $this->loadDistribusiNilai();
        $this->loadProgressPerKelas();
        $this->loadPelajarBelumDinilai();

        $this->dispatch('data-refreshed');
    }

    public function render()
    {
        return view('livewire.guru.dashboard');
    }
}
