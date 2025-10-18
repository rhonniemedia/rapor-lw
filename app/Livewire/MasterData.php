<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Rombel;
use App\Models\Jurusan;
use App\Models\Pelajar;
use Livewire\Component;
use App\Models\Semester;
use App\Models\Kurikulum;
use App\Models\TahunAjaran;
use App\Models\MataPelajaran;
use App\Models\Ekstrakurikuler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class MasterData extends Component
{
    // Properti untuk menyimpan data hitungan
    public $masterData = [];

    // Properti untuk data API
    public $apiDataRombel = null;  // ubah dari [] jadi null
    public $apiDataRombelDetail = null;
    public $apiDataPesertaDidik = null;
    public $apiDataGuru = null;

    // Properti untuk hitungan
    public $totalLocalData = 0;
    public $totalServerData = 0;

    // Loading state untuk setiap API
    public $isLoadingRombel = true;  // ubah dari false jadi true
    public $isLoadingRombelDetail = true;
    public $isLoadingPesertaDidik = true;
    public $isLoadingGuru = true;

    // Tentukan model-model yang akan dihitung dan labelnya
    protected $dataMappings = [
        'kurikulum' => ['model' => Kurikulum::class, 'label' => 'Data Kurikulum', 'icon' => 'mdi-book-multiple'],
        'jurusan' => ['model' => Jurusan::class, 'label' => 'Data Jurusan', 'icon' => 'mdi-school'],
        'tahun_ajaran' => ['model' => TahunAjaran::class, 'label' => 'Data Tahun Ajaran', 'icon' => 'mdi-calendar-range'],
        'semester' => ['model' => Semester::class, 'label' => 'Data Semester', 'icon' => 'mdi-bookmark-multiple'],
        'rombel' => ['model' => Rombel::class, 'label' => 'Data Rombongan Belajar', 'icon' => 'mdi-account-group'],
        'mata_pelajaran' => ['model' => MataPelajaran::class, 'label' => 'Data Mata Pelajaran', 'icon' => 'mdi-book-open'],
        'pendidik' => ['model' => User::class, 'label' => 'Data Pendidik', 'icon' => 'mdi mdi-human-greeting'],
        'pelajar' => ['model' => Pelajar::class, 'label' => 'Data Pelajar', 'icon' => 'mdi-account-details'],
        'ekstrakurikuler' => ['model' => Ekstrakurikuler::class, 'label' => 'Data Ekstrakurikuler', 'icon' => 'mdi-soccer'],
    ];

    // Event listener
    protected $listeners = [
        'openSyncModal' => 'openSyncModal',
    ];


    public function mount()
    {
        $this->loadDataCounts();
    }

    // Fungsi untuk mengambil hitungan data dan informasi lainnya
    public function loadDataCounts()
    {
        $results = [];
        $totalLocal = 0;

        foreach ($this->dataMappings as $key => $mapping) {
            $modelClass = $mapping['model'];

            // Hitung total record
            $count = $modelClass::count();
            $totalLocal += $count;

            // Ambil record terakhir yang dibuat
            $latestCreated = $modelClass::latest('created_at')->first();

            // Ambil record terakhir yang diperbarui
            $latestUpdated = $modelClass::latest('updated_at')->first();

            $results[] = [
                'key' => $key,
                'label' => $mapping['label'],
                'icon' => $mapping['icon'],
                'count' => $count,
                'latest_created_at' => $latestCreated ? $latestCreated->created_at->format('d F Y') : 'N/A',
                'latest_updated_at' => $latestUpdated ? $latestUpdated->updated_at->diffForHumans() : 'N/A',
                'has_data' => $count > 0,
                'status' => 'Tervalidasi',
                'status_date' => now()->subDays(rand(1, 10))->format('d-m-Y'),
            ];
        }

        $this->masterData = $results;
        $this->totalLocalData = $totalLocal;
    }

    // Method untuk membuka modal tanpa load data API
    public function openSyncModal()
    {
        $this->dispatch('showSyncModal');

        // Load data API secara paralel setelah modal ditampilkan
        // $this->fetchRombel();
        // $this->fetchRombelDetail();
        // $this->fetchPesertaDidik();
        // $this->fetchGuru();
    }

    // Method untuk load semua data API - AKAN DIPANGGIL SATU PER SATU DARI JS
    public function loadApiData()
    {
        $this->fetchRombel();
    }

    // Fungsi untuk mengambil data Rombel
    public function fetchRombel()
    {
        $this->isLoadingRombel = true;
        try {
            $response = Http::timeout(30)->get('http://localhost/pintar/api/rombel');
            if ($response->successful()) {
                $json = $response->json();
                $this->apiDataRombel = isset($json['data']) ? $json['data'] : [];
            } else {
                $this->apiDataRombel = [];
            }
        } catch (\Exception $e) {
            Log::error('Error fetching Rombel data: ' . $e->getMessage());
            $this->apiDataRombel = [];
        }
        $this->isLoadingRombel = false;
        $this->calculateServerData();
        // JANGAN panggil yang lain
    }

    // Fungsi untuk mengambil data Rombel Detail
    public function fetchRombelDetail()
    {
        $this->isLoadingRombelDetail = true;
        try {
            $response = Http::timeout(30)->get('http://localhost/pintar/api/rombel-data');
            if ($response->successful()) {
                $json = $response->json();
                $this->apiDataRombelDetail = isset($json['data']) ? $json['data'] : [];
            } else {
                $this->apiDataRombelDetail = [];
            }
        } catch (\Exception $e) {
            Log::error('Error fetching Rombel Detail data: ' . $e->getMessage());
            $this->apiDataRombelDetail = [];
        }
        $this->isLoadingRombelDetail = false;
        $this->calculateServerData();
        // JANGAN panggil yang lain
    }

    // Fungsi untuk mengambil data Peserta Didik
    public function fetchPesertaDidik()
    {
        $this->isLoadingPesertaDidik = true;
        try {
            $response = Http::timeout(30)->get('http://localhost/pintar/api/data-peserta-didik');
            if ($response->successful()) {
                $json = $response->json();
                $this->apiDataPesertaDidik = isset($json['data']) ? $json['data'] : [];
            } else {
                $this->apiDataPesertaDidik = [];
            }
        } catch (\Exception $e) {
            Log::error('Error fetching Peserta Didik data: ' . $e->getMessage());
            $this->apiDataPesertaDidik = [];
        }
        $this->isLoadingPesertaDidik = false;
        $this->calculateServerData();
        // JANGAN panggil yang lain
    }

    // Fungsi untuk mengambil data Guru
    public function fetchGuru()
    {
        $this->isLoadingGuru = true;
        try {
            $response = Http::timeout(30)->get('http://localhost/simka/api/data-guru');
            if ($response->successful()) {
                $json = $response->json();
                $this->apiDataGuru = isset($json['data']) ? $json['data'] : [];
            } else {
                $this->apiDataGuru = [];
            }
        } catch (\Exception $e) {
            Log::error('Error fetching Guru data: ' . $e->getMessage());
            $this->apiDataGuru = [];
        }
        $this->isLoadingGuru = false;
        $this->calculateServerData();
    }

    // Hitung total data dari server
    private function calculateServerData()
    {
        $total = 0;

        if (is_array($this->apiDataRombel)) {
            $total += count($this->apiDataRombel);
        }

        if (is_array($this->apiDataRombelDetail)) {
            $total += count($this->apiDataRombelDetail);
        }

        if (is_array($this->apiDataPesertaDidik)) {
            $total += count($this->apiDataPesertaDidik);
        }

        if (is_array($this->apiDataGuru)) {
            $total += count($this->apiDataGuru);
        }

        $this->totalServerData = $total;
    }

    public function syncDataToDatabase()
    {
        try {
            DB::beginTransaction();

            // 1. Sync Rombel
            if (is_array($this->apiDataRombel)) {
                foreach ($this->apiDataRombel as $item) {
                    Rombel::updateOrCreate(
                        ['id' => $item['id']], // atau kolom unique lainnya
                        [
                            'nama' => $item['nama'],
                            'tahun_ajaran_id' => $item['tahun_ajaran_id'],
                            // tambahkan field lainnya sesuai struktur tabel
                        ]
                    );
                }
            }

            // 2. Sync Rombel Detail
            if (is_array($this->apiDataRombelDetail)) {
                foreach ($this->apiDataRombelDetail as $item) {
                    // Model untuk RombelDetail jika ada
                    // RombelDetail::updateOrCreate(...);
                }
            }

            // 3. Sync Peserta Didik
            if (is_array($this->apiDataPesertaDidik)) {
                foreach ($this->apiDataPesertaDidik as $item) {
                    Pelajar::updateOrCreate(
                        ['nisn' => $item['nisn']], // atau 'id' => $item['id']
                        [
                            'nama' => $item['nama'],
                            'nis' => $item['nis'],
                            'tempat_lahir' => $item['tempat_lahir'],
                            'tanggal_lahir' => $item['tanggal_lahir'],
                            // tambahkan field lainnya
                        ]
                    );
                }
            }

            // 4. Sync Guru
            if (is_array($this->apiDataGuru)) {
                foreach ($this->apiDataGuru as $item) {
                    User::updateOrCreate(
                        ['nip' => $item['nip']], // atau 'email' => $item['email']
                        [
                            'name' => $item['nama'],
                            'email' => $item['email'],
                            'nip' => $item['nip'],
                            // tambahkan field lainnya
                        ]
                    );
                }
            }

            DB::commit();

            // Reload data counts
            $this->loadDataCounts();

            session()->flash('success', 'Data berhasil disinkronkan ke database!');
            $this->dispatch('closeSyncModal');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error syncing data to database: ' . $e->getMessage());
            session()->flash('error', 'Gagal menyinkronkan data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.master-data');
    }
}
