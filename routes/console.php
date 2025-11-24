<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('fix:all-roles', function () {
    $users = [
        1 => 'admin',
        3 => 'dispatcher',
        4 => 'accountant',
        5 => 'driver',
    ];
    
    foreach ($users as $userId => $roleName) {
        $user = \App\Models\User::find($userId);
        
        if ($user) {
            $user->syncRoles([]);
            $user->assignRole($roleName);
            $this->info("✓ {$user->email} → {$roleName} ({$user->getAllPermissions()->count()} permissions)");
        } else {
            $this->warn("✗ User ID {$userId} not found");
        }
    }
    
    // Clear cache
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    $this->info("\n✓ All roles fixed and cache cleared!");
    
})->purpose('Fix all user role assignments');
