<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class StaffRolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for staff management
        $staffPermissions = [
            'view staff',
            'create staff',
            'edit staff',
            'delete staff',
        ];

        foreach ($staffPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles and their permissions
        $rolesPermissions = [
            'admin' => [
                // Full access to everything including dashboard
                'access dashboard',
                'view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles',
                'view patients', 'create patients', 'edit patients', 'delete patients',
                'view incidents', 'create incidents', 'edit incidents', 'delete incidents',
                'view transactions', 'create transactions', 'edit transactions', 'delete transactions',
                'view notes', 'create notes', 'edit notes', 'delete notes',
                'manage vehicles', 'manage settings',
                'view staff', 'create staff', 'edit staff', 'delete staff',
            ],
            'manager' => [
                // Same as admin
                'access dashboard',
                'view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles',
                'view patients', 'create patients', 'edit patients', 'delete patients',
                'view incidents', 'create incidents', 'edit incidents', 'delete incidents',
                'view transactions', 'create transactions', 'edit transactions', 'delete transactions',
                'view notes', 'create notes', 'edit notes', 'delete notes',
                'manage vehicles', 'manage settings',
                'view staff', 'create staff', 'edit staff', 'delete staff',
            ],
            'medical_staff' => [
                // NO dashboard access - go directly to profile
                // Can only create incidents (quick entry) and view incidents list
                'create incidents',
                'view incidents',
            ],
            'driver' => [
                // NO dashboard access - go directly to profile
                // Can only create incidents (quick entry) and view incidents list
                'create incidents',
                'view incidents',
            ],
            'investor' => [
                // Can view everything including dashboard
                'access dashboard',
                'view vehicles',
                'view patients',
                'view incidents',
                'view transactions',
                'view notes',
                'view staff',
            ],
            'vehicle_owner' => [
                // Vehicle owners can access dashboard and view their own data
                'access dashboard',
                'view vehicles',
                'view patients',
                'view incidents',
                'view transactions',
                'view notes',
                'view reports',
            ],
        ];

        // Create roles and assign permissions
        foreach ($rolesPermissions as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            
            // Get or create all permissions for this role
            $permissionObjects = [];
            foreach ($permissions as $permissionName) {
                $permissionObjects[] = Permission::firstOrCreate(['name' => $permissionName]);
            }
            
            // Sync permissions to role
            $role->syncPermissions($permissionObjects);
        }

        $this->command->info('Staff roles and permissions created successfully!');
    }
}
