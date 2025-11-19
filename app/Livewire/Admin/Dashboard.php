<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Nilai;
use App\Models\Rombel;
use App\Models\Pelajar;
use Livewire\Component;
use App\Models\Kehadiran;
use App\Models\Pengaturan;
use App\Models\Kokurikuler;
use Livewire\WithPagination;
use App\Models\EkskulPelajar;
use App\Models\MataPelajaran;
use App\Models\RombelPelajar;
use App\Models\RombelPengajar;
use Illuminate\Support\Facades\DB;
use App\Models\TahunAjaranSemester;

class Dashboard extends Component
{
    public $totalPelajar;
    public $totalGuru;
    public $totalRombel;
    public $totalMataPelajaran;
    public $totalRombelByJurusan;
    public $deadline;
    public $progressPerJenjang;
    public $progressPerMapel;
    public $progressPerGuru;
    public $rombelTerendah;
    public $distribusiNilai;
    public $aktivitasTerbaru;
    public $jadwalPenting;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Dapatkan semester aktif
        $semesterAktif = TahunAjaranSemester::where('status', 'aktif')->first();

        if (!$semesterAktif) {
            // Jika tidak ada semester aktif, set default values
            $this->totalPelajar = 0;
            $this->totalGuru = 0;
            $this->totalRombel = 0;
            $this->totalMataPelajaran = 0;
            $this->totalRombelByJurusan = 'Tidak ada data';
            $this->deadline = null;
            $this->progressPerJenjang = $this->getDefaultProgressPerJenjang();
            $this->progressPerMapel = collect();
            $this->progressPerGuru = collect();
            $this->rombelTerendah = collect();
            $this->distribusiNilai = [];
            $this->aktivitasTerbaru = [];
            $this->jadwalPenting = [];
            return;
        }

        // Total Statistik
        $this->totalPelajar = Pelajar::count();
        $this->totalGuru = User::where('is_teacher', true)
            ->where('status', 'aktif')
            ->count();
        $this->totalRombel = Rombel::where('tahun_ajaran_id', $semesterAktif->tahun_ajaran_id)->count();

        // Total Mata Pelajaran Aktif
        $this->totalMataPelajaran = MataPelajaran::where('status', 'aktif')->count();

        // Breakdown Rombel by Jurusan
        $rombelByJurusan = DB::table('rombels')
            ->join('jurusans', 'rombels.jurusan_id', '=', 'jurusans.id')
            ->where('rombels.tahun_ajaran_id', $semesterAktif->tahun_ajaran_id)
            ->select('jurusans.nama', DB::raw('count(*) as total'))
            ->groupBy('jurusans.id', 'jurusans.nama')
            ->get();

        $this->totalRombelByJurusan = $rombelByJurusan->map(function ($item) {
            return $item->total . ' ' . $item->nama;
        })->implode(', ');

        // Deadline dari pengaturan
        $pengaturan = Pengaturan::where('tahun_ajaran_semester_id', $semesterAktif->id)->first();
        $this->deadline = $pengaturan ? $pengaturan->tanggal_rapor : null;

        // Progress per Jenjang (X, XI, XII)
        $this->progressPerJenjang = $this->calculateProgressPerJenjang($semesterAktif);

        // Progress per Mata Pelajaran
        $this->progressPerMapel = $this->calculateProgressPerMapel($semesterAktif);

        // Progress per Guru
        $this->progressPerGuru = $this->calculateProgressPerGuru($semesterAktif);

        // Rombel dengan Progress Terendah
        $this->rombelTerendah = $this->calculateRombelTerendah($semesterAktif);

        // Distribusi Nilai
        $this->distribusiNilai = $this->calculateDistribusiNilai($semesterAktif);

        // Aktivitas Terbaru
        $this->aktivitasTerbaru = $this->getAktivitasTerbaru();

