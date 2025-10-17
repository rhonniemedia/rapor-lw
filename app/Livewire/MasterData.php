<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kurikulum;
use App\Models\Jurusan;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Rombel;
use App\Models\MataPelajaran;
use App\Models\Ekstrakurikuler;
use App\Models\Pelajar;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MasterData extends Component
{
    // Properti untuk menyimpan data hitungan
    public $masterData = [];

    // Properti untuk data API
    public $apiDataRombel = [];
    public $apiDataRombelDetail = [];
    public $apiDataPesertaDidik = [];
    public $apiDataGuru = [];

    // Properti untuk hitungan
    public $totalLocalData = 0;
    public $totalServerData = 0;

    // Loading state
    public $isLoadingApi = false;

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

    // Method untuk membuka modal dan load data API
    public function openSyncModal()
    {
        $this->isLoadingApi = true;
        $this->dispatch('showSyncModal');

        // Load data dari API
        $this->fetchApiData();

        $this->isLoadingApi = false;
    }

    // Fungsi untuk mengambil data dari API
    public function fetchApiData()
    {
        try {
            // Fetch Rombel
            $responseRombel = Http::timeout(10)->get('http://128.16.0.8/pintar/api/rombel');
            if ($responseRombel->successful()) {
                $data = $responseRombel->json();
                $this->apiDataRombel = is_array($data) ? $data : [];
            } else {
                $this->apiDataRombel = [];
            }

            // Fetch Rombel Detail
            $responseRombelDetail = Http::timeout(10)->get('http://128.16.0.8/pintar/api/rombel-data');
            if ($responseRombelDetail->successful()) {
                $data = $responseRombelDetail->json();
                $this->apiDataRombelDetail = is_array($data) ? $data : [];
            } else {
                $this->apiDataRombelDetail = [];
            }

            // Fetch Peserta Didik
            $responsePesertaDidik = Http::timeout(10)->get('http://128.16.0.8/pintar/api/data-peserta-didik');
            if ($responsePesertaDidik->successful()) {
                $data = $responsePesertaDidik->json();
                $this->apiDataPesertaDidik = is_array($data) ? $data : [];
            } else {
                $this->apiDataPesertaDidik = [];
            }

            // Fetch Guru
            $responseGuru = Http::timeout(10)->get('http://128.16.0.8/simka/api/data-guru');
            if ($responseGuru->successful()) {
                $data = $responseGuru->json();
                $this->apiDataGuru = is_array($data) ? $data : [];
            } else {
                $this->apiDataGuru = [];
            }

            // Hitung total data server
            $this->calculateServerData();
        } catch (\Exception $e) {
            Log::error('Error fetching API data: ' . $e->getMessage());
            $this->apiDataRombel = [];
            $this->apiDataRombelDetail = [];
            $this->apiDataPesertaDidik = [];
            $this->apiDataGuru = [];
            session()->flash('error', 'Gagal mengambil data dari server: ' . $e->getMessage());
        }
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

    public function render()
    {
        return view('livewire.master-data');
    }
}
