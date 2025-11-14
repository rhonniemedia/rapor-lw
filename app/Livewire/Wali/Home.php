<?php

namespace App\Livewire\Wali;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Rombel;
use App\Models\RombelPelajar;
use App\Models\Pelajar;
use App\Models\Nilai;
use App\Models\Kehadiran;
use App\Models\Kokurikuler;
use App\Models\EkskulPelajar;
use App\Models\CatatanWaliKelas;
use App\Models\TahunAjaranSemester;

class Home extends Component
{
    public $rombel;
    public $tahunAjaranSemester;

    // Statistik Utama
    public $totalSiswa = 0;
    public $siswaLakiLaki = 0;
    public $siswaPerempuan = 0;
    public $rataRataNilai = 0;
    public $nilaiTertinggi = 0;
    public $persentaseKehadiran = 0;
    public $siswaKehadiranRendah = 0;
    public $progressInput = 0;

    // Progress Detail
    public $progressNilai = 0;
    public $progressKokurikuler = 0;
    public $progressKehadiran = 0;
    public $progressEkstrakurikuler = 0;
    public $progressCatatan = 0;

    // Siswa yang perlu perhatian
    public $siswaPerluPerhatian = [];

    // Distribusi Nilai
    public $distribusiNilai = [
        'A' => ['persentase' => 0, 'jumlah' => 0],
        'B' => ['persentase' => 0, 'jumlah' => 0],
        'C' => ['persentase' => 0, 'jumlah' => 0],
        'D' => ['persentase' => 0, 'jumlah' => 0],
    ];

    // Aktivitas Terbaru
    public $aktivitasTerbaru = [];

    // Data untuk chart
    public $chartData = [];

    public function mount()
    {
        $this->loadWaliKelasData();
        $this->loadTahunAjaranSemester();

        if ($this->rombel && $this->tahunAjaranSemester) {
            $this->loadStatistikUtama();
            $this->loadProgressDetail();
            $this->loadSiswaPerluPerhatian();
            $this->loadDistribusiNilai();
            $this->loadAktivitasTerbaru();
            $this->prepareChartData();
        }
    }

    /**
     * Load data rombel yang diampu oleh wali kelas
     */
    protected function loadWaliKelasData()
    {
        $user = Auth::user();

        // Cari rombel berdasarkan slug wali kelas
        $this->rombel = Rombel::where('wali_kelas_slug', $user->slug)
            ->with(['jurusan', 'tahunAjaran'])
            ->first();
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
     * Load statistik utama (4 card di atas)
     */
    protected function loadStatistikUtama()
    {
        $rombelId = $this->rombel->id;
        $semesterId = $this->tahunAjaranSemester->id;

        // 1. Jumlah Siswa
        $siswaData = RombelPelajar::where('rombel_id', $rombelId)
            ->join('pelajars', 'rombel_pelajars.pelajar_id', '=', 'pelajars.id')
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN pelajars.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as laki_laki"),
                DB::raw("SUM(CASE WHEN pelajars.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as perempuan")
            )
            ->first();

        $this->totalSiswa = $siswaData->total;
        $this->siswaLakiLaki = $siswaData->laki_laki;
        $this->siswaPerempuan = $siswaData->perempuan;

        // 2. Rata-rata Nilai
        $nilaiStats = Nilai::whereHas('rombelPengajar', function ($query) use ($rombelId) {
            $query->where('rombel_id', $rombelId);
        })
            ->where('tahun_ajaran_semester_id', $semesterId)
            ->selectRaw('AVG(nilai_angka) as rata_rata, MAX(nilai_angka) as tertinggi')
            ->first();

        $this->rataRataNilai = round($nilaiStats->rata_rata ?? 0, 1);
        $this->nilaiTertinggi = round($nilaiStats->tertinggi ?? 0, 1);

        // 3. Kehadiran
        $kehadiranData = Kehadiran::where('rombel_id', $rombelId)
            ->where('tahun_ajaran_semester_id', $semesterId)
            ->get();

        if ($kehadiranData->count() > 0) {
            // Asumsi: jumlah hari efektif dalam semester = 100 hari
            $hariEfektif = 100;

            $totalHadirPersentase = 0;
            $siswaKehadiranRendah = 0;

            foreach ($kehadiranData as $kehadiran) {
                $totalTidakHadir = $kehadiran->jumlah_sakit + $kehadiran->jumlah_izin + $kehadiran->jumlah_tanpa_keterangan;
                $persentaseHadir = (($hariEfektif - $totalTidakHadir) / $hariEfektif) * 100;
                $totalHadirPersentase += $persentaseHadir;

                if ($persentaseHadir < 80) {
                    $siswaKehadiranRendah++;
                }
            }

            $this->persentaseKehadiran = round($totalHadirPersentase / $kehadiranData->count(), 0);
            $this->siswaKehadiranRendah = $siswaKehadiranRendah;
        }

        // 4. Progress Input
        $this->calculateProgressInput();
    }

