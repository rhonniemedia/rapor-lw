<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Semester Ganjil
        Semester::create([
            'nama' => 'Ganjil',
            'urutan' => 1,
        ]);

        // Semester Genap
        Semester::create([
            'nama' => 'Genap',
            'urutan' => 2,
        ]);
    }
}
