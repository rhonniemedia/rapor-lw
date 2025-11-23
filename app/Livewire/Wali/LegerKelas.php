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
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Auth;
use App\Models\KurikulumMataPelajaran;

class LegerKelas extends Component
{
    // ========================================
    // PROPERTIES
    // ========================================

    public $rombel;
    public $semesterAktif;
    public $dataSekolah = null;
    public $pengaturan = null;

    // Data Collections
    public $studentsList = [];
    public $mataPelajaranList = [];
    public $pdfUrl;

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

        $this->loadSemesterAktif();

        if (!$this->semesterAktif) {
            session()->flash('warning', 'Tidak ada semester aktif saat ini.');
            return;
        }

        $this->loadDataSekolah();
        $this->loadPengaturan();
        $this->loadMataPelajaranList();
        $this->loadStudentsWithNilai();
        $this->generatePdfUrl();
    }

    // ========================================
    // INITIALIZATION METHODS
    // ========================================

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

    private function loadDataSekolah(): void
    {
        $this->dataSekolah = DataSekolah::first();
    }

    private function loadPengaturan(): void
    {
        $this->pengaturan = Pengaturan::with('kepalaSekolah')
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->first();
    }

    // ========================================
    // LOAD MATA PELAJARAN LIST
    // ========================================

    private function loadMataPelajaranList(): void
    {
        if (!$this->rombel || !$this->semesterAktif) {
            $this->mataPelajaranList = [];
            return;
        }

        $kurikulumId = $this->rombel->tahunAjaranKurikulum->kurikulum_id ?? null;

        if (!$kurikulumId) {
            $this->mataPelajaranList = [];
            return;
        }

        // Ambil semua mata pelajaran
        $allMapel = KurikulumMataPelajaran::with(['mataPelajaran', 'kelompok'])
            ->where('kurikulum_id', $kurikulumId)
            ->get();

        // Pisahkan agama dan non-agama
        $agamaMapels = $allMapel->filter(function ($item) {
            return $item->mataPelajaran->is_mapel_agama == true || $item->mataPelajaran->is_mapel_agama == 1;
        });

        $nonAgamaMapels = $allMapel->filter(function ($item) {
            return !$item->mataPelajaran->is_mapel_agama || $item->mataPelajaran->is_mapel_agama == 0;
        });

        // Ambil hanya 1 agama sebagai representasi
        $combined = collect();
        if ($agamaMapels->isNotEmpty()) {
            $combined->push($agamaMapels->first());
        }
        $combined = $combined->merge($nonAgamaMapels);

        // Sort dan mapping
        $mataPelajarans = $combined
            ->sortBy(function ($item) {
                return [$item->kelompok->kode, $item->urutan];
            })
            ->map(function ($item) {
                $isAgama = $item->mataPelajaran->is_mapel_agama == true || $item->mataPelajaran->is_mapel_agama == 1;

                return (object) [
                    'id'             => $isAgama ? 'agama' : $item->mataPelajaran->id,
                    'nama'           => $isAgama ? 'Pendidikan Agama dan Budi Pekerti' : $item->mataPelajaran->nama,
                    'kode'           => $isAgama ? 'PABP' : $item->mataPelajaran->kode,
                    'kelompok_nama'  => $item->kelompok->nama,
                    'kelompok_kode'  => $item->kelompok->kode,
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
        if (!$this->rombel || !$this->semesterAktif || empty($this->mataPelajaranList)) {
            $this->studentsList = [];
            return;
        }

        // Get kurikulum_id
        $kurikulumId = $this->rombel->tahunAjaranKurikulum->kurikulum_id ?? null;

        if (!$kurikulumId) {
            $this->studentsList = [];
            return;
        }

        // Load students dari rombel
        $rombelPelajars = RombelPelajar::with('pelajar')
            ->where('rombel_id', $this->rombel->id)
            ->orderBy('id', 'asc')
            ->get();

        $studentsData = $rombelPelajars->map(function ($rombelPelajar, $index) use ($kurikulumId) {
            $pelajar = $rombelPelajar->pelajar;

            // Load nilai untuk semua mata pelajaran
            $nilaiPerMapel = [];
            $totalNilai = 0;
            $jumlahMapelDiisi = 0;

            foreach ($this->mataPelajaranList as $mapel) {
                if (isset($mapel->is_agama) && $mapel->is_agama === true) {
                    // Ambil nilai agama sesuai agama siswa
                    $nilai = Nilai::where('pelajar_id', $pelajar->id)
                        ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                        ->whereHas('mataPelajaran', function ($q) {
                            $q->where('is_mapel_agama', true); // ← UBAH INI
                        })
                        ->first();
                } else {
                    // Nilai mata pelajaran non-agama
                    $nilai = Nilai::where('pelajar_id', $pelajar->id)
                        ->where('mata_pelajaran_id', $mapel->id)
                        ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                        ->first();
                }

                $nilaiAngka = $nilai ? round($nilai->nilai_angka ?? 0) : 0;
                $nilaiPerMapel[$mapel->id] = $nilaiAngka;

                if ($nilaiAngka > 0) {
                    $totalNilai += $nilaiAngka;
                    $jumlahMapelDiisi++;
                }
            }

            // Hitung rata-rata
            $rataRata = $jumlahMapelDiisi > 0 ? round($totalNilai / $jumlahMapelDiisi, 1) : 0;

            // Load kokurikuler
            $kokurikuler = Kokurikuler::where('pelajar_id', $pelajar->id)
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                ->first();

            // Load kehadiran
            $kehadiran = Kehadiran::where('pelajar_id', $pelajar->id)
                ->where('rombel_id', $this->rombel->id)
                ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
                ->first();

            return [
                'no' => $index + 1,
                'id' => $pelajar->id,
                'nis' => $pelajar->nomor_induk ?? '-',
                'nisn' => $pelajar->nisn ?? '-',
                'nama' => $pelajar->nama_lengkap ?? 'N/A',
                'jenis_kelamin' => $pelajar->jenis_kelamin ?? '-',
                'nilai_per_mapel' => $nilaiPerMapel,
                'kokurikuler' => $kokurikuler->predikat ?? '-',
                'jumlah_nilai' => $totalNilai,
                'rata_rata' => $rataRata,
                'peringkat' => 0, // Akan dihitung setelah semua data terkumpul
                'sakit' => $kehadiran->jumlah_sakit ?? 0,
                'izin' => $kehadiran->jumlah_izin ?? 0,
                'tanpa_keterangan' => $kehadiran->jumlah_tanpa_keterangan ?? 0,
            ];
        })->toArray();

        // Hitung peringkat berdasarkan jumlah nilai (descending)
        // Sort berdasarkan jumlah nilai tertinggi
        usort($studentsData, function ($a, $b) {
            return $b['jumlah_nilai'] <=> $a['jumlah_nilai'];
        });

        // Assign peringkat
        $currentRank = 1;
        $previousNilai = null;
        $sameRankCount = 0;

        foreach ($studentsData as $key => &$student) {
            if ($student['jumlah_nilai'] === 0) {
                $student['peringkat'] = '-';
            } else {
                if ($previousNilai !== null && $student['jumlah_nilai'] === $previousNilai) {
                    // Nilai sama dengan sebelumnya, pakai ranking yang sama
                    $sameRankCount++;
                } else {
                    // Nilai berbeda, update ranking
                    $currentRank += $sameRankCount;
                    $sameRankCount = 1;
                }
                $student['peringkat'] = $currentRank;
                $previousNilai = $student['jumlah_nilai'];
            }
        }

        // Kembalikan urutan berdasarkan nomor urut awal (berdasarkan id)
        usort($studentsData, function ($a, $b) {
            return $a['no'] <=> $b['no'];
        });

        $this->studentsList = $studentsData;
    }

    // ========================================
    // PDF GENERATION
    // ========================================

    public function generatePdfUrl()
    {
        if (!$this->rombel || !$this->semesterAktif) {
            $this->pdfUrl = '';
            return;
        }

        // Prepare data untuk PDF
        $pdfData = [
            'sekolah' => [
                'nama_sekolah' => $this->dataSekolah->nama_sekolah ?? 'N/A',
                'npsn' => $this->dataSekolah->npsn ?? 'N/A',
                'alamat' => $this->dataSekolah->alamat ?? 'N/A',
                'kota_kabupaten' => $this->dataSekolah->kota_kabupaten ?? 'N/A',
                'logo_sekolah_path' => $this->dataSekolah->logo_sekolah_path ?? null,
            ],
            'tahun_ajaran' => $this->semesterAktif->tahunAjaran->nama ?? 'N/A',
            'semester_nama' => $this->semesterAktif->semester->nama ?? 'N/A',
            'kelas' => $this->rombel->nama ?? 'N/A',
            'kkm' => $this->pengaturan->kkm ?? 75,
            'wali_kelas' => [
                'nama' => $this->rombel->waliKelas->name ?? 'N/A',
                'nip' => $this->rombel->waliKelas->nip ?? '~'
            ],
            'kepala_sekolah' => [
                'nama' => $this->pengaturan->kepalaSekolah->name ?? 'N/A',
                'nip' => $this->pengaturan->kepalaSekolah->nip ?? 'N/A'
            ],
            'tanggal_rapor' => $this->pengaturan?->tanggal_rapor
                ? \Carbon\Carbon::parse($this->pengaturan->tanggal_rapor)->format('Y-m-d')
                : now()->format('Y-m-d'),
            'mata_pelajaran' => $this->mataPelajaranList,
            'students' => $this->studentsList,
        ];

        // Encode data
        $encodedData = base64_encode(json_encode($pdfData));

        // Generate URL
        $this->pdfUrl = route('pdf.generate.leger') . '?data=' . $encodedData;
    }

    // ========================================
    // RENDER METHOD
    // ========================================

    public function render()
    {
        return view('livewire.wali.leger-kelas', [
            'totalStudents' => count($this->studentsList),
            'totalMataPelajaran' => count($this->mataPelajaranList),
        ]);
    }
}
