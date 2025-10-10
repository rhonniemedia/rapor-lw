<?php

namespace Database\Seeders;

use App\Models\MataPelajaran;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mapels = [
            ['kode' => 'MTK', 'nama' => 'Matematika'],
            ['kode' => 'BIND', 'nama' => 'Bahasa Indonesia'],
            ['kode' => 'BING', 'nama' => 'Bahasa Inggris'],
            ['kode' => 'IPAS', 'nama' => 'Ilmu Pengetahuan Alam Sosial'],
            ['kode' => 'PKN', 'nama' => 'Pendidikan Kewarganegaraan'],
            ['kode' => 'SENI', 'nama' => 'Seni Budaya'],
            ['kode' => 'PJOK', 'nama' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan'],
            ['kode' => 'PABP', 'nama' => 'Pendidikan Agama dan Budi Pekerti'],
        ];

        foreach ($mapels as $mapel) {
            MataPelajaran::create($mapel);
        }
    }
}
