<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jurusans = [
            [
                'id' => '9d400210-5076-4931-a5fd-f6b9e8d6adea',
                'nama' => 'Desain Pemodelan dan Informasi Bangunan',
                'alias' => 'DPIB',
                'kode' => '01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '9d400221-1410-4f1b-aecb-ddd5f67f02a3',
                'nama' => 'Teknik Elektronika Industri',
                'alias' => 'TEI',
                'kode' => '02',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '9d40022c-f9ca-462c-bb56-7deacfde9de7',
                'nama' => 'Teknik Komputer dan Jaringan',
                'alias' => 'TKJ',
                'kode' => '03',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '9d5a8d57-41bb-45ce-af49-37b145025d1c',
                'nama' => 'Teknik Instalasi Tenaga Listrik',
                'alias' => 'TITL',
                'kode' => '05',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '9d5a8d9d-a49a-4434-8aed-b0f977b6f6e6',
                'nama' => 'Teknik Pembangkit Tenaga Listrik',
                'alias' => 'TPTL',
                'kode' => '04',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '9d5a8db7-59e4-4d75-9129-5a23b3b052f4',
                'nama' => 'Teknik Pemesinan',
                'alias' => 'TM',
                'kode' => '06',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '9d5a8dcf-0f44-43fc-b673-c8ee1fd68d29',
                'nama' => 'Teknik Pengelasan',
                'alias' => 'TL',
                'kode' => '07',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '9d5a8de5-5848-4031-bdc8-51662a120714',
                'nama' => 'Teknik Sepeda Motor',
                'alias' => 'TSM',
                'kode' => '09',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '9d5a8dfa-a285-49ab-bb7f-b61fd74a6e90',
                'nama' => 'Teknik Kendaraan Ringan',
                'alias' => 'TKR',
                'kode' => '08',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => '9d7a0339-07bb-465a-bd14-f9c765a624b5',
                'nama' => 'Teknik Konstruksi dan Perumahan',
                'alias' => 'TKP',
                'kode' => '10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($jurusans as $jurusan) {
            Jurusan::create($jurusan);
        }
    }
}
