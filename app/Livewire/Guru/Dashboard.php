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

    // Statistik Utama
    public $jumlahKelas = 0;
    public $totalSiswa = 0;
    public $totalMapel = 0;
    public $progressInputNilai = 0;
    public $rataRataKelas = 0;

    // Kelas yang Diampu
    public $kelasYangDiampu = [];

    // Distribusi Nilai
    public $distribusiNilai = [
        'A' => ['persentase' => 0, 'jumlah' => 0],
        'B' => ['persentase' => 0, 'jumlah' => 0],
        'C' => ['persentase' => 0, 'jumlah' => 0],
        'D' => ['persentase' => 0, 'jumlah' => 0],
    ];

    // Progress per Kelas
    public $progressPerKelas = [];

    // Siswa Perlu Perhatian
    public $siswaPerluPerhatian = [];

    // Aktivitas Terbaru
    public $aktivitasTerbaru = [];

    // Jadwal Besok
    public $jadwalBesok = [];

    public function mount()
    {
        $this->guru = Auth::guard('web')->user();
        $this->loadTahunAjaranSemester();

        if ($this->guru && $this->tahunAjaranSemester) {
            $this->loadStatistikUtama();
            $this->loadKelasYangDiampu();
            $this->loadDistribusiNilai();
            $this->loadProgressPerKelas();
            $this->loadSiswaPerluPerhatian();
            $this->loadAktivitasTerbaru();
            $this->loadJadwalBesok();
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
     * Load statistik utama (4 card)
     */
    protected function loadStatistikUtama()
    {
        $guruId = $this->guru->id;
        $semesterId = $this->tahunAjaranSemester->id;

        // 1. Jumlah Kelas yang Diampu
        $this->jumlahKelas = RombelPengajar::where('guru_id', $guruId)->distinct('rombel_id')->count('rombel_id');

        // 2. Total Siswa dari semua kelas yang diampu
        $rombelIds = RombelPengajar::where('guru_id', $guruId)->pluck('rombel_id');

        $this->totalSiswa = RombelPelajar::whereIn('rombel_id', $rombelIds)
            ->distinct('pelajar_id')
            ->count('pelajar_id');

        // 3. Total Mata Pelajaran yang diajar
        $this->totalMapel = RombelPengajar::where('guru_id', $guruId)
            ->distinct('mata_pelajaran_id')
            ->count('mata_pelajaran_id');

        // 4. Progress Input Nilai
        $this->calculateProgressInputNilai();

        // 5. Rata-rata Kelas
        $nilaiStats = Nilai::whereHas('rombelPengajar', function ($query) use ($guruId) {
            $query->where('guru_id', $guruId);
        })
            ->where('tahun_ajaran_semester_id', $semesterId)
            ->whereNotNull('nilai_angka')
            ->avg('nilai_angka');

        $this->rataRataKelas = round($nilaiStats ?? 0, 1);
    }

    /**
     * Hitung progress input nilai
     */
    protected function calculateProgressInputNilai()
    {
        $guruId = $this->guru->slug;
        $semesterId = $this->tahunAjaranSemester->id;

        // Total yang harus diinput = jumlah siswa * jumlah rombel pengajar
        $rombelPengajarIds = RombelPengajar::where('guru_id', $guruId)->pluck('id');

        $totalYangHarusDiinput = 0;
        foreach ($rombelPengajarIds as $rombelPengajarId) {
            $rombelPengajar = RombelPengajar::find($rombelPengajarId);
            if ($rombelPengajar) {
                $jumlahSiswa = RombelPelajar::where('rombel_id', $rombelPengajar->rombel_id)->count();
                $totalYangHarusDiinput += $jumlahSiswa;
            }
        }

        if ($totalYangHarusDiinput == 0) {
            $this->progressInputNilai = 0;
            return;
        }

        // Total yang sudah diinput
        $totalSudahDiinput = Nilai::whereIn('rombel_pengajar_id', $rombelPengajarIds)
            ->where('tahun_ajaran_semester_id', $semesterId)
            ->whereNotNull('nilai_angka')
            ->count();

        $this->progressInputNilai = round(($totalSudahDiinput / $totalYangHarusDiinput) * 100, 0);
    }

    /**
     * Load kelas yang diampu
     */
    protected function loadKelasYangDiampu()
    {
        $guruId = $this->guru->slug;

        $rombelPengajars = RombelPengajar::where('guru_id', $guruId)
            ->with(['rombel.jurusan', 'mataPelajaran'])
            ->get();

        $kelasData = [];
        foreach ($rombelPengajars as $rombelPengajar) {
            $rombel = $rombelPengajar->rombel;
            $mapel = $rombelPengajar->mataPelajaran;

            $kelasData[] = [
                'id' => $rombel->id,
                'nama' => $rombel->nama,
                'mata_pelajaran' => $mapel->nama ?? '-',
                'jumlah_siswa' => RombelPelajar::where('rombel_id', $rombel->id)->count()
            ];
        }

        $this->kelasYangDiampu = $kelasData;
    }

    /**
     * Load distribusi nilai
     */
    protected function loadDistribusiNilai()
    {
        $guruId = $this->guru->slug;
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
        $guruId = $this->guru->slug;
        $semesterId = $this->tahunAjaranSemester->id;

        $rombelPengajars = RombelPengajar::where('guru_id', $guruId)
            ->with('rombel')
            ->get();

        $progressData = [];

        foreach ($rombelPengajars as $rombelPengajar) {
            $rombelId = $rombelPengajar->rombel_id;
            $rombel = $rombelPengajar->rombel;

            // Jumlah siswa di kelas
            $totalSiswa = RombelPelajar::where('rombel_id', $rombelId)->count();

            if ($totalSiswa == 0) continue;

            // Jumlah nilai yang sudah diinput
            $nilaiSelesai = Nilai::where('rombel_pengajar_id', $rombelPengajar->id)
                ->where('tahun_ajaran_semester_id', $semesterId)
                ->whereNotNull('nilai_angka')
                ->count();

            $progressPersen = round(($nilaiSelesai / $totalSiswa) * 100, 0);

            // Rata-rata nilai kelas
            $rataRata = Nilai::where('rombel_pengajar_id', $rombelPengajar->id)
                ->where('tahun_ajaran_semester_id', $semesterId)
                ->whereNotNull('nilai_angka')
                ->avg('nilai_angka') ?? 0;

            $progressData[] = [
                'nama' => $rombel->nama . ' - ' . ($rombelPengajar->mataPelajaran->nama ?? 'Mapel'),
                'progress_persen' => $progressPersen,
                'nilai_selesai' => $nilaiSelesai,
                'total_siswa' => $totalSiswa,
                'rata_rata' => round($rataRata, 1),
                'warna' => $progressPersen >= 80 ? 'success' : ($progressPersen >= 50 ? 'warning' : 'danger')
            ];
        }

        $this->progressPerKelas = $progressData;
    }

    /**
     * Load siswa yang perlu perhatian
     */
    protected function loadSiswaPerluPerhatian()
    {
        $guruId = $this->guru->slug;
        $semesterId = $this->tahunAjaranSemester->id;

        // Ambil siswa dengan nilai rendah
        $nilaiRendah = Nilai::whereHas('rombelPengajar', function ($query) use ($guruId) {
            $query->where('guru_id', $guruId);
        })
            ->where('tahun_ajaran_semester_id', $semesterId)
            ->where('nilai_angka', '<', 70)
            ->with(['pelajar', 'rombelPengajar.rombel', 'rombelPengajar.mataPelajaran'])
            ->orderBy('nilai_angka', 'asc')
            ->limit(5)
            ->get();

        $siswaData = [];

        foreach ($nilaiRendah as $nilai) {
            $pelajar = $nilai->pelajar;
            $rombel = $nilai->rombelPengajar->rombel ?? null;
            $mapel = $nilai->rombelPengajar->mataPelajaran ?? null;

            if (!$pelajar || !$rombel) continue;

            $siswaData[] = [
                'siswa_id' => $pelajar->id,
                'siswa_nama' => $pelajar->nama_lengkap,
                'siswa_nis' => $pelajar->nis ?? '-',
                'siswa_foto' => $pelajar->foto ?? null,
                'kelas_nama' => $rombel->nama,
                'mata_pelajaran' => $mapel->nama ?? '-',
                'nilai_akhir' => round($nilai->nilai_angka, 1),
                'kategori' => $nilai->nilai_angka < 60 ? 'Perlu Bimbingan' : 'Perlu Perbaikan'
            ];
        }

        $this->siswaPerluPerhatian = $siswaData;
    }

    /**
     * Load aktivitas terbaru
     */
    protected function loadAktivitasTerbaru()
    {
        $guruId = $this->guru->slug;
        $semesterId = $this->tahunAjaranSemester->id;

        $nilaiTerbaru = Nilai::whereHas('rombelPengajar', function ($query) use ($guruId) {
            $query->where('guru_id', $guruId);
        })
            ->where('tahun_ajaran_semester_id', $semesterId)
            ->with(['rombelPengajar.rombel', 'rombelPengajar.mataPelajaran'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        $aktivitas = [];

        foreach ($nilaiTerbaru as $nilai) {
            $rombel = $nilai->rombelPengajar->rombel ?? null;
            $mapel = $nilai->rombelPengajar->mataPelajaran ?? null;

            if ($rombel && $mapel) {
                $aktivitas[] = [
                    'deskripsi' => "Menginput nilai " . $mapel->nama . " kelas " . $rombel->nama,
                    'waktu' => $this->formatTimeAgo($nilai->updated_at)
                ];
            }
        }

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
     * Load jadwal mengajar besok
     */
    protected function loadJadwalBesok()
    {
        // Implementasi sesuai dengan model Jadwal Anda
        // Untuk sementara return array kosong
        $this->jadwalBesok = [];

        /* Contoh implementasi jika ada model Jadwal:
        $guruId = $this->guru->slug;
        $besok = now()->addDay()->toDateString();
        
        $jadwal = Jadwal::where('guru_id', $guruId)
            ->whereDate('tanggal', $besok)
            ->with(['rombel', 'mataPelajaran'])
            ->orderBy('jam_mulai')
            ->get();
        
        $jadwalData = [];
        foreach ($jadwal as $j) {
            $jadwalData[] = [
                'kelas_nama' => $j->rombel->nama ?? '-',
                'jam_mulai' => $j->jam_mulai,
                'jam_selesai' => $j->jam_selesai,
                'materi' => $j->materi ?? $j->mataPelajaran->nama ?? '-',
                'warna' => ['primary', 'success', 'info', 'warning'][rand(0, 3)]
            ];
        }
        
        $this->jadwalBesok = $jadwalData;
        */
    }

    /**
     * Refresh data
     */
    public function refreshData()
    {
        $this->loadStatistikUtama();
        $this->loadDistribusiNilai();
        $this->loadProgressPerKelas();
        $this->loadSiswaPerluPerhatian();
        $this->loadAktivitasTerbaru();

        $this->dispatch('data-refreshed');
    }

    /**
     * Open modal catatan
     */
    public function openCatatanModal($siswaId)
    {
        $this->dispatch('open-catatan-modal', siswaId: $siswaId);
    }

    public function render()
    {
        return view('livewire.guru.dashboard');
    }
}
