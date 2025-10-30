<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan role superadmin sudah ada
        $role = Role::firstOrCreate(
            ['name' => 'superadmin'],
            ['guard_name' => 'web']
        );

        // Buat user superadmin
        $user = User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'password' => Hash::make('Password123!'),
                'status' => 'aktif',
            ]
        );

        // Assign role superadmin (syncRoles akan menghapus role lama jika ada)
        $user->syncRoles(['superadmin']);

        // Contoh user lain (optional)
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'password' => Hash::make('Password123!'),
                'status' => 'aktif',
            ]
        );
        $admin->syncRoles(['admin']);
    }
}