    /**
     * Hitung progress input data
     */
    protected function calculateProgressInput()
    {
        $totalProgress = 0;
        $jumlahKategori = 5;

        // Hitung progress dari setiap kategori
        $totalProgress += $this->calculateProgressNilai();
        $totalProgress += $this->calculateProgressKokurikuler();
        $totalProgress += $this->calculateProgressKehadiran();
        $totalProgress += $this->calculateProgressEkstrakurikuler();
        $totalProgress += $this->calculateProgressCatatan();

        $this->progressInput = round($totalProgress / $jumlahKategori, 0);
    }

    /**
     * Load detail progress untuk setiap kategori
     */
    protected function loadProgressDetail()
    {
        $this->progressNilai = $this->calculateProgressNilai();
        $this->progressKokurikuler = $this->calculateProgressKokurikuler();
        $this->progressKehadiran = $this->calculateProgressKehadiran();
        $this->progressEkstrakurikuler = $this->calculateProgressEkstrakurikuler();
        $this->progressCatatan = $this->calculateProgressCatatan();
    }

    /**
     * Hitung progress nilai akademik
     */
    protected function calculateProgressNilai()
    {
        $rombelId = $this->rombel->id;
        $semesterId = $this->tahunAjaranSemester->id;

        $totalSiswa = $this->totalSiswa;

        // Hitung jumlah mata pelajaran yang diajar di rombel ini
        $jumlahMapel = DB::table('rombel_pengajars')
            ->where('rombel_id', $rombelId)
            ->count();

        if ($totalSiswa == 0 || $jumlahMapel == 0) {
            return 0;
        }

        // Total yang harus diinput
        $totalYangHarusDiinput = $totalSiswa * $jumlahMapel;

        // Total yang sudah diinput
        $totalSudahDiinput = Nilai::whereHas('rombelPengajar', function ($query) use ($rombelId) {
            $query->where('rombel_id', $rombelId);
        })
            ->where('tahun_ajaran_semester_id', $semesterId)
            ->count();

        return round(($totalSudahDiinput / $totalYangHarusDiinput) * 100, 0);
    }

    /**
     * Hitung progress kokurikuler
     */
    protected function calculateProgressKokurikuler()
    {
        $rombelId = $this->rombel->id;
        $semesterId = $this->tahunAjaranSemester->id;

        $siswaIds = RombelPelajar::where('rombel_id', $rombelId)
            ->pluck('pelajar_id');

        if ($siswaIds->count() == 0) {
            return 0;
        }

        $totalSudahDiinput = Kokurikuler::whereIn('pelajar_id', $siswaIds)
            ->where('tahun_ajaran_semester_id', $semesterId)
            ->count();

        return round(($totalSudahDiinput / $siswaIds->count()) * 100, 0);
    }

    /**
     * Hitung progress kehadiran
     */
    protected function calculateProgressKehadiran()
    {
        $rombelId = $this->rombel->id;
        $semesterId = $this->tahunAjaranSemester->id;

        $totalSiswa = $this->totalSiswa;

        if ($totalSiswa == 0) {
            return 0;
        }

        $totalSudahDiinput = Kehadiran::where('rombel_id', $rombelId)
            ->where('tahun_ajaran_semester_id', $semesterId)
            ->count();

        return round(($totalSudahDiinput / $totalSiswa) * 100, 0);
    }

    /**
     * Hitung progress ekstrakurikuler
     */
    protected function calculateProgressEkstrakurikuler()
    {
        $rombelId = $this->rombel->id;
        $semesterId = $this->tahunAjaranSemester->id;

        $siswaIds = RombelPelajar::where('rombel_id', $rombelId)
            ->pluck('pelajar_id');

        if ($siswaIds->count() == 0) {
            return 0;
        }

        $totalSudahDiinput = EkskulPelajar::whereIn('pelajar_id', $siswaIds)
            ->where('tahun_ajaran_semester_id', $semesterId)
            ->count();

        return round(($totalSudahDiinput / $siswaIds->count()) * 100, 0);
    }

