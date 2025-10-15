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

class MasterData extends Component
{
    // Properti untuk menyimpan data hitungan
    public $masterData = [];

    // Tentukan model-model yang akan dihitung dan labelnya
    protected $dataMappings = [
        'kurikulum' => ['model' => Kurikulum::class, 'label' => 'Data Kurikulum', 'icon' => 'mdi-book-multiple'],
        'jurusan' => ['model' => Jurusan::class, 'label' => 'Data Jurusan', 'icon' => 'mdi-school'],
        'tahun_ajaran' => ['model' => TahunAjaran::class, 'label' => 'Data Tahun Ajaran', 'icon' => 'mdi-calendar-range'],
        'semester' => ['model' => Semester::class, 'label' => 'Data Semester', 'icon' => 'mdi-bookmark-multiple'],
        'rombel' => ['model' => Rombel::class, 'label' => 'Data Rombongan Belajar', 'icon' => 'mdi-account-group'],
        'mata_pelajaran' => ['model' => MataPelajaran::class, 'label' => 'Data Mata Pelajaran', 'icon' => 'mdi-book-open'],
        // Data 'pelajar' ditambahkan di sini
        'pendidik' => ['model' => User::class, 'label' => 'Data Pendidik', 'icon' => 'mdi mdi-human-greeting'],
        'pelajar' => ['model' => Pelajar::class, 'label' => 'Data Pelajar', 'icon' => 'mdi-account-details'],
        'ekstrakurikuler' => ['model' => Ekstrakurikuler::class, 'label' => 'Data Ekstrakurikuler', 'icon' => 'mdi-soccer'],
    ];

    // 🔹 Event listener
    protected $listeners = [
        'openSyncModal' => 'openSyncModal', // ✅ Event listener yang benar
    ];


    public function mount()
    {
        $this->loadDataCounts();
    }

    // Fungsi untuk mengambil hitungan data dan informasi lainnya
    public function loadDataCounts()
    {
        $results = [];

        foreach ($this->dataMappings as $key => $mapping) {
            $modelClass = $mapping['model'];

            // Hitung total record
            $count = $modelClass::count();

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
                // Status dan Tanggal Validasi/Sinkronisasi di-mock
                'status' => 'Tervalidasi',
                'status_date' => now()->subDays(rand(1, 10))->format('d-m-Y'),
            ];
        }

        $this->masterData = $results;
    }

    // ✅ Method untuk membuka modal
    public function openSyncModal()
    {
        $this->dispatch('showSyncModal');
    }

    public function render()
    {
        return view('livewire.master-data');
    }
}
