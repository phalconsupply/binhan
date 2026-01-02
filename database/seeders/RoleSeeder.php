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
            'manage vehicles',
            
            'view incidents',
            'create incidents',
            'edit incidents',
            'delete incidents',
            
            'view transactions',
            'create transactions',
            'edit transactions',
            'delete transactions',
            'manage transactions',
            
            'view patients',
            'create patients',
            'edit patients',
            'delete patients',
            
            'view reports',
            'export reports',
            
            'view audits',
            'manage users',
            'manage settings',
            
            // Search permissions
            'search vehicles',
            'search incidents',
            'search transactions',
            'search patients',
            'search notes',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        
        // Admin - full access
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // Dispatcher - create/edit incidents & view reports
        $dispatcher = Role::firstOrCreate(['name' => 'dispatcher']);
        $dispatcher->syncPermissions([
            'view vehicles',
            'view incidents',
            'create incidents',
            'edit incidents',
            'view transactions',
            'create transactions',
            'view patients',
            'create patients',
            'edit patients',
            'view reports',
            'manage settings',
            'search vehicles',
            'search incidents',
            'search patients',
            'search transactions',
            'search notes',
        ]);

        // Accountant - manage transactions & export reports
        $accountant = Role::firstOrCreate(['name' => 'accountant']);
        $accountant->syncPermissions([
            'view vehicles',
            'view incidents',
            'view transactions',
            'create transactions',
            'edit transactions',
            'view patients',
            'view reports',
            'export reports',
            'manage settings',
            'search vehicles',
            'search incidents',
            'search transactions',
            'search patients',
        ]);

        // Driver - view only own vehicle and create incidents
        $driver = Role::firstOrCreate(['name' => 'driver']);
        $driver->syncPermissions([
            'view vehicles',
            'view incidents',
            'create incidents',
            'view transactions',
            'search vehicles',
            'search incidents',
        ]);

        // Medical Staff - similar to driver
        $medicalStaff = Role::firstOrCreate(['name' => 'medical_staff']);
        $medicalStaff->syncPermissions([
            'view vehicles',
            'view incidents',
            'view transactions',
            'view patients',
            'search vehicles',
            'search incidents',
            'search patients',
        ]);

        // Manager - broader access
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions([
            'view vehicles',
            'view incidents',
            'view transactions',
            'manage transactions',
            'view patients',
            'view reports',
            'export reports',
            'manage settings',
            'search vehicles',
            'search incidents',
            'search transactions',
            'search patients',
            'search notes',
        ]);

        // Investor - view reports only
        $investor = Role::firstOrCreate(['name' => 'investor']);
        $investor->syncPermissions([
            'view reports',
        ]);

        // Vehicle Owner - view vehicle and earnings
        $vehicleOwner = Role::firstOrCreate(['name' => 'vehicle_owner']);
        $vehicleOwner->syncPermissions([
            'view vehicles',
            'view incidents',
            'view transactions',
            'view reports',
            'search vehicles',
            'search incidents',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}
