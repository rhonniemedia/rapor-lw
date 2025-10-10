<?php

namespace Database\Seeders;

use App\Models\TahunAjaran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TahunAjaran::create([
            'id' => '9d3ffceb-deb7-4f7e-ba1b-08b555daca35',
            'nama' => '2024/2025',
            'tgl_mulai' => '2024-07-01',
            'tgl_selesai' => '2025-06-30',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
