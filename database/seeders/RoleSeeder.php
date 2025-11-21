<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view vehicles',
            'create vehicles',
            'edit vehicles',
            'delete vehicles',
            
            'view incidents',
            'create incidents',
            'edit incidents',
            'delete incidents',
            
            'view transactions',
            'create transactions',
            'edit transactions',
            'delete transactions',
            
            'view reports',
            'export reports',
            
            'view audits',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin - full access
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // Dispatcher - create/edit incidents & view reports
        $dispatcher = Role::create(['name' => 'dispatcher']);
        $dispatcher->givePermissionTo([
            'view vehicles',
            'view incidents',
            'create incidents',
            'edit incidents',
            'view transactions',
            'create transactions',
            'view reports',
        ]);

        // Accountant - manage transactions & export reports
        $accountant = Role::create(['name' => 'accountant']);
        $accountant->givePermissionTo([
            'view vehicles',
            'view incidents',
            'view transactions',
            'create transactions',
            'edit transactions',
            'view reports',
            'export reports',
        ]);

        // Driver - view only own vehicle
        $driver = Role::create(['name' => 'driver']);
        $driver->givePermissionTo([
            'view vehicles',
            'view incidents',
            'view transactions',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}
