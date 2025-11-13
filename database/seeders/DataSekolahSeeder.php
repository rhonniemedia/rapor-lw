<?php

namespace Database\Seeders;

use App\Models\DataSekolah;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataSekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan tabel kosong sebelum diisi (opsional, tapi disarankan)
        DataSekolah::truncate();

        // Menggunakan metode create() Model.
        // ID (UUID) akan otomatis dibuat oleh metode boot() di Model.
        DataSekolah::create([
            'nama_sekolah' => 'SMK Negeri 1 Rejang Lebong',
            'npsn' => '10700610',
            'nis' => null,
            'nss' => null,
            'nds' => null,
            'alamat' => 'Jl. Ahmad Marzuki 105',
            'kode_pos' => '39111',
            'kelurahan' => 'Air Rambai',
            'kecamatan' => 'Curup',
            'kota_kabupaten' => 'Rejang Lebong',
            'provinsi' => 'Bengkulu',
            'telepon' => '073221258',
            'website' => 'https://smkn1rl.sch.id/',
            'email' => 'mail@smkn1rl.sch.id',
            'logo_sekolah_path' => null,
            'logo_pemda_path' => null,
        ]);
    }
}
