<?php

namespace Database\Seeders;

use App\Models\Kurikulum;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KurikulumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kurikulum::create([
            'nama' => 'Kurikulum Merdeka',
            'kode' => 'KM',
            'deskripsi' => 'Kurikulum tahun 2022',
        ]);
    }
}
