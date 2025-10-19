<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiSyncService
{
    private array $endpoints = [
        'tahun_ajaran'  => 'http://localhost/pintar/api/tahun-ajaran',
        'jurusan'       => 'http://localhost/pintar/api/jurusan',
        'rombel'        => 'http://localhost/pintar/api/rombel',
        'rombel_detail' => 'http://localhost/pintar/api/rombel-data',
        'peserta_didik' => 'http://localhost/pintar/api/data-peserta-didik',
        'guru'          => 'http://localhost/simka/api/data-guru',
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
        return $this->fetch('guru', 'Guru');
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
}
