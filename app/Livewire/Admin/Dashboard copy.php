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
use App\Models\TahunAjaranSemester;

class Dashboard extends Component
{
    public $totalPelajar;
    public $totalGuru;
    public $totalRombel;
    public $deadline;
    public $progressPerJenjang;
    public $progressPerMapel;
    public $progressPerGuru;
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
            $this->deadline = null;
            $this->progressPerJenjang = $this->getDefaultProgressPerJenjang();
            $this->progressPerMapel = collect();
            $this->progressPerGuru = collect();
            $this->jadwalPenting = [];
            return;
        }

        // Total Statistik
        $this->totalPelajar = Pelajar::count();
        $this->totalGuru = User::where('is_teacher', true)
            ->where('status', 'aktif')
            ->count();
        $this->totalRombel = Rombel::where('tahun_ajaran_id', $semesterAktif->tahun_ajaran_id)->count();

        // Deadline dari pengaturan
        $pengaturan = Pengaturan::where('tahun_ajaran_semester_id', $semesterAktif->id)->first();
        $this->deadline = $pengaturan ? $pengaturan->tanggal_rapor : null;

        // Progress per Jenjang (X, XI, XII)
        $this->progressPerJenjang = $this->calculateProgressPerJenjang($semesterAktif);

        // Progress per Mata Pelajaran
        $this->progressPerMapel = $this->calculateProgressPerMapel($semesterAktif);

        // Progress per Guru
        $this->progressPerGuru = $this->calculateProgressPerGuru($semesterAktif);

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
            $totalPelajar = RombelPelajar::whereIn('rombel_id', $rombelIds)
                ->distinct()
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
            $totalMapelPerSiswa = RombelPengajar::whereIn('rombel_id', $rombelIds)
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
            $totalSiswa = RombelPelajar::join('rombel_pengajars', 'rombel_pelajars.rombel_id', '=', 'rombel_pengajars.rombel_id')
                ->where('rombel_pengajars.mata_pelajaran_id', $mapel->id)
                ->whereIn('rombel_pengajars.rombel_id', function ($query) use ($semester) {
                    $query->select('id')
                        ->from('rombels')
                        ->where('tahun_ajaran_id', $semester->tahun_ajaran_id);
                })
                ->select('rombel_pelajars.pelajar_id')
                ->distinct()
                ->count();

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
            $totalTarget = RombelPengajar::where('guru_id', $guru->id)
                ->whereHas('rombel', function ($q) use ($semester) {
                    $q->where('tahun_ajaran_id', $semester->tahun_ajaran_id);
                })
                ->with(['rombel.rombelPelajars'])
                ->get()
                ->sum(function ($pengajar) {
                    return $pengajar->rombel->rombelPelajars->count();
                });


            // Hitung nilai yang sudah diinput
            $nilaiSelesai = Nilai::where('guru_id', $guru->id)
                ->where('tahun_ajaran_semester_id', $semester->id)
                ->count();

            $progress = $totalTarget > 0 ? round(($nilaiSelesai / $totalTarget) * 100) : 0;

            // Hitung jumlah rombel yang diajar
            $jumlahRombel = $guru->rombelPengajars()
                ->whereHas('rombel', function ($q) use ($semester) {
                    $q->where('tahun_ajaran_id', $semester->tahun_ajaran_id);
                })
                ->distinct('rombel_id')
                ->count('rombel_id');

            // Ambil mata pelajaran yang diajar (ambil yang pertama saja untuk display)
            $mataPelajaran = $guru->rombelPengajars()
                ->whereHas('rombel', function ($q) use ($semester) {
                    $q->where('tahun_ajaran_id', $semester->tahun_ajaran_id);
                })
                ->with('mataPelajaran')
                ->first()
                ?->mataPelajaran;

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