    /**
     * Hitung progress catatan sikap
     */
    protected function calculateProgressCatatan()
    {
        $rombelId = $this->rombel->id;
        $semesterId = $this->tahunAjaranSemester->id;

        $siswaIds = RombelPelajar::where('rombel_id', $rombelId)
            ->pluck('pelajar_id');

        if ($siswaIds->count() == 0) {
            return 0;
        }

        $totalSudahDiinput = CatatanWaliKelas::whereIn('pelajar_id', $siswaIds)
            ->where('tahun_ajaran_semester_id', $semesterId)
            ->distinct('pelajar_id')
            ->count();

        return round(($totalSudahDiinput / $siswaIds->count()) * 100, 0);
    }

    /**
     * Load siswa yang memerlukan perhatian
     */
    protected function loadSiswaPerluPerhatian()
    {
        $rombelId = $this->rombel->id;
        $semesterId = $this->tahunAjaranSemester->id;

        $siswaIds = RombelPelajar::where('rombel_id', $rombelId)
            ->pluck('pelajar_id');

        $siswaPerhatian = [];

        foreach ($siswaIds as $siswaId) {
            $pelajar = Pelajar::find($siswaId);
            $alasan = [];
            $persentaseHadir = null;
            $nilaiRataRata = null;

            // Cek kehadiran
            $kehadiran = Kehadiran::where('pelajar_id', $siswaId)
                ->where('rombel_id', $rombelId)
                ->where('tahun_ajaran_semester_id', $semesterId)
                ->first();

            if ($kehadiran) {
                $hariEfektif = 100;
                $totalTidakHadir = $kehadiran->jumlah_sakit + $kehadiran->jumlah_izin + $kehadiran->jumlah_tanpa_keterangan;
                $persentaseHadir = (($hariEfektif - $totalTidakHadir) / $hariEfektif) * 100;

                if ($persentaseHadir < 80) {
                    $alasan[] = 'Kehadiran rendah (' . round($persentaseHadir, 0) . '%)';
                }
            }

            // Hitung nilai rata-rata
            $nilaiAvg = Nilai::where('pelajar_id', $siswaId)
                ->whereHas('rombelPengajar', function ($query) use ($rombelId) {
                    $query->where('rombel_id', $rombelId);
                })
                ->where('tahun_ajaran_semester_id', $semesterId)
                ->avg('nilai_angka');

            if ($nilaiAvg) {
                $nilaiRataRata = round($nilaiAvg, 1);
            }

            // Cek nilai rendah
            $nilaiRendah = Nilai::where('pelajar_id', $siswaId)
                ->whereHas('rombelPengajar', function ($query) use ($rombelId) {
                    $query->where('rombel_id', $rombelId);
                })
                ->where('tahun_ajaran_semester_id', $semesterId)
                ->where('nilai_angka', '<', 70)
                ->count();

            if ($nilaiRendah > 0) {
                $alasan[] = 'Nilai rendah di ' . $nilaiRendah . ' mapel';
            }

            // Cek data belum lengkap
            $nilaiCount = Nilai::where('pelajar_id', $siswaId)
                ->whereHas('rombelPengajar', function ($query) use ($rombelId) {
                    $query->where('rombel_id', $rombelId);
                })
                ->where('tahun_ajaran_semester_id', $semesterId)
                ->count();

            $jumlahMapel = DB::table('rombel_pengajars')
                ->where('rombel_id', $rombelId)
                ->count();

            if ($nilaiCount < $jumlahMapel) {
                $alasan[] = 'Data nilai belum lengkap';
            }

            if (count($alasan) > 0) {
                $siswaPerhatian[] = [
                    'nama' => $pelajar->nama_lengkap,
                    'alasan' => $alasan,
                    'badge_class' => $this->getBadgeClass($alasan),
                    'nilai_rata_rata' => $nilaiRataRata ?? '-',
                    'persentase_kehadiran' => $persentaseHadir ? round($persentaseHadir, 0) . '%' : '-',
                ];
            }
        }

        $this->siswaPerluPerhatian = collect($siswaPerhatian)->take(5)->toArray();
    }

    /**
     * Get badge class berdasarkan alasan
     */
    protected function getBadgeClass($alasan)
    {
        $hasKehadiranRendah = collect($alasan)->contains(fn($a) => str_contains($a, 'Kehadiran'));
        $hasNilaiRendah = collect($alasan)->contains(fn($a) => str_contains($a, 'Nilai rendah'));

        if ($hasNilaiRendah) {
            return 'badge-danger';
        } elseif ($hasKehadiranRendah) {
            return 'badge-warning';
        }
        return 'badge-info';
    }

