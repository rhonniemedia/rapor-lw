<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiSyncService
{
    private array $endpoints = [
        'tahun_ajaran'  => 'http://128.16.0.8/pintar/api/tahun-ajaran',
        'jurusan'       => 'http://128.16.0.8/pintar/api/jurusan',
        'rombel'        => 'http://128.16.0.8/pintar/api/rombel',
        'rombel_detail' => 'http://128.16.0.8/pintar/api/rombel-data',
        'peserta_didik' => 'http://128.16.0.8/pintar/api/data-peserta-didik',
        'guru'          => 'http://128.16.0.8/simka/api/data-guru',
    ];

    public function fetchTahunAjaran(): array
    {
        return $this->fetch('tahun_ajaran', 'Tahun Ajaran');
    }

    public function fetchJurusan(): array
    {
        return $this->fetch('jurusan', 'Jurusan');
    }

    public function fetchRombel(): array
    {
        return $this->fetch('rombel', 'Rombel');
    }

    public function fetchRombelDetail(): array
    {
        return $this->fetch('rombel_detail', 'Rombel Detail');
    }

    public function fetchPesertaDidik(): array
    {
        return $this->fetch('peserta_didik', 'Peserta Didik');
    }

    public function fetchGuru(): array
    {
        $data = $this->fetch('guru', 'Guru');

        $filtered = collect($data)
            ->filter(
                fn($item) =>
                isset($item['jptk_id']) &&
                    ((int)$item['jptk_id'] === 1)
            )
            ->values()
            ->all();

        // Ringkasan singkat (tidak verbose)
        Log::info('Guru fetched', [
            'total' => count($data),
            'valid' => count($filtered),
        ]);

        return $filtered;
    }

    private function fetch(string $key, string $name): array
    {
        try {
            $response = Http::timeout(30)->get($this->endpoints[$key]);

            if ($response->successful()) {
                $json = $response->json();
                return $json['data'] ?? [];
            }

            Log::warning("Failed to fetch {$name} data: " . $response->status());
            return [];
        } catch (\Exception $e) {
            Log::error("Error fetching {$name} data: " . $e->getMessage());
            return [];
        }
    }

    public function calculateTotal(array $datasets): int
    {
        return array_reduce($datasets, function ($carry, $dataset) {
            return $carry + (is_array($dataset) ? count($dataset) : 0);
        }, 0);
    }

    /**
     * Get all wali kelas slugs from rombel data
     */
    public function getWaliKelasSlugs(): array
    {
        try {
            $rombelData = $this->fetchRombel();

            if (empty($rombelData)) {
                Log::warning('No rombel data available for wali kelas');
                return [];
            }

            // Extract wali_kelas slugs
            $waliKelasSlugs = collect($rombelData)
                ->pluck('wali_kelas')
                ->filter() // Remove null/empty values
                ->unique()
                ->values()
                ->toArray();

            Log::info('Wali kelas slugs extracted', [
                'total_rombel' => count($rombelData),
                'total_wali_kelas' => count($waliKelasSlugs)
            ]);

            return $waliKelasSlugs;
        } catch (\Exception $e) {
            Log::error('Error extracting wali kelas slugs', [
                'message' => $e->getMessage()
            ]);
            return [];
        }
    }
}
