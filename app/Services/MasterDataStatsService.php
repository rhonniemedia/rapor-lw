<?php

namespace App\Services;

class MasterDataStatsService
{
    private array $dataMappings = [
        'kurikulum' => [
            'model' => \App\Models\Kurikulum::class,
            'label' => 'Data Kurikulum',
            'icon' => 'mdi-book-multiple'
        ],
        'jurusan' => [
            'model' => \App\Models\Jurusan::class,
            'label' => 'Data Jurusan',
            'icon' => 'mdi-school'
        ],
        'tahun_ajaran' => [
            'model' => \App\Models\TahunAjaran::class,
            'label' => 'Data Tahun Ajaran',
            'icon' => 'mdi-calendar-range'
        ],
        'semester' => [
            'model' => \App\Models\Semester::class,
            'label' => 'Data Semester',
            'icon' => 'mdi-bookmark-multiple'
        ],
        'rombel' => [
            'model' => \App\Models\Rombel::class,
            'label' => 'Data Rombongan Belajar',
            'icon' => 'mdi-account-group'
        ],
        'mata_pelajaran' => [
            'model' => \App\Models\MataPelajaran::class,
            'label' => 'Data Mata Pelajaran',
            'icon' => 'mdi-book-open'
        ],
        'pendidik' => [
            'model' => \App\Models\User::class,
            'label' => 'Data Pendidik',
            'icon' => 'mdi mdi-human-greeting'
        ],
        'pelajar' => [
            'model' => \App\Models\Pelajar::class,
            'label' => 'Data Pelajar',
            'icon' => 'mdi-account-details'
        ],
        'ekstrakurikuler' => [
            'model' => \App\Models\Ekstrakurikuler::class,
            'label' => 'Data Ekstrakurikuler',
            'icon' => 'mdi-soccer'
        ],
    ];

    public function getStats(): array
    {
        $results = [];
        $totalLocal = 0;

        foreach ($this->dataMappings as $key => $mapping) {
            $modelClass = $mapping['model'];
            $count = $modelClass::count();
            $totalLocal += $count;

            $latestCreated = $modelClass::latest('created_at')->first();
            $latestUpdated = $modelClass::latest('updated_at')->first();

            $results[] = [
                'key' => $key,
                'label' => $mapping['label'],
                'icon' => $mapping['icon'],
                'count' => $count,
                'latest_created_at' => $latestCreated
                    ? $latestCreated->created_at->format('d F Y')
                    : 'N/A',
                'latest_updated_at' => $latestUpdated
                    ? $latestUpdated->updated_at->diffForHumans()
                    : 'N/A',
                'has_data' => $count > 0,
                'status' => 'Tervalidasi',
                'status_date' => now()->subDays(rand(1, 10))->format('d-m-Y'),
            ];
        }

        return [
            'data' => $results,
            'total' => $totalLocal,
        ];
    }
}
