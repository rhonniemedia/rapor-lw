<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sekolah::create([
            'nama_sekolah'   => 'SMK Negeri 1 Rejang Lebong',
            'npsn'           => '10700610',
            'nss'            => null,
            'alamat'         => 'Jl. Ahmad Marzuki No. 105',
            'kelurahan'      => 'Air Rambai',
            'kecamatan'      => 'Curup',
            'kota'           => 'Rejang Lebong',
            'provinsi'       => 'Bengkulu',
            'kode_pos'       => '39111',
            'telepon'        => '073221258',
            'email'          => 'mail@smkn1rl.sch.id',
            'website'        => 'https://smkn1rl.sch.id/',
            'status_sekolah' => 'Negeri',
            'tgl_berdiri'    => null,
            'akreditasi'     => null,
        ]);
    }
}