        // Jadwal Penting (hardcoded untuk contoh, bisa dibuat dynamic dari tabel jadwal)
        $this->jadwalPenting = [
            ['tanggal' => '15 Des', 'kegiatan' => 'Deadline Input Nilai'],
            ['tanggal' => '20-22 Des', 'kegiatan' => 'Verifikasi Nilai'],
            ['tanggal' => '25 Des', 'kegiatan' => 'Generate Rapor'],
            ['tanggal' => '28 Des', 'kegiatan' => 'Pembagian Rapor'],
        ];
    }

    /**
     * Hitung progress per jenjang (Kelas X, XI, XII)
     */
    private function calculateProgressPerJenjang($semester)
    {
        $jenjangData = [];
        $tingkatMap = [10 => 'X', 'XI' => 'XI', 12 => 'XII'];

        foreach ([10, 11, 12] as $tingkat) {
            // Ambil semua rombel di tingkat ini
            $rombelIds = Rombel::where('tahun_ajaran_id', $semester->tahun_ajaran_id)
                ->where('tingkat', $tingkat)
                ->pluck('id');

            if ($rombelIds->isEmpty()) {
                $jenjangData[$tingkat] = [
                    'nama' => 'Kelas ' . ($tingkat == 10 ? 'X' : ($tingkat == 11 ? 'XI' : 'XII')),
                    'nilai' => 0,
                    'kokurikuler' => 0,
                    'kehadiran' => 0,
                    'ekstrakurikuler' => 0,
                ];
                continue;
            }

            // Hitung jumlah pelajar di tingkat ini
            $totalPelajar = DB::table('rombel_pelajars')
                ->whereIn('rombel_id', $rombelIds)
                ->distinct('pelajar_id')
                ->count('pelajar_id');

            if ($totalPelajar == 0) {
                $jenjangData[$tingkat] = [
                    'nama' => 'Kelas ' . ($tingkat == 10 ? 'X' : ($tingkat == 11 ? 'XI' : 'XII')),
                    'nilai' => 0,
                    'kokurikuler' => 0,
                    'kehadiran' => 0,
                    'ekstrakurikuler' => 0,
                ];
                continue;
            }

            // 1. Progress Nilai
            // Hitung berapa mapel yang harus dinilai per siswa
            $totalMapelPerSiswa = DB::table('rombel_pengajars')
                ->whereIn('rombel_id', $rombelIds)
                ->distinct('mata_pelajaran_id')
                ->count('mata_pelajaran_id');

            $targetNilai = $totalPelajar * $totalMapelPerSiswa;

            $nilaiSelesai = Nilai::where('tahun_ajaran_semester_id', $semester->id)
                ->whereHas('pelajar', function ($q) use ($rombelIds) {
                    $q->whereHas('rombels', function ($q2) use ($rombelIds) {
                        $q2->whereIn('rombels.id', $rombelIds);
                    });
                })
                ->count();

            $progressNilai = $targetNilai > 0 ? round(($nilaiSelesai / $targetNilai) * 100) : 0;

            // 2. Progress Kokurikuler
            $kokurikulerSelesai = Kokurikuler::where('tahun_ajaran_semester_id', $semester->id)
                ->whereHas('pelajar', function ($q) use ($rombelIds) {
                    $q->whereHas('rombels', function ($q2) use ($rombelIds) {
                        $q2->whereIn('rombels.id', $rombelIds);
                    });
                })
                ->distinct('pelajar_id')
                ->count('pelajar_id');

            $progressKokurikuler = $totalPelajar > 0 ? round(($kokurikulerSelesai / $totalPelajar) * 100) : 0;

            // 3. Progress Kehadiran
            $kehadiranSelesai = Kehadiran::where('tahun_ajaran_semester_id', $semester->id)
                ->whereIn('rombel_id', $rombelIds)
                ->distinct('pelajar_id')
                ->count('pelajar_id');

            $progressKehadiran = $totalPelajar > 0 ? round(($kehadiranSelesai / $totalPelajar) * 100) : 0;

            // 4. Progress Ekstrakurikuler
            $ekskulSelesai = EkskulPelajar::where('tahun_ajaran_semester_id', $semester->id)
                ->whereHas('pelajar', function ($q) use ($rombelIds) {
                    $q->whereHas('rombels', function ($q2) use ($rombelIds) {
                        $q2->whereIn('rombels.id', $rombelIds);
                    });
                })
                ->distinct('pelajar_id')
                ->count('pelajar_id');

            $progressEkskul = $totalPelajar > 0 ? round(($ekskulSelesai / $totalPelajar) * 100) : 0;

            $jenjangData[$tingkat] = [
                'nama' => 'Kelas ' . ($tingkat == 10 ? 'X' : ($tingkat == 11 ? 'XI' : 'XII')),
                'nilai' => $progressNilai,
                'kokurikuler' => $progressKokurikuler,
                'kehadiran' => $progressKehadiran,
                'ekstrakurikuler' => $progressEkskul,
            ];
        }

        return $jenjangData;
    }

    /**
     * Hitung progress per mata pelajaran
     */
    private function calculateProgressPerMapel($semester)
    {
        // Ambil semua mata pelajaran yang diajarkan di semester ini
        $mapels = MataPelajaran::whereHas('rombelPengajars', function ($q) use ($semester) {
            $q->whereHas('rombel', function ($q2) use ($semester) {
                $q2->where('tahun_ajaran_id', $semester->tahun_ajaran_id);
            });
        })
            ->where('status', 'aktif')
            ->get();

        $progressData = [];

        foreach ($mapels as $mapel) {
            // Hitung total siswa yang harus dinilai untuk mapel ini
            $totalSiswa = DB::table('rombel_pelajars')
                ->join('rombel_pengajars', 'rombel_pelajars.rombel_id', '=', 'rombel_pengajars.rombel_id')
                ->where('rombel_pengajars.mata_pelajaran_id', $mapel->id)
                ->whereIn('rombel_pengajars.rombel_id', function ($query) use ($semester) {
                    $query->select('id')
                        ->from('rombels')
                        ->where('tahun_ajaran_id', $semester->tahun_ajaran_id);
                })
                ->distinct('rombel_pelajars.pelajar_id')
                ->count('rombel_pelajars.pelajar_id');

            // Hitung nilai yang sudah diinput
            $nilaiSelesai = Nilai::where('mata_pelajaran_id', $mapel->id)
                ->where('tahun_ajaran_semester_id', $semester->id)
                ->count();

            $progress = $totalSiswa > 0 ? round(($nilaiSelesai / $totalSiswa) * 100) : 0;

            // Tentukan status berdasarkan progress
            $status = 'success';
            if ($progress < 50) {
                $status = 'danger';
            } elseif ($progress < 80) {
                $status = 'warning';
            }

            $progressData[] = [
                'nama' => $mapel->nama,
                'progress' => $progress,
                'status' => $status,
                'selesai' => $nilaiSelesai,
                'total' => $totalSiswa,
            ];
        }

        // Sort by progress ascending (yang paling rendah di atas)
        usort($progressData, function ($a, $b) {
            return $a['progress'] <=> $b['progress'];
        });

        // Ambil 10 teratas (yang progress-nya paling rendah)
        return collect($progressData)->take(10);
    }

    /**
     * Hitung progress per guru
     */
    private function calculateProgressPerGuru($semester)
    {
        // Ambil guru yang mengajar di semester ini
        $gurus = User::where('is_teacher', true)
            ->where('status', 'aktif')
            ->whereHas('rombelPengajars', function ($q) use ($semester) {
                $q->whereHas('rombel', function ($q2) use ($semester) {
                    $q2->where('tahun_ajaran_id', $semester->tahun_ajaran_id);
                });
            })
            ->get();

        $progressData = [];

        foreach ($gurus as $guru) {
            // Hitung total nilai yang harus diinput oleh guru ini
            $totalTarget = DB::table('rombel_pengajars')
                ->join('rombel_pelajars', 'rombel_pengajars.rombel_id', '=', 'rombel_pelajars.rombel_id')
                ->where('rombel_pengajars.guru_id', $guru->id)
                ->whereIn('rombel_pengajars.rombel_id', function ($query) use ($semester) {
                    $query->select('id')
                        ->from('rombels')
                        ->where('tahun_ajaran_id', $semester->tahun_ajaran_id);
                })
                ->count();

            // Hitung nilai yang sudah diinput
            $nilaiSelesai = Nilai::where('guru_id', $guru->id)
                ->where('tahun_ajaran_semester_id', $semester->id)
                ->count();

            $progress = $totalTarget > 0 ? round(($nilaiSelesai / $totalTarget) * 100) : 0;

            // Hitung jumlah rombel yang diajar
            $jumlahRombel = DB::table('rombel_pengajars')
                ->where('guru_id', $guru->id)
                ->whereIn('rombel_id', function ($query) use ($semester) {
                    $query->select('id')
                        ->from('rombels')
                        ->where('tahun_ajaran_id', $semester->tahun_ajaran_id);
                })
                ->distinct('rombel_id')
                ->count('rombel_id');

            // Ambil mata pelajaran yang diajar (ambil yang pertama saja untuk display)
            $mataPelajaran = DB::table('rombel_pengajars')
                ->join('mata_pelajarans', 'rombel_pengajars.mata_pelajaran_id', '=', 'mata_pelajarans.id')
                ->where('rombel_pengajars.guru_id', $guru->id)
                ->whereIn('rombel_pengajars.rombel_id', function ($query) use ($semester) {
                    $query->select('id')
                        ->from('rombels')
                        ->where('tahun_ajaran_id', $semester->tahun_ajaran_id);
                })
                ->select('mata_pelajarans.nama')
                ->first();

            $progressData[] = [
                'nama' => $guru->name,
                'mapel' => $mataPelajaran ? $mataPelajaran->nama : 'N/A',
                'progress' => $progress,
                'jumlah_rombel' => $jumlahRombel,
            ];
        }

        // Sort by progress ascending
        usort($progressData, function ($a, $b) {
            return $a['progress'] <=> $b['progress'];
        });

        // Ambil 5 teratas
        return collect($progressData)->take(5);
    }

    /**
     * Hitung rombel dengan progress terendah
     */
    private function calculateRombelTerendah($semester)
    {
        $rombels = Rombel::where('tahun_ajaran_id', $semester->tahun_ajaran_id)->get();

        $rombelProgress = [];

        foreach ($rombels as $rombel) {
            // Hitung jumlah siswa
            $jumlahSiswa = DB::table('rombel_pelajars')
                ->where('rombel_id', $rombel->id)
                ->count();

            if ($jumlahSiswa == 0) continue;

            // Get wali kelas name
            $waliKelas = 'Belum Ada';
            if ($rombel->wali_kelas_slug) {
                $guru = User::where('slug', $rombel->wali_kelas_slug)->first();
                if ($guru) {
                    $waliKelas = $guru->name;
                }
            }

            // Hitung total target (nilai + kokurikuler + kehadiran + ekskul)
            // Target Nilai: jumlah siswa x jumlah mapel
            $jumlahMapel = DB::table('rombel_pengajars')
                ->where('rombel_id', $rombel->id)
                ->distinct('mata_pelajaran_id')
                ->count('mata_pelajaran_id');

            $targetNilai = $jumlahSiswa * $jumlahMapel;
            $targetLainnya = $jumlahSiswa; // untuk kokurikuler, kehadiran, ekskul

            // Get pelajar IDs di rombel ini
            $pelajarIds = DB::table('rombel_pelajars')
                ->where('rombel_id', $rombel->id)
                ->pluck('pelajar_id');

            // Hitung yang sudah selesai
            $nilaiSelesai = Nilai::where('tahun_ajaran_semester_id', $semester->id)
                ->whereIn('pelajar_id', $pelajarIds)
                ->count();

            $kokurikulerSelesai = Kokurikuler::where('tahun_ajaran_semester_id', $semester->id)
                ->whereIn('pelajar_id', $pelajarIds)
                ->distinct('pelajar_id')
                ->count('pelajar_id');

            $kehadiranSelesai = Kehadiran::where('tahun_ajaran_semester_id', $semester->id)
                ->where('rombel_id', $rombel->id)
                ->count();

            $ekskulSelesai = EkskulPelajar::where('tahun_ajaran_semester_id', $semester->id)
                ->whereIn('pelajar_id', $pelajarIds)
                ->distinct('pelajar_id')
                ->count('pelajar_id');

            // Total progress
            $totalSelesai = $nilaiSelesai + $kokurikulerSelesai + $kehadiranSelesai + $ekskulSelesai;
            $totalTarget = $targetNilai + ($targetLainnya * 3); // 3 = kokur + kehadiran + ekskul

            $progress = $totalTarget > 0 ? round(($totalSelesai / $totalTarget) * 100) : 0;

            $rombelProgress[] = [
                'nama' => $rombel->nama,
                'wali_kelas' => $waliKelas,
                'jumlah_siswa' => $jumlahSiswa,
                'progress' => $progress,
            ];
        }

        // Sort by progress ascending
        usort($rombelProgress, function ($a, $b) {
            return $a['progress'] <=> $b['progress'];
        });

        // Ambil 5 rombel dengan progress terendah
        return collect($rombelProgress)->take(5);
    }

    /**
     * Hitung distribusi nilai rata-rata
     */
    private function calculateDistribusiNilai($semester)
    {
        // Ambil semua nilai di semester ini
        $allNilai = Nilai::where('tahun_ajaran_semester_id', $semester->id)
            ->pluck('nilai_angka');

        $total = $allNilai->count();

        if ($total == 0) {
            return [];
        }

        // Hitung distribusi
        $gradeA = $allNilai->filter(fn($n) => $n >= 90 && $n <= 100)->count();
        $gradeB = $allNilai->filter(fn($n) => $n >= 80 && $n < 90)->count();
        $gradeC = $allNilai->filter(fn($n) => $n >= 70 && $n < 80)->count();
        $gradeD = $allNilai->filter(fn($n) => $n < 70)->count();

        return [
            [
                'range' => '90-100',
                'grade' => 'A',
                'label' => 'Sangat Baik',
                'count' => $gradeA,
                'percentage' => $total > 0 ? round(($gradeA / $total) * 100) : 0,
                'badge_class' => 'success',
            ],
            [
                'range' => '80-89',
                'grade' => 'B',
                'label' => 'Baik',
                'count' => $gradeB,
                'percentage' => $total > 0 ? round(($gradeB / $total) * 100) : 0,
                'badge_class' => 'info',
            ],
            [
                'range' => '70-79',
                'grade' => 'C',
                'label' => 'Cukup',
                'count' => $gradeC,
                'percentage' => $total > 0 ? round(($gradeC / $total) * 100) : 0,
                'badge_class' => 'warning',
            ],
            [
                'range' => '0-69',
                'grade' => 'D',
                'label' => 'Perlu Perbaikan',
                'count' => $gradeD,
                'percentage' => $total > 0 ? round(($gradeD / $total) * 100) : 0,
                'badge_class' => 'danger',
            ],
        ];
    }

    /**
     * Ambil aktivitas terbaru (dummy data, bisa diambil dari activity log table)
     */
    private function getAktivitasTerbaru()
    {
        // TODO: Implement real activity log
        // Untuk sementara return data dummy
        // Nanti bisa diambil dari tabel activity_logs atau audit logs

        $recentNilais = Nilai::with(['guru', 'mataPelajaran', 'pelajar.rombels'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $activities = [];

        foreach ($recentNilais as $nilai) {
            if ($nilai->guru && $nilai->mataPelajaran) {
                $rombel = $nilai->pelajar && $nilai->pelajar->rombels->isNotEmpty()
                    ? $nilai->pelajar->rombels->first()->nama
                    : 'N/A';

                $timeAgo = \Carbon\Carbon::parse($nilai->created_at)->locale('id')->diffForHumans();

                $activities[] = [
                    'user' => $nilai->guru->name,
                    'action' => 'menginput nilai ' . $nilai->mataPelajaran->nama . ' ' . $rombel,
                    'time' => $timeAgo,
                ];
            }
        }

        return $activities;
    }

    /**
     * Default progress per jenjang jika tidak ada data
     */
    private function getDefaultProgressPerJenjang()
    {
        return [
            10 => ['nama' => 'Kelas X', 'nilai' => 0, 'kokurikuler' => 0, 'kehadiran' => 0, 'ekstrakurikuler' => 0],
            11 => ['nama' => 'Kelas XI', 'nilai' => 0, 'kokurikuler' => 0, 'kehadiran' => 0, 'ekstrakurikuler' => 0],
            12 => ['nama' => 'Kelas XII', 'nilai' => 0, 'kokurikuler' => 0, 'kehadiran' => 0, 'ekstrakurikuler' => 0],
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
