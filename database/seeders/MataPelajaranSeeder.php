<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
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
        $mapelsRingkas = [
            ['kode' => 'BIND', 'nama' => 'Bahasa Indonesia'],
            ['kode' => 'BING', 'nama' => 'Bahasa Inggris'],
            ['kode' => 'IPAS', 'nama' => 'Ilmu Pengetahuan Alam Sosial'],
            ['kode' => 'PPKN', 'nama' => 'Pendidikan Pancasila'],
            ['kode' => 'SENI', 'nama' => 'Seni Budaya'],
            ['kode' => 'PJOK', 'nama' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan'],
            ['kode' => 'PABP', 'nama' => 'Pendidikan Agama dan Budi Pekerti'], // Entri yang perlu diperluas
        ];

        $agamaList = [
            'islam',
            'kristen',
            'katolik',
            'hindu',
            'buddha',
            'khonghucu'
        ];

        foreach ($mapelsRingkas as $mapel) {

            // Logika untuk Perluasan Mata Pelajaran Agama
            if ($mapel['kode'] === 'PABP') {

                foreach ($agamaList as $agama) {
                    $kodeAgama = strtoupper(substr($agama, 0, 2));

                    // Mengkapitalisasi huruf pertama untuk tampilan di kolom 'nama'
                    $agamaKapital = Str::ucfirst($agama);

                    MataPelajaran::create([
                        // ID otomatis dari HasUuids
                        'nama' => "Pendidikan Agama {$agamaKapital} dan Budi Pekerti", // Menggunakan $agamaKapital
                        'kode' => "PA{$kodeAgama}",
                        'is_mapel_agama' => true,
                        'agama_terkait' => $agama, // Tersimpan lowercase ('islam') untuk hashing yang konsisten
                        'status' => 'aktif',
                    ]);
                }
            } else {
                // Logika untuk Mata Pelajaran Umum lainnya
                MataPelajaran::create([
                    // ID otomatis dari HasUuids
                    'nama' => $mapel['nama'],
                    'kode' => $mapel['kode'],
                    'is_mapel_agama' => false,
                    'agama_terkait' => null,
                    'status' => 'aktif',
                ]);
            }
        }
    }
}
