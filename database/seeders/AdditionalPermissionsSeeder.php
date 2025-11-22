<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdditionalPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create new permissions
        $newPermissions = [
            'manage vehicles',
            'view patients',
            'create patients',
            'edit patients',
            'delete patients',
            'manage settings',
        ];

        foreach ($newPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Give admin all permissions
        $admin = Role::findByName('admin');
        $admin->givePermissionTo(Permission::all());

        // Give dispatcher additional permissions
        $dispatcher = Role::findByName('dispatcher');
        $dispatcher->givePermissionTo([
            'view patients',
            'create patients',
            'edit patients',
            'manage settings',
        ]);

        // Give accountant additional permissions
        $accountant = Role::findByName('accountant');
        $accountant->givePermissionTo([
            'view patients',
            'manage settings',
        ]);

        $this->command->info('Additional permissions created and assigned successfully!');
    }
}

