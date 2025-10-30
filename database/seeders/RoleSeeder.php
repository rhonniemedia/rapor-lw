<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat roles
        $roles = [
            'superadmin',
            'admin',
            'walikelas',
            'guru',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'web']
            );
        }

        // Contoh membuat permissions (optional)
        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['guard_name' => 'web']
            );
        }

        // Assign permissions ke role superadmin (optional)
        $superadmin = Role::where('name', 'superadmin')->first();
        if ($superadmin) {
            $superadmin->givePermissionTo(Permission::all());
        }
    }
}
