<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ApiSyncService;
use App\Services\DataSyncService;
use App\Services\MasterDataStatsService;

class MasterData extends Component
{
    // Services
    private ApiSyncService $apiService;
    private DataSyncService $syncService;
    private MasterDataStatsService $statsService;

    // Properti untuk menyimpan data
    public $masterData = [];

    // Properti untuk data API
    public $apiTahunAjaran = null;
    public $apiDataJurusan = null;
    public $apiDataRombel = null;
    public $apiDataRombelDetail = null;
    public $apiDataPesertaDidik = null;
    public $apiDataGuru = null;

    // Properti untuk hitungan
    public $totalLocalData = 0;
    public $totalServerData = 0;

    // Loading states
    public $isLoadingTahunAjaran = true;
    public $isLoadingJurusan = true;
    public $isLoadingRombel = true;
    public $isLoadingRombelDetail = true;
    public $isLoadingPesertaDidik = true;
    public $isLoadingGuru = true;

    // Event listener
    protected $listeners = [
        'openSyncModal' => 'openSyncModal',
    ];

    public function boot(
        ApiSyncService $apiService,
        DataSyncService $syncService,
        MasterDataStatsService $statsService
    ) {
        $this->apiService = $apiService;
        $this->syncService = $syncService;
        $this->statsService = $statsService;
    }

    public function mount()
    {
        $this->loadDataCounts();
    }

    public function loadDataCounts()
    {
        $stats = $this->statsService->getStats();
        $this->masterData = $stats['data'];
        $this->totalLocalData = $stats['total'];
    }

    public function openSyncModal()
    {
        $this->dispatch('showSyncModal');
    }

    public function loadApiData()
    {
        $this->fetchTahunAjaran();
    }

    public function fetchTahunAjaran()
    {
        $this->isLoadingTahunAjaran = true;
        $this->apiTahunAjaran = $this->apiService->fetchTahunAjaran();
        $this->isLoadingTahunAjaran = false;
        $this->calculateServerData();
    }

    public function fetchJurusan()
    {
        $this->isLoadingJurusan = true;
        $this->apiDataJurusan = $this->apiService->fetchJurusan();
        $this->isLoadingJurusan = false;
        $this->calculateServerData();
    }

    public function fetchRombel()
    {
        $this->isLoadingRombel = true;
        $this->apiDataRombel = $this->apiService->fetchRombel();
        $this->isLoadingRombel = false;
        $this->calculateServerData();
    }

    public function fetchRombelDetail()
    {
        $this->isLoadingRombelDetail = true;
        $this->apiDataRombelDetail = $this->apiService->fetchRombelDetail();
        $this->isLoadingRombelDetail = false;
        $this->calculateServerData();
    }

    public function fetchPesertaDidik()
    {
        $this->isLoadingPesertaDidik = true;
        $this->apiDataPesertaDidik = $this->apiService->fetchPesertaDidik();
        $this->isLoadingPesertaDidik = false;
        $this->calculateServerData();
    }

    public function fetchGuru()
    {
        $this->isLoadingGuru = true;
        $this->apiDataGuru = $this->apiService->fetchGuru();
        $this->isLoadingGuru = false;
        $this->calculateServerData();
    }

    private function calculateServerData()
    {
        $this->totalServerData = $this->apiService->calculateTotal([
            $this->apiTahunAjaran,
            $this->apiDataJurusan,
            $this->apiDataRombel,
            $this->apiDataRombelDetail,
            $this->apiDataPesertaDidik,
            $this->apiDataGuru,
        ]);
    }

    public function syncDataToDatabase()
    {
        try {
            $this->syncService->syncAll([
                'tahun_ajaran' => $this->apiTahunAjaran,
                'jurusan' => $this->apiDataJurusan,
                'rombel' => $this->apiDataRombel,
                'rombel_detail' => $this->apiDataRombelDetail,
                'peserta_didik' => $this->apiDataPesertaDidik,
                'guru' => $this->apiDataGuru,
            ]);

            $this->loadDataCounts();
            session()->flash('success', 'Data berhasil disinkronkan ke database!');
            $this->dispatch('closeSyncModal');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyinkronkan data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.master-data');
    }
}
