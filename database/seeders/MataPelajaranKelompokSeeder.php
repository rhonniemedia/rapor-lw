<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MataPelajaranKelompok;

class MataPelajaranKelompokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelompok = [
            ['kode' => 'A', 'nama' => 'Mata Pelajaran Umum'],
            ['kode' => 'B', 'nama' => 'Mata Pelajaran Kejuruan'],
        ];

        foreach ($kelompok as $data) {
            MataPelajaranKelompok::create($data);
        }
    }
}
