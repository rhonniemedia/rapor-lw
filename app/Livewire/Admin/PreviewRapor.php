<?php

namespace App\Livewire\Admin;

use App\Models\Nilai;
use App\Models\Rombel;
use Livewire\Component;
use Livewire\Attributes\Computed; // WAJIB: Import ini untuk fitur Computed
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
    // --- Filter Properties (Tetap Public untuk wire:model) ---
    public $tahunAjaranId = null;
    public $semesterId = null;
    public $rombelId = null;

    // --- Navigation & View ---
    public $currentIndex = 0;
    public $selectedPage = 'cover';

    // --- Data Utama Siswa Terpilih (Tetap Public untuk View) ---
    public $currentStudent = null;
    public $pdfUrl = null;

    protected $queryString = [
        'tahunAjaranId' => ['except' => null],
        'semesterId' => ['except' => null],
        'rombelId' => ['except' => null],
        'selectedPage' => ['except' => 'cover'],
    ];

    public function mount()
    {
        // Set Default Filter jika kosong
        if (!$this->tahunAjaranId) {
            $this->tahunAjaranId = TahunAjaran::where('status', 'aktif')->value('id');
        }

        if ($this->tahunAjaranId && !$this->semesterId) {
            $this->semesterId = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahunAjaranId)
                ->where('status', 'aktif')->value('id');
        }

        // Jika filter sudah lengkap (misal dari refresh page), load siswa
        if ($this->rombelId && $this->semesterId) {
            $this->loadCurrentStudent();
        }
    }

    // =================================================================
    // 1. COMPUTED PROPERTIES (PENGGANTI VARIABEL LIST MANUAL)
    // =================================================================

    #[Computed]
    public function dataSekolah()
    {
        return DataSekolah::first();
    }

    #[Computed]
    public function tahunAjaranList()
    {
        return TahunAjaran::orderBy('tgl_mulai', 'desc')->get();
    }

    #[Computed]
    public function semesterList()
    {
        if (!$this->tahunAjaranId) return [];
        return TahunAjaranSemester::with('semester')
            ->where('tahun_ajaran_id', $this->tahunAjaranId)
            ->get();
    }

    #[Computed]
    public function rombelList()
    {
        if (!$this->tahunAjaranId) return [];
        return Rombel::whereHas('tahunAjaran', function ($q) {
            $q->where('tahun_ajaran_id', $this->tahunAjaranId);
        })->orderBy('tingkat', 'asc')->orderBy('nama', 'asc')->get();
    }

    #[Computed]
    public function rombel() // Pengganti variable $this->rombel
    {
        if (!$this->rombelId) return null;
        return Rombel::with(['tahunAjaranKurikulum.tahunAjaran', 'tahunAjaranKurikulum.kurikulum', 'waliKelas', 'jurusan'])
            ->find($this->rombelId);
    }

    #[Computed]
    public function studentsList()
    {
        if (!$this->rombelId || !$this->semesterId) return [];

        // Query Ringan: Hanya ID dan Nama
        return RombelPelajar::with('pelajar:id,nama_lengkap,nomor_induk')
            ->where('rombel_id', $this->rombelId)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->pelajar->id,
                    'nama' => $item->pelajar->nama_lengkap,
                    'nis' => $item->pelajar->nomor_induk
                ];
            })
            ->sortBy('nama') // Urutkan nama
            ->values()       // Reset index array
            ->toArray();
    }

    // =================================================================
    // 2. LOGIKA UTAMA (LOAD SINGLE STUDENT)
    // =================================================================

    public function loadCurrentStudent()
    {
        // Akses computed property menggunakan $this->studentsList
        $list = $this->studentsList;

        if (empty($list) || !isset($list[$this->currentIndex])) {
            $this->currentStudent = null;
            $this->pdfUrl = '';
            return;
        }

        $simpleStudent = $list[$this->currentIndex];
        $pelajarId = $simpleStudent['id'];

        // Ambil Data Lengkap (Berat) hanya untuk 1 siswa
        $pelajar = \App\Models\Pelajar::with(['orangTuaWalis' => function ($q) {
            $q->orderBy('hubungan', 'asc');
        }])->find($pelajarId);

        if (!$pelajar) return;

        $orangTuaWalis = $pelajar->orangTuaWalis ?? collect();
        $rombel = $this->rombel; // Akses computed property
        $tingkat = $rombel->tingkat ?? 0;
        $fase = ((int)$tingkat === 10) ? 'E' : 'F';

        // Load Data Penunjang
        $nilaiData = $this->loadNilaiPelajar($pelajarId);

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
            'telepon' => $pelajar->telepon ?? '-',
            'sekolah_asal' => $pelajar->sekolah_asal,
            'diterima_di_kelas' => $pelajar->diterima_di_kelas,
            'pada_tanggal' => $pelajar->pada_tanggal,
            'kelas' => $rombel->nama ?? '-',
            'fase' => $fase,
            'tingkat' => $tingkat,
            'ayah' => $this->formatOrangTua($orangTuaWalis->firstWhere('hubungan', 'ayah')),
            'ibu' => $this->formatOrangTua($orangTuaWalis->firstWhere('hubungan', 'ibu')),
            'wali' => $this->formatOrangTua($orangTuaWalis->firstWhere('hubungan', 'wali')),
            'nilai' => $nilaiData['nilai'],
            'nilai_grouped' => $nilaiData['nilai_grouped'],
            'kokurikuler' => $this->loadKokurikuler($pelajarId),
            'ekstrakurikuler' => $this->loadEkstrakurikuler($pelajarId),
            'ketidakhadiran' => $this->loadKehadiran($pelajarId),
            'catatan_wali' => $this->loadCatatanWali($pelajarId),
            'tanggapan_ortu' => '',
        ];

        $this->generatePdfUrl();
    }

    public function generatePdfUrl()
    {
        if (!$this->currentStudent) {
            $this->pdfUrl = '';
            return;
        }

        $selectedTahunAjaran = TahunAjaran::find($this->tahunAjaranId);
        $selectedSemester = TahunAjaranSemester::find($this->semesterId);
        $pengaturan = Pengaturan::with('kepalaSekolah')->where('tahun_ajaran_semester_id', $this->semesterId)->first();

        $sekolah = $this->dataSekolah; // Akses Computed
        $rombel = $this->rombel;       // Akses Computed

        $pdfData = array_merge($this->currentStudent, [
            'sekolah' => [
                'nama_sekolah' => $sekolah->nama_sekolah ?? 'N/A',
                'npsn' => $sekolah->npsn ?? 'N/A',
                'nss' => $sekolah->nss ?? '',
                'alamat' => $sekolah->alamat ?? 'N/A',
                'kode_pos' => $sekolah->kode_pos ?? 'N/A',
                'kelurahan' => $sekolah->kelurahan ?? 'N/A',
                'kecamatan' => $sekolah->kecamatan ?? 'N/A',
                'kota_kabupaten' => $sekolah->kota_kabupaten ?? 'N/A',
                'provinsi' => $sekolah->provinsi ?? 'N/A',
                'telepon' => $sekolah->telepon ?? 'N/A',
                'website' => $sekolah->website ?? 'N/A',
                'email' => $sekolah->email ?? 'N/A',
            ],
            'semester_nama' => $selectedSemester->semester->nama ?? 'N/A',
            'semester_urutan' => $selectedSemester->semester->urutan ?? 'N/A',
            'tahun_ajaran' => $selectedTahunAjaran->nama ?? 'N/A',
            'wali_kelas' => ['nama' => $rombel->waliKelas->name ?? 'N/A', 'nip' => $rombel->waliKelas->nip ?? '~'],
            'kepala_sekolah' => ['nama' => $pengaturan->kepalaSekolah->name ?? 'N/A', 'nip' => $pengaturan->kepalaSekolah->nip ?? 'N/A'],
            'tanggal_rapor' => $pengaturan->tanggal_rapor ?? null,
        ]);

        $userId = Auth::id() ?? 'guest';
        $studentId = $this->currentStudent['id'];
        $cacheKey = "rapor_print_{$userId}_{$studentId}";

        Cache::put($cacheKey, $pdfData, 600); // 10 menit

        $this->pdfUrl = route('pdf.generate') . '?key=' . $cacheKey . '&view=' . $this->selectedPage;
    }

    // --- Query Helpers (Logic tetap sama, hanya penyesuaian variable) ---
    private function loadNilaiPelajar($pelajarId): array
    {
        $rombel = $this->rombel; // Akses Computed
        if (!$this->rombelId || !$rombel || !$rombel->tahunAjaranKurikulum) return ['nilai' => [], 'nilai_grouped' => []];

        $kurikulumId = $rombel->tahunAjaranKurikulum->kurikulum_id;
        $tingkatRombel = $rombel->tingkat;

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
            ->where('rombel_pengajars.rombel_id', $this->rombelId)
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

    // Helper data pendukung (Tetap sama)
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
        $k = Kehadiran::where('pelajar_id', $pelajarId)->where('rombel_id', $this->rombelId)->where('tahun_ajaran_semester_id', $this->semesterId)->first();
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
        return [
            'nama' => $orangTua->nama ?? '-',
            'pekerjaan' => $labelPekerjaan,
            'telepon' => $telepon,
            'alamat' => $alamat,
            'status' => $orangTua->status ?? 'Masih Hidup'
        ];
    }


    // --- Update Handlers (Jauh lebih simpel karena Computed otomatis update) ---
    public function updatedTahunAjaranId()
    {
        $this->semesterId = null;
        $this->rombelId = null;
        $this->resetStudent();
    }
    public function updatedSemesterId()
    {
        $this->rombelId = null;
        $this->resetStudent();
    }
    public function updatedRombelId()
    {
        $this->resetStudent();
        // Otomatis load siswa pertama
        if ($this->rombelId && $this->semesterId) {
            $this->loadCurrentStudent();
        }
    }
    public function updatedSelectedPage()
    {
        $this->generatePdfUrl();
    }

    private function resetStudent()
    {
        $this->currentStudent = null;
        $this->currentIndex = 0;
        $this->pdfUrl = null;
    }

    // --- Navigation ---
    public function nextStudent()
    {
        // Akses via computed $this->studentsList
        if ($this->currentIndex < count($this->studentsList) - 1) {
            $this->currentIndex++;
            $this->loadCurrentStudent();
        }
    }

    public function previousStudent()
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
            $this->loadCurrentStudent();
        }
    }

    public function selectStudent($index)
    {
        $this->currentIndex = (int)$index;
        $this->loadCurrentStudent();
    }

    public function render()
    {
        // Total students dikirim manual karena dibutuhkan untuk tombol next/prev
        return view('livewire.admin.preview-rapor', [
            'totalStudents' => count($this->studentsList),
        ]);
    }
}