    /**
     * Load distribusi nilai kelas
     */
    protected function loadDistribusiNilai()
    {
        $rombelId = $this->rombel->id;
        $semesterId = $this->tahunAjaranSemester->id;

        $nilaiData = Nilai::whereHas('rombelPengajar', function ($query) use ($rombelId) {
            $query->where('rombel_id', $rombelId);
        })
            ->where('tahun_ajaran_semester_id', $semesterId)
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
     * Load aktivitas terbaru
     */
    protected function loadAktivitasTerbaru()
    {
        $rombelId = $this->rombel->id;
        $semesterId = $this->tahunAjaranSemester->id;

        $aktivitas = [];

        // Aktivitas dari nilai
        $nilaiTerbaru = Nilai::whereHas('rombelPengajar', function ($query) use ($rombelId) {
            $query->where('rombel_id', $rombelId);
        })
            ->where('tahun_ajaran_semester_id', $semesterId)
            ->with(['guru', 'mataPelajaran', 'pelajar'])
            ->latest('created_at')
            ->take(3)
            ->get();

        foreach ($nilaiTerbaru as $nilai) {
            if ($nilai->guru) {
                $aktivitas[] = [
                    'user' => $nilai->guru->name,
                    'action' => 'menginput nilai ' . ($nilai->mataPelajaran->nama ?? 'Mata Pelajaran'),
                    'time' => $nilai->created_at,
                    'formatted_time' => $this->formatTimeAgo($nilai->created_at)
                ];
            }
        }

        // Aktivitas dari catatan wali kelas
        $catatanTerbaru = CatatanWaliKelas::whereIn('pelajar_id', function ($query) use ($rombelId) {
            $query->select('pelajar_id')
                ->from('rombel_pelajars')
                ->where('rombel_id', $rombelId);
        })
            ->where('tahun_ajaran_semester_id', $semesterId)
            ->with('guru')
            ->latest('created_at')
            ->take(2)
            ->get();

        foreach ($catatanTerbaru as $catatan) {
            if ($catatan->guru) {
                $aktivitas[] = [
                    'user' => $catatan->guru->name,
                    'action' => 'menambahkan catatan wali kelas',
                    'time' => $catatan->created_at,
                    'formatted_time' => $this->formatTimeAgo($catatan->created_at)
                ];
            }
        }

        // Sort by time
        $aktivitas = collect($aktivitas)
            ->sortByDesc('time')
            ->take(5)
            ->values()
            ->toArray();

        $this->aktivitasTerbaru = $aktivitas;
    }

    /**
     * Format waktu ke format "X jam lalu", "X hari lalu"
     */
    protected function formatTimeAgo($datetime)
    {
        $diff = now()->diffInMinutes($datetime);

        if ($diff < 60) {
            return $diff . ' menit lalu';
        } elseif ($diff < 1440) { // 24 jam
            $hours = floor($diff / 60);
            return $hours . ' jam lalu';
        } else {
            $days = floor($diff / 1440);
            return $days . ' hari lalu';
        }
    }

    /**
     * Prepare data untuk chart
     */
    protected function prepareChartData()
    {
        $this->chartData = [
            'labels' => ['Nilai', 'Kokurikuler', 'Kehadiran', 'Ekstrakurikuler'],
            'data' => [
                $this->progressNilai,
                $this->progressKokurikuler,
                $this->progressKehadiran,
                $this->progressEkstrakurikuler
            ]
        ];
    }

    /**
     * Hitung jumlah mapel yang belum lengkap
     */
    public function getMapelBelumLengkapProperty()
    {
        $rombelId = $this->rombel->id ?? null;

        if (!$rombelId) {
            return 0;
        }

        $jumlahMapel = DB::table('rombel_pengajars')
            ->where('rombel_id', $rombelId)
            ->count();

        $mapelSudahLengkap = Nilai::whereHas('rombelPengajar', function ($query) use ($rombelId) {
            $query->where('rombel_id', $rombelId);
        })
            ->where('tahun_ajaran_semester_id', $this->tahunAjaranSemester->id ?? null)
            ->distinct('mata_pelajaran_id')
            ->count('mata_pelajaran_id');

        return $jumlahMapel - $mapelSudahLengkap;
    }

    public function render()
    {
        return view('livewire.wali.home');
    }
}
