<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\RoleUser;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar role
        $roles = ['superadmin', 'admin', 'guru', 'walikelas'];

        foreach ($roles as $namaRole) {
            Role::firstOrCreate(
                ['nama_role' => $namaRole],
                ['id' => (string) Str::uuid()]
            );
        }
    }
}
