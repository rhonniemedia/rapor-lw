<?php

namespace App\Livewire\Wali;

use App\Models\Nilai;
use App\Models\Rombel;
use Livewire\Component;
use App\Models\Kehadiran;
use App\Models\Pengaturan;
use App\Models\DataSekolah;
use App\Models\Kokurikuler;
use App\Models\RombelPelajar;
use App\Models\TahunAjaran;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PreviewLeger extends Component
{
    // ========================================
    // PROPERTIES
    // ========================================

    // Filter Properties
    public $tahunAjaranId = null;
    public $semesterId = null;

    // Data Collections Filter
    public $tahunAjaranList = [];
    public $semesterList = [];

    // Main Data
    public $rombel;
    public $dataSekolah = null;
    public $pengaturan = null;

    // Data Collections
    public $studentsList = [];
    public $mataPelajaranList = [];
    public $pdfUrl = '';

    protected $queryString = [
        'tahunAjaranId' => ['except' => null],
        'semesterId'    => ['except' => null],
    ];

    // ========================================
    // MOUNT
    // ========================================

    public function mount()
    {
        $this->loadRombelWaliKelas();

        if (!$this->rombel) {
            session()->flash('error', 'Anda tidak memiliki kelas binaan.');
            return redirect()->route('walikelas.dashboard');
        }

        $this->initializeFilters();
    }

    // ========================================
    // INITIALIZATION & FILTER METHODS
    // ========================================

    private function initializeFilters(): void
    {
        $this->loadTahunAjaran();

        if (!$this->tahunAjaranId) {
            $this->setActiveTahunAjaran();
        }

        if ($this->tahunAjaranId) {
            $this->loadSemester();

            if (!$this->semesterId) {
                $this->setActiveSemester();
            }
        }

        if ($this->rombel && $this->semesterId) {
            $this->loadDataSekolah();
            $this->loadPengaturan();
            $this->loadMataPelajaranList();
            $this->loadStudentsWithNilai();
            $this->generatePdfUrl();
        }
    }

    private function setActiveTahunAjaran(): void
    {
        $activeTahunAjaran = TahunAjaran::where('status', 'aktif')->first();
        if ($activeTahunAjaran) {
            $this->tahunAjaranId = $activeTahunAjaran->id;
        }
    }

    private function setActiveSemester(): void
    {
        $activeSemester = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahunAjaranId)
            ->where('status', 'aktif')
            ->first();
        if ($activeSemester) {
            $this->semesterId = $activeSemester->id;
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

    // ========================================
    // FILTER UPDATE HANDLERS
    // ========================================

    public function updatedTahunAjaranId(): void
    {
        $this->semesterId = null;
        $this->semesterList = [];
        $this->loadSemester();
        $this->setActiveSemester();

        if ($this->semesterId) {
            $this->updatedSemesterId();
        } else {
            $this->resetLegerData();
        }
    }

    public function updatedSemesterId(): void
    {
        if ($this->semesterId) {
            $this->loadDataSekolah();
            $this->loadPengaturan();
            $this->loadMataPelajaranList();
            $this->loadStudentsWithNilai();
            $this->generatePdfUrl();
        } else {
            $this->resetLegerData();
        }
    }

    private function resetLegerData(): void
    {
        $this->studentsList = [];
        $this->mataPelajaranList = [];
        $this->pengaturan = null;
        $this->pdfUrl = '';
    }

    // ========================================
    // LOAD DATA UTILITIES
    // ========================================

    private function loadDataSekolah(): void
    {
        $this->dataSekolah = DataSekolah::first();
    }

    private function loadPengaturan(): void
    {
        if (!$this->semesterId) return;

        $this->pengaturan = Pengaturan::with('kepalaSekolah')
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->first();
    }

    // ========================================
    // LOAD MATA PELAJARAN LIST
    // ========================================

    private function loadMataPelajaranList(): void
    {
        $kurikulumId = $this->rombel->tahunAjaranKurikulum->kurikulum_id ?? null;
        $tingkatRombel = $this->rombel->tingkat ?? null;

        if (!$this->rombel || !$kurikulumId || !$tingkatRombel) {
            $this->mataPelajaranList = [];
            return;
        }

        $allMapel = \App\Models\RombelPengajar::query()
            ->where('rombel_pengajars.rombel_id', $this->rombel->id)
            ->join('mata_pelajarans as mp', 'rombel_pengajars.mata_pelajaran_id', '=', 'mp.id')
            ->join('kurikulum_mata_pelajarans as kmp', function ($join) use ($kurikulumId, $tingkatRombel) {
                $join->on('mp.id', '=', 'kmp.mata_pelajaran_id')
                    ->where('kmp.kurikulum_id', '=', $kurikulumId)
                    ->where('kmp.tingkat', '=', $tingkatRombel);
            })
            ->join('mata_pelajaran_kelompoks as mpk', 'kmp.kelompok_id', '=', 'mpk.id')
            ->select(
                'mp.id',
                'mp.nama',
                'mp.kode',
                'mp.is_mapel_agama',
                'mpk.kode as kelompok_kode',
                'mpk.nama as kelompok_nama',
                'kmp.urutan'
            )
            ->orderBy('kmp.urutan', 'asc')
            ->get();

        $agamaMapels = $allMapel->filter(fn($item) => $item->is_mapel_agama == true || $item->is_mapel_agama == 1);
        $nonAgamaMapels = $allMapel->filter(fn($item) => !$item->is_mapel_agama || $item->is_mapel_agama == 0);

        $combined = collect();

        if ($agamaMapels->isNotEmpty()) {
            $combined->push($agamaMapels->first());
        }

        $combined = $combined->merge($nonAgamaMapels);

        $mataPelajarans = $combined
            ->sortBy(fn($item) => [$item->kelompok_kode, $item->urutan])
            ->map(function ($item) {
                $isAgama = $item->is_mapel_agama == true || $item->is_mapel_agama == 1;
                return [
                    'id'             => $isAgama ? 'agama' : $item->id,
                    'nama'           => $isAgama ? 'Pendidikan Agama dan Budi Pekerti' : $item->nama,
                    'kode'           => $isAgama ? 'PABP' : $item->kode,
                    'kelompok_kode'  => $item->kelompok_kode,
                    'kelompok_nama'  => $item->kelompok_nama,
                    'urutan'         => $item->urutan,
                    'is_agama'       => $isAgama,
                ];
            });

        $this->mataPelajaranList = $mataPelajarans->values()->toArray();
    }

    // ========================================
    // LOAD STUDENTS WITH NILAI
    // ========================================

    private function loadStudentsWithNilai(): void
    {
        if (!$this->rombel || !$this->semesterId || empty($this->mataPelajaranList)) {
            $this->studentsList = [];
            return;
        }

        $kurikulumId = $this->rombel->tahunAjaranKurikulum->kurikulum_id ?? null;
        if (!$kurikulumId) {
            $this->studentsList = [];
            return;
        }

        $rombelPelajars = RombelPelajar::with('pelajar')
            ->where('rombel_id', $this->rombel->id)
            ->get();

        $totalMapelWajib = count($this->mataPelajaranList);
        $mapelOrderIds = array_column($this->mataPelajaranList, 'id');

        $studentsData = $rombelPelajars->map(function ($rombelPelajar, $index) use ($totalMapelWajib) {
            $pelajar = $rombelPelajar->pelajar;
            $nilaiPerMapel = [];
            $totalNilai = 0;

            foreach ($this->mataPelajaranList as $mapel) {
                if (isset($mapel['is_agama']) && $mapel['is_agama'] === true) {
                    $nilai = Nilai::where('pelajar_id', $pelajar->id)
                        ->where('tahun_ajaran_semester_id', $this->semesterId)
                        ->whereHas('mataPelajaran', fn($q) => $q->where('is_mapel_agama', true))
                        ->latest('updated_at')
                        ->first();
                } else {
                    $nilai = Nilai::where('pelajar_id', $pelajar->id)
                        ->where('mata_pelajaran_id', $mapel['id'])
                        ->where('tahun_ajaran_semester_id', $this->semesterId)
                        ->first();
                }

                $nilaiAngka = $nilai ? round($nilai->nilai_angka ?? 0) : 0;

                $nilaiPerMapel[$mapel['id']] = $nilaiAngka;
                $totalNilai += $nilaiAngka;
            }

            $rataRata = $totalMapelWajib > 0 ? round($totalNilai / $totalMapelWajib, 1) : 0;

            $kokurikuler = Kokurikuler::where('pelajar_id', $pelajar->id)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->first();

            $kehadiran = Kehadiran::where('pelajar_id', $pelajar->id)
                ->where('rombel_id', $this->rombel->id)
                ->where('tahun_ajaran_semester_id', $this->semesterId)
                ->first();

            return [
                'id'                => $pelajar->id,
                'nis'               => $pelajar->nomor_induk ?? '-',
                'nisn'              => $pelajar->nisn ?? '-',
                'nama'              => $pelajar->nama_lengkap ?? 'N/A',
                'jenis_kelamin'     => $pelajar->jenis_kelamin ?? 'L',
                'nilai_per_mapel'   => $nilaiPerMapel,
                'kokurikuler'       => $kokurikuler->predikat ?? '-',
                'jumlah_nilai'      => $totalNilai,
                'rata_rata'         => $rataRata,
                'peringkat'         => 0,
                'sakit'             => $kehadiran->jumlah_sakit ?? 0,
                'izin'              => $kehadiran->jumlah_izin ?? 0,
                'tanpa_keterangan'  => $kehadiran->jumlah_tanpa_keterangan ?? 0,
            ];
        })->toArray();

        usort($studentsData, function ($a, $b) use ($mapelOrderIds) {
            if ($b['jumlah_nilai'] !== $a['jumlah_nilai']) {
                return $b['jumlah_nilai'] <=> $a['jumlah_nilai'];
            }

            foreach ($mapelOrderIds as $mapelId) {
                $nilaiA = $a['nilai_per_mapel'][$mapelId] ?? 0;
                $nilaiB = $b['nilai_per_mapel'][$mapelId] ?? 0;

                if ($nilaiB !== $nilaiA) {
                    return $nilaiB <=> $nilaiA;
                }
            }

            return strcmp($a['nama'], $b['nama']);
        });

        $rankCounter = 1;
        foreach ($studentsData as &$student) {
            if ($student['jumlah_nilai'] === 0) {
                $student['peringkat'] = '-';
            } else {
                $student['peringkat'] = $rankCounter++;
            }
        }
        unset($student);

        usort($studentsData, fn($a, $b) => strcmp($a['nama'], $b['nama']));

        foreach ($studentsData as $key => &$val) {
            $val['no'] = $key + 1;
        }

        $this->studentsList = $studentsData;
    }

    // ========================================
    // PDF GENERATION
    // ========================================

    public function generatePdfUrl()
    {
        if (!$this->rombel || empty($this->studentsList) || !$this->semesterId) {
            $this->pdfUrl = '';
            return;
        }

        $semesterObj = TahunAjaranSemester::with(['semester', 'tahunAjaran'])->find($this->semesterId);

        $pdfData = [
            'sekolah' => [
                'nama_sekolah' => $this->dataSekolah->nama_sekolah ?? 'N/A',
                'kota_kabupaten' => $this->dataSekolah->kota_kabupaten ?? 'Kota',
            ],
            'tahun_ajaran' => $semesterObj->tahunAjaran->nama ?? 'N/A',
            'semester_nama' => $semesterObj->semester->nama ?? 'N/A',
            'kelas' => $this->rombel->nama ?? 'N/A',
            'wali_kelas' => [
                'nama' => $this->rombel->waliKelas->name ?? 'N/A',
                'nip' => $this->rombel->waliKelas->nip ?? '-'
            ],
            'kepala_sekolah' => [
                'nama' => $this->pengaturan->kepalaSekolah->name ?? 'N/A',
                'nip' => $this->pengaturan->kepalaSekolah->nip ?? '-'
            ],
            'tanggal_rapor' => $this->pengaturan?->tanggal_rapor ?? date('Y-m-d'),
            'mata_pelajaran' => $this->mataPelajaranList,
            'students' => $this->studentsList,
        ];

        $userId = Auth::id() ?? 'guest';
        $cacheKey = "leger_print_{$userId}_{$this->rombel->id}";

        Cache::put($cacheKey, $pdfData, 3600);

        // Tambahkan query timestamp untuk bypass cache dari browser
        $timestamp = now()->timestamp;
        $this->pdfUrl = route('pdf.leger', ['key' => $cacheKey, 't' => $timestamp]);
    }

    // ========================================
    // RENDER METHOD
    // ========================================

    public function render()
    {
        $selectedSemesterObj = $this->semesterId
            ? collect($this->semesterList)->firstWhere('id', $this->semesterId)
            : null;

        if (!$selectedSemesterObj && $this->semesterId) {
            $selectedSemesterObj = TahunAjaranSemester::with(['semester', 'tahunAjaran'])->find($this->semesterId);
        }

        return view('livewire.wali.preview-leger', [
            'totalStudents'       => count($this->studentsList),
            'totalMataPelajaran'  => count($this->mataPelajaranList),
            'hasData'             => !empty($this->studentsList),
            'selectedSemesterObj' => $selectedSemesterObj,
        ]);
    }
}
