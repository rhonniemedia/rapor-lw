<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\RoleUser;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data user default
        $users = [
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('Password123!'),
                'status' => 'aktif',
                'role' => 'superadmin',
            ],
            [
                'name' => 'Admin Satu',
                'slug' => 'admin-satu',
                'email' => 'admin@example.com',
                'password' => Hash::make('Password123!'),
                'status' => 'aktif',
                'role' => 'admin',
            ],
            [
                'name' => 'Guru Satu',
                'slug' => 'guru-satu',
                'email' => 'guru@example.com',
                'password' => Hash::make('Password123!'),
                'status' => 'aktif',
                'role' => 'guru',
            ],
            [
                'name' => 'Wali Kelas',
                'slug' => 'wali-kelas',
                'email' => 'walikelas@example.com',
                'password' => Hash::make('Password123!'),
                'status' => 'aktif',
                'role' => 'walikelas',
            ],
        ];

        foreach ($users as $data) {
            // Buat user
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'id' => (string) Str::uuid(),
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'password' => $data['password'],
                    'status' => $data['status'],
                ]
            );

            // Ambil role yang sudah dibuat di RoleSeeder
            $role = Role::where('nama_role', $data['role'])->first();

            if ($role) {
                RoleUser::firstOrCreate([
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                ]);
            }
        }
    }
}
