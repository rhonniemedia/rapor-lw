<?php

namespace App\Livewire\Wali;

use App\Models\Nilai;
use App\Models\Rombel;
use Livewire\Component;
use Livewire\Attributes\Computed;
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
use Illuminate\Support\Facades\Cache;

class PreviewRapor extends Component
{
    // ========================================
    // PROPERTIES (STATE)
    // ========================================

    // Filter Properties
    public $tahunAjaranId = null;
    public $semesterId = null;

    // Data Collections
    public $tahunAjaranList = [];
    public $semesterList = [];

    // Main Data
    public $rombel;

    // Navigation & State
    public $currentIndex = 0;
    public $selectedPage = 'cover';

    // Data Utama Siswa Terpilih
    public $currentStudent = null;
    public $pdfUrl = null;

    protected $queryString = [
        'selectedPage'  => ['except' => 'cover'],
        'tahunAjaranId' => ['except' => null],
        'semesterId'    => ['except' => null],
    ];

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
            $this->loadCurrentStudent();
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

    private function loadRombelWaliKelas(): void
    {
        $user = Auth::user();
        $this->rombel = Rombel::with([
            'tahunAjaranKurikulum.tahunAjaran',
            'tahunAjaranKurikulum.kurikulum',
            'waliKelas',
            'jurusan'
        ])->where('wali_kelas_slug', $user->slug ?? $user->id)->first();
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
            $this->resetStudentData();
        }
    }

    public function updatedSemesterId(): void
    {
        $this->currentIndex = 0;
        if ($this->semesterId) {
            $this->loadCurrentStudent();
        } else {
            $this->resetStudentData();
        }
    }

    private function resetStudentData(): void
    {
        $this->currentStudent = null;
        $this->pdfUrl = null;
    }

    public function updatedSelectedPage(): void
    {
        $this->generatePdfUrl();
    }

    // ========================================
    // COMPUTED PROPERTIES
    // ========================================

    #[Computed]
    public function dataSekolah()
    {
        return DataSekolah::first();
    }

    #[Computed]
    public function studentsList()
    {
        if (!$this->rombel) return [];

        return RombelPelajar::with('pelajar:id,nama_lengkap,nomor_induk')
            ->where('rombel_id', $this->rombel->id)
            ->get()
            ->map(function ($item) {
                return [
                    'id'   => $item->pelajar->id,
                    'nama' => $item->pelajar->nama_lengkap,
                    'nis'  => $item->pelajar->nomor_induk
                ];
            })
            ->sortBy('nama')
            ->values()
            ->toArray();
    }

    // ========================================
    // LOGIKA UTAMA (LOAD DATA)
    // ========================================

    public function loadCurrentStudent()
    {
        $list = $this->studentsList;

        if (empty($list) || !isset($list[$this->currentIndex])) {
            $this->resetStudentData();
            return;
        }

        $simpleStudent = $list[$this->currentIndex];
        $pelajarId = $simpleStudent['id'];

        $pelajar = \App\Models\Pelajar::with(['orangTuaWalis' => function ($q) {
            $q->orderBy('hubungan', 'asc');
        }])->find($pelajarId);

        if (!$pelajar) return;

        $orangTuaWalis = $pelajar->orangTuaWalis ?? collect();
        $rombel = $this->rombel;
        $tingkat = $rombel->tingkat ?? 0;
        $fase = ((int)$tingkat === 10) ? 'E' : 'F';

        $nilaiData = $this->loadNilaiPelajar($pelajar->id);

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
            'kelas' => $rombel->nama,
            'fase' => $fase,
            'tingkat' => $tingkat,
            'ayah' => $this->formatOrangTua($orangTuaWalis->firstWhere('hubungan', 'ayah')),
            'ibu' => $this->formatOrangTua($orangTuaWalis->firstWhere('hubungan', 'ibu')),
            'wali' => $this->formatOrangTua($orangTuaWalis->firstWhere('hubungan', 'wali')),
            'nilai' => $nilaiData['nilai'],
            'nilai_grouped' => $nilaiData['nilai_grouped'],
            'kokurikuler' => $this->loadKokurikuler($pelajar->id),
            'ekstrakurikuler' => $this->loadEkstrakurikuler($pelajar->id),
            'ketidakhadiran' => $this->loadKehadiran($pelajar->id),
            'catatan_wali' => $this->loadCatatanWali($pelajar->id),
            'tanggapan_ortu' => '',
        ];

        $this->generatePdfUrl();
    }

    public function generatePdfUrl()
    {
        if (!$this->currentStudent || !$this->semesterId) {
            $this->pdfUrl = '';
            return;
        }

        $selectedSemesterObj = TahunAjaranSemester::with(['semester', 'tahunAjaran'])->find($this->semesterId);

        $pengaturan = Pengaturan::with('kepalaSekolah')
            ->where('tahun_ajaran_semester_id', $this->semesterId)
            ->first();

        $sekolah = $this->dataSekolah;
        $rombel = $this->rombel;

        $pdfData = [
            ...$this->currentStudent,
            'sekolah' => [
                'nama_sekolah' => $sekolah->nama_sekolah ?? 'N/A',
                'npsn' => $sekolah->npsn ?? 'N/A',
                'nis' => $sekolah->nis ?? '',
                'nss' => $sekolah->nss ?? '',
                'nds' => $sekolah->nds ?? '',
                'alamat' => $sekolah->alamat ?? 'N/A',
                'kode_pos' => $sekolah->kode_pos ?? 'N/A',
                'kelurahan' => $sekolah->kelurahan ?? 'N/A',
                'kecamatan' => $sekolah->kecamatan ?? 'N/A',
                'kota_kabupaten' => $sekolah->kota_kabupaten ?? 'N/A',
                'provinsi' => $sekolah->provinsi ?? 'N/A',
                'telepon' => $sekolah->telepon ?? 'N/A',
                'website' => $sekolah->website ?? 'N/A',
                'email' => $sekolah->email ?? 'N/A',
                'logo_sekolah_path' => $sekolah->logo_sekolah_path ?? null,
                'logo_pemda_path' => $sekolah->logo_pemda_path ?? null,
            ],
            'semester_nama' => $selectedSemesterObj->semester->nama ?? 'N/A',
            'semester_urutan' => $selectedSemesterObj->semester->urutan ?? 'N/A',
            'tahun_ajaran' => $selectedSemesterObj->tahunAjaran->nama ?? 'N/A',
            'ayah' => $this->currentStudent['ayah'],
            'ibu'  => $this->currentStudent['ibu'],
            'wali' => $this->currentStudent['wali'],
            'wali_kelas' => ['nama' => $rombel->waliKelas->name ?? 'N/A', 'nip' => $rombel->waliKelas->nip ?? '~'],
            'kepala_sekolah' => ['nama' => $pengaturan->kepalaSekolah->name ?? 'N/A', 'nip' => $pengaturan->kepalaSekolah->nip ?? 'N/A'],
            'tanggal_rapor' => $pengaturan->tanggal_rapor ?? null,
        ];

        $userId = Auth::id() ?? 'guest';
        $studentId = $this->currentStudent['id'] ?? 'unknown';
        $cacheKey = "rapor_print_{$userId}_{$studentId}";

        Cache::put($cacheKey, $pdfData, 600);

        // PENAMBAHAN: parameter timestamp `&t=` ditambahkan agar cache dari iframe browser terbypass ketika file yang sama di-generate ulang
        $timestamp = now()->timestamp;
        $this->pdfUrl = route('pdf.generate') . '?key=' . $cacheKey . '&view=' . $this->selectedPage . '&t=' . $timestamp;
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    private function loadNilaiPelajar($pelajarId): array
    {
        $rombel = $this->rombel;
        $kurikulumId = $rombel->tahunAjaranKurikulum->kurikulum_id ?? null;
        $tingkatRombel = $rombel->tingkat ?? null;

        if (!$rombel || !$kurikulumId || !$tingkatRombel) {
            return ['nilai' => [], 'nilai_grouped' => []];
        }

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
                    ->where('nilais.tahun_ajaran_semester_id', '=', $this->semesterId);
            })
            ->where('rombel_pengajars.rombel_id', $rombel->id)
            ->where(function ($query) use ($agamaPelajarHash) {
                $query->where('mp.is_mapel_agama', false)
                    ->orWhere(function ($q) use ($agamaPelajarHash) {
                        $q->where('mp.is_mapel_agama', true);
                        if ($agamaPelajarHash) $q->where('mp.agama_terkait_hash', $agamaPelajarHash);
                        else $q->whereNull('mp.id');
                    });
            })
            ->select('mp.id', 'mp.nama as mapel_nama', 'mpk.nama as kelompok_nama', 'mpk.kode as kelompok_kode', 'kmp.urutan', 'nilais.nilai_angka', 'nilais.predikat', 'nilais.capaian_kompetensi')
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
        $k = Kokurikuler::where('pelajar_id', $pelajarId)->where('tahun_ajaran_semester_id', $this->semesterId)->first();
        return $k ? ($k->capaian ?? '') : '';
    }

    private function loadEkstrakurikuler($pelajarId): array
    {
        $e = EkskulPelajar::with('ekstrakurikuler')->where('pelajar_id', $pelajarId)->where('tahun_ajaran_semester_id', $this->semesterId)->get();
        return $e->map(function ($x) {
            return ['nama' => $x->ekstrakurikuler->nama ?? 'N/A', 'keterangan' => $x->deskripsi ?? 'Tidak ada keterangan.'];
        })->toArray();
    }

    private function loadKehadiran($pelajarId): array
    {
        $k = Kehadiran::where('pelajar_id', $pelajarId)->where('rombel_id', $this->rombel->id)->where('tahun_ajaran_semester_id', $this->semesterId)->first();
        return ['sakit' => $k->jumlah_sakit ?? 0, 'izin' => $k->jumlah_izin ?? 0, 'tanpa_keterangan' => $k->jumlah_tanpa_keterangan ?? 0];
    }

    private function loadCatatanWali($pelajarId): string
    {
        $c = CatatanWaliKelas::where('pelajar_id', $pelajarId)->where('tahun_ajaran_semester_id', $this->semesterId)->first();
        return $c ? ($c->catatan ?? '') : '';
    }

    private function formatOrangTua($orangTua): array
    {
        if (!$orangTua) return ['nama' => '-', 'pekerjaan' => '-', 'telepon' => '-', 'alamat' => '-', 'status' => '-'];
        $kodePekerjaan = $orangTua->pekerjaan ?? null;
        $labelPekerjaan = config("enums.pekerjaan.$kodePekerjaan") ?? $kodePekerjaan ?? '-';
        $telepon = empty($orangTua->telepon) ? '-' : $orangTua->telepon;
        $alamat = empty($orangTua->alamat) ? '-' : $orangTua->alamat;
        return ['nama' => $orangTua->nama ?? '-', 'pekerjaan' => $labelPekerjaan, 'telepon' => $telepon, 'alamat' => $alamat, 'status' => $orangTua->status ?? 'Masih Hidup'];
    }

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
    // RENDER
    // ========================================

    public function render()
    {
        $selectedSemesterObj = $this->semesterId
            ? collect($this->semesterList)->firstWhere('id', $this->semesterId)
            : null;

        if (!$selectedSemesterObj && $this->semesterId) {
            $selectedSemesterObj = TahunAjaranSemester::with(['semester', 'tahunAjaran'])->find($this->semesterId);
        }

        return view('livewire.wali.preview-rapor', [
            'totalStudents'       => count($this->studentsList),
            'selectedSemesterObj' => $selectedSemesterObj,
            'rombel'              => $this->rombel,
        ]);
    }
}
