<?php

namespace App\Livewire\Wali;

use App\Models\Nilai;
use App\Models\Rombel;
use Livewire\Component;
use App\Models\Kehadiran;
use App\Models\Pengaturan;
use App\Models\DataSekolah;
use App\Models\Kokurikuler;
use App\Models\TahunAjaran;
use App\Models\EkskulPelajar;
use App\Models\RombelPelajar;
use App\Models\CatatanWaliKelas;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache; // Wajib import Cache

class PreviewPdfRapor extends Component
{
    // ========================================
    // PROPERTIES
    // ========================================

    public $rombel;
    public $semesterAktif;
    public $dataSekolah = null;

    // Data Collections (Ringan - Hanya ID & Nama)
    public $studentsList = [];

    // Navigation properties
    public $currentIndex = 0;

    // Variabel ini akan berisi detail lengkap siswa yang SEDANG dipilih
    public $currentStudent = null;

    public $pdfUrl;

    // View selector
    public $selectedPage = 'cover'; // cover = biodata, content = nilai

    protected $queryString = [
        'selectedPage' => ['except' => 'cover'],
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

        $this->loadSemesterAktif();

        if (!$this->semesterAktif) {
            session()->flash('warning', 'Tidak ada semester aktif saat ini.');
            return;
        }

        $this->loadDataSekolah();

        // Panggil metode load yang ringan
        $this->loadStudentsListOnly();
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

    // ========================================
    // UPDATED HOOKS
    // ========================================

    public function updatedSelectedPage(): void
    {
        $this->generatePdfUrl();
    }

    // ========================================
    // LOAD STUDENTS (OPTIMIZED)
    // ========================================

    // 1. Ambil List Siswa (Versi Ringan)
    private function loadStudentsListOnly(): void
    {
        if (!$this->rombel || !$this->semesterAktif) {
            $this->studentsList = [];
            $this->currentStudent = null;
            $this->currentIndex = 0;
            return;
        }

        // Hanya ambil ID dan Nama. JANGAN load nilai di sini.
        $this->studentsList = RombelPelajar::with('pelajar:id,nama_lengkap,nomor_induk')
            ->where('rombel_id', $this->rombel->id)
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->pelajar->id,
                    'nama' => $item->pelajar->nama_lengkap,
                    'nis' => $item->pelajar->nomor_induk
                ];
            })->toArray();

        // Otomatis load detail siswa pertama jika ada
        if (!empty($this->studentsList)) {
            if (!isset($this->studentsList[$this->currentIndex])) {
                $this->currentIndex = 0;
            }
            $this->loadCurrentStudent(); // Load detail berat di sini
        }
    }

    // 2. Ambil Detail Lengkap (Hanya untuk 1 Siswa)
    public function loadCurrentStudent()
    {
        if (!isset($this->studentsList[$this->currentIndex])) {
            $this->currentStudent = null;
            $this->pdfUrl = '';
            return;
        }

        $simpleStudent = $this->studentsList[$this->currentIndex];
        $pelajarId = $simpleStudent['id'];

        // Ambil Data Lengkap Pelajar (Ortu, dll)
        $pelajar = \App\Models\Pelajar::with(['orangTuaWalis' => function ($q) {
            $q->orderBy('hubungan', 'asc');
        }])->find($pelajarId);

        if (!$pelajar) return;

        $orangTuaWalis = $pelajar->orangTuaWalis ?? collect();
        $tingkat = $this->rombel->tingkat ?? 0;
        $fase = ((int)$tingkat === 10) ? 'E' : 'F';

        // Load Nilai & Data Pendukung (Hanya untuk siswa ini)
        $nilaiData = $this->loadNilaiPelajar($pelajar->id);
        $kokurikulerData = $this->loadKokurikuler($pelajar->id);
        $ekskulData = $this->loadEkstrakurikuler($pelajar->id);
        $kehadiranData = $this->loadKehadiran($pelajar->id);
        $catatanData = $this->loadCatatanWali($pelajar->id);

        // Susun Data Detail
        $this->currentStudent = [
            'id' => $pelajar->id,
            'nis' => $pelajar->nomor_induk,
            'nisn' => $pelajar->nisn,
            'nama' => $pelajar->nama_lengkap,
            'tempat_lahir' => $pelajar->tempat_lahir,
            'tanggal_lahir' => $pelajar->tanggal_lahir,
            'jenis_kelamin' => $pelajar->jenis_kelamin,
            'agama' => $pelajar->agama,
            'status_dalam_keluarga' => $pelajar->status_dalam_keluarga,
            'anak_ke' => $pelajar->anak_ke,
            'alamat' => $pelajar->alamat,
            'telepon' => $pelajar->telepon,
            'sekolah_asal' => $pelajar->sekolah_asal,
            'diterima_di_kelas' => $pelajar->diterima_di_kelas,
            'pada_tanggal' => $pelajar->pada_tanggal,
            'kelas' => $this->rombel->nama,
            'fase' => $fase,
            // Orang Tua / Wali
            'ayah' => $this->formatOrangTua($orangTuaWalis->firstWhere('hubungan', 'ayah')),
            'ibu' => $this->formatOrangTua($orangTuaWalis->firstWhere('hubungan', 'ibu')),
            'wali' => $this->formatOrangTua($orangTuaWalis->firstWhere('hubungan', 'wali')),
            // Data Nilai & Kegiatan
            'nilai' => $nilaiData['nilai'],
            'nilai_grouped' => $nilaiData['nilai_grouped'],
            'kokurikuler' => $kokurikulerData,
            'ekstrakurikuler' => $ekskulData,
            'ketidakhadiran' => $kehadiranData,
            'catatan_wali' => $catatanData,
            'tanggapan_ortu' => '',
        ];

        // Generate URL setelah data siap
        $this->generatePdfUrl();
    }

    // ========================================
    // DATA LOADING METHODS (HELPER)
    // ========================================

    private function loadNilaiPelajar($pelajarId): array
    {
        $kurikulumId = $this->rombel->tahunAjaranKurikulum->kurikulum_id ?? null;
        $tingkatRombel = $this->rombel->tingkat ?? null;

        if (!$this->rombel || !$kurikulumId || !$tingkatRombel) {
            return ['nilai' => [], 'nilai_grouped' => []];
        }

        // Ambil hash agama
        $pelajar = \App\Models\Pelajar::select('id', 'agama_hash')->find($pelajarId);
        $agamaPelajarHash = $pelajar ? $pelajar->agama_hash : null;

        $dataMapel = \App\Models\RombelPengajar::query()
            ->join('mata_pelajarans as mp', 'rombel_pengajars.mata_pelajaran_id', '=', 'mp.id')
            ->join('kurikulum_mata_pelajarans as kmp', function ($join) use ($kurikulumId, $tingkatRombel) {
                $join->on('mp.id', '=', 'kmp.mata_pelajaran_id')
                    ->where('kmp.kurikulum_id', '=', $kurikulumId)
                    ->where('kmp.tingkat', '=', $tingkatRombel);
            })
            ->join('mata_pelajaran_kelompoks as mpk', 'kmp.kelompok_id', '=', 'mpk.id')
            ->leftJoin('nilais', function ($join) use ($pelajarId) {
                $join->on('mp.id', '=', 'nilais.mata_pelajaran_id')
                    ->where('nilais.pelajar_id', '=', $pelajarId)
                    ->where('nilais.tahun_ajaran_semester_id', '=', $this->semesterAktif->id);
            })
            ->where('rombel_pengajars.rombel_id', $this->rombel->id)
            ->where(function ($query) use ($agamaPelajarHash) {
                $query->where('mp.is_mapel_agama', false)
                    ->orWhere(function ($q) use ($agamaPelajarHash) {
                        $q->where('mp.is_mapel_agama', true);
                        if ($agamaPelajarHash) {
                            $q->where('mp.agama_terkait_hash', $agamaPelajarHash);
                        } else {
                            $q->whereNull('mp.id');
                        }
                    });
            })
            ->select(
                'mp.id',
                'mp.nama as mapel_nama',
                'mpk.nama as kelompok_nama',
                'mpk.kode as kelompok_kode',
                'kmp.urutan',
                'nilais.nilai_angka',
                'nilais.predikat',
                'nilais.capaian_kompetensi'
            )
            ->orderBy('kmp.urutan', 'asc')
            ->get();

        $nilaiArray = [];
        $nilaiGrouped = [];
        $counter = 1;

        foreach ($dataMapel as $row) {
            $nilaiAngka = $row->nilai_angka ? round($row->nilai_angka) : 0;
            $item = [
                'no' => $counter++,
                'mapel' => $row->mapel_nama,
                'kelompok' => $row->kelompok_nama,
                'kelompok_kode' => $row->kelompok_kode,
                'nilai' => $nilaiAngka,
                'predikat' => $row->predikat ?? '-',
                'capaian' => $row->capaian_kompetensi ?? '',
            ];
            $nilaiArray[] = $item;
            $kelompokNama = $row->kelompok_nama;
            if (!isset($nilaiGrouped[$kelompokNama])) {
                $nilaiGrouped[$kelompokNama] = ['kode' => $row->kelompok_kode, 'items' => []];
            }
            $nilaiGrouped[$kelompokNama]['items'][] = $item;
        }

        return ['nilai' => $nilaiArray, 'nilai_grouped' => $nilaiGrouped];
    }

    private function loadKokurikuler($pelajarId): string
    {
        if (!$this->semesterAktif) return '';
        $kokurikuler = Kokurikuler::where('pelajar_id', $pelajarId)
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->first();
        return $kokurikuler ? ($kokurikuler->capaian ?? '') : '';
    }

    private function loadEkstrakurikuler($pelajarId): array
    {
        if (!$this->semesterAktif) return [];
        $ekskuls = EkskulPelajar::with('ekstrakurikuler')
            ->where('pelajar_id', $pelajarId)
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->get();
        return $ekskuls->map(function ($ekskul) {
            return [
                'nama' => $ekskul->ekstrakurikuler->nama ?? 'N/A',
                'keterangan' => $ekskul->deskripsi ?? 'Tidak ada keterangan.'
            ];
        })->toArray();
    }

    private function loadKehadiran($pelajarId): array
    {
        if (!$this->semesterAktif || !$this->rombel) return ['sakit' => 0, 'izin' => 0, 'tanpa_keterangan' => 0];
        $kehadiran = Kehadiran::where('pelajar_id', $pelajarId)
            ->where('rombel_id', $this->rombel->id)
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->first();
        return [
            'sakit' => $kehadiran->jumlah_sakit ?? 0,
            'izin' => $kehadiran->jumlah_izin ?? 0,
            'tanpa_keterangan' => $kehadiran->jumlah_tanpa_keterangan ?? 0,
        ];
    }

    private function loadCatatanWali($pelajarId): string
    {
        if (!$this->semesterAktif) return '';
        $catatan = CatatanWaliKelas::where('pelajar_id', $pelajarId)
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->first();
        return $catatan ? ($catatan->catatan ?? '') : '';
    }

    // ========================================
    // STUDENT NAVIGATION
    // ========================================

    public function nextStudent()
    {
        if ($this->currentIndex < count($this->studentsList) - 1) {
            $this->currentIndex++;
            $this->loadCurrentStudent();
            $this->dispatch('student-changed', direction: 'next', student: $this->currentStudent['nama']);
        }
    }

    public function previousStudent()
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
            $this->loadCurrentStudent();
            $this->dispatch('student-changed', direction: 'previous', student: $this->currentStudent['nama']);
        }
    }

    public function selectStudent($index)
    {
        $this->currentIndex = (int)$index;
        $this->loadCurrentStudent();
    }

    // ========================================
    // PDF GENERATION (OPTIMIZED WITH CACHE)
    // ========================================

    public function generatePdfUrl()
    {
        if (!$this->currentStudent || !$this->semesterAktif) {
            $this->pdfUrl = '';
            return;
        }

        $pengaturan = Pengaturan::with('kepalaSekolah')
            ->where('tahun_ajaran_semester_id', $this->semesterAktif->id)
            ->first();

        $pdfData = [
            'nama' => $this->currentStudent['nama'],
            'nis' => $this->currentStudent['nis'],
            'nisn' => $this->currentStudent['nisn'],
            'kelas' => $this->currentStudent['kelas'],
            'fase' => $this->currentStudent['fase'],
            'tempat_lahir' => $this->currentStudent['tempat_lahir'],
            'tanggal_lahir' => $this->currentStudent['tanggal_lahir'],
            'jenis_kelamin' => $this->currentStudent['jenis_kelamin'],
            'agama' => $this->currentStudent['agama'],
            'status_dalam_keluarga' => $this->currentStudent['status_dalam_keluarga'],
            'anak_ke' => $this->currentStudent['anak_ke'],
            'alamat' => $this->currentStudent['alamat'],
            'telepon' => $this->currentStudent['telepon'],
            'sekolah_asal' => $this->currentStudent['sekolah_asal'],
            'diterima_di_kelas' => $this->currentStudent['diterima_di_kelas'],
            'pada_tanggal' => $this->currentStudent['pada_tanggal'],

            // Sekolah
            'sekolah' => [
                'nama_sekolah' => $this->dataSekolah->nama_sekolah ?? 'N/A',
                'npsn' => $this->dataSekolah->npsn ?? 'N/A',
                'nis' => $this->dataSekolah->nis ?? '',
                'nss' => $this->dataSekolah->nss ?? '',
                'nds' => $this->dataSekolah->nds ?? '',
                'alamat' => $this->dataSekolah->alamat ?? 'N/A',
                'kode_pos' => $this->dataSekolah->kode_pos ?? 'N/A',
                'kelurahan' => $this->dataSekolah->kelurahan ?? 'N/A',
                'kecamatan' => $this->dataSekolah->kecamatan ?? 'N/A',
                'kota_kabupaten' => $this->dataSekolah->kota_kabupaten ?? 'N/A',
                'provinsi' => $this->dataSekolah->provinsi ?? 'N/A',
                'telepon' => $this->dataSekolah->telepon ?? 'N/A',
                'website' => $this->dataSekolah->website ?? 'N/A',
                'email' => $this->dataSekolah->email ?? 'N/A',
                'logo_sekolah_path' => $this->dataSekolah->logo_sekolah_path ?? null,
                'logo_pemda_path' => $this->dataSekolah->logo_pemda_path ?? null,
            ],

            'semester_nama' => $this->semesterAktif->semester->nama ?? 'N/A',
            'semester_urutan' => $this->semesterAktif->semester->urutan ?? 'N/A',
            'tahun_ajaran' => $this->semesterAktif->tahunAjaran->nama ?? 'N/A',

            // Orang Tua / Wali
            'ayah' => $this->formatOrangTua($this->currentStudent['ayah']),
            'ibu' => $this->formatOrangTua($this->currentStudent['ibu']),
            'wali' => $this->formatOrangTua($this->currentStudent['wali']),

            // Data Nilai
            'nilai' => $this->currentStudent['nilai'],
            'nilai_grouped' => $this->currentStudent['nilai_grouped'],
            'kokurikuler' => $this->currentStudent['kokurikuler'],
            'ekstrakurikuler' => $this->currentStudent['ekstrakurikuler'],
            'ketidakhadiran' => $this->currentStudent['ketidakhadiran'],
            'catatan_wali' => $this->currentStudent['catatan_wali'],
            'tanggapan_ortu' => $this->currentStudent['tanggapan_ortu'],

            // Tanda Tangan
            'wali_kelas' => ['nama' => $this->rombel->waliKelas->name ?? 'N/A', 'nip' => $this->rombel->waliKelas->nip ?? '~'],
            'kepala_sekolah' => ['nama' => $pengaturan->kepalaSekolah->name ?? 'N/A', 'nip' => $pengaturan->kepalaSekolah->nip ?? 'N/A'],
            'tanggal_rapor' => $pengaturan->tanggal_rapor ?? null,
        ];

        // --- SIMPAN KE CACHE ---
        $userId = Auth::id() ?? 'guest';
        $studentId = $this->currentStudent['id'] ?? 'unknown';
        $cacheKey = "rapor_print_{$userId}_{$studentId}";

        Cache::put($cacheKey, $pdfData, 600); // 10 menit

        $this->pdfUrl = route('pdf.generate') . '?key=' . $cacheKey . '&view=' . $this->selectedPage;
    }

    private function formatOrangTua($orangTua): array
    {
        if (!$orangTua) return ['nama' => '-', 'pekerjaan' => '-', 'telepon' => '-', 'alamat' => '-', 'status' => '-'];
        $kodePekerjaan = $orangTua->pekerjaan ?? null;
        $labelPekerjaan = config("enums.pekerjaan.$kodePekerjaan") ?? $kodePekerjaan ?? '-';
        return ['nama' => $orangTua->nama ?? '-', 'pekerjaan' => $labelPekerjaan, 'telepon' => $orangTua->telepon ?? '-', 'alamat' => $orangTua->alamat ?? '-', 'status' => $orangTua->status ?? 'Masih Hidup'];
    }

    // ========================================
    // RENDER METHOD
    // ========================================

    public function render()
    {
        return view('livewire.wali.preview-pdf-rapor', [
            'totalStudents' => count($this->studentsList),
        ]);
    }
}
