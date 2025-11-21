<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@binhan.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Dispatcher user
        $dispatcher = User::create([
            'name' => 'Điều phối viên',
            'email' => 'dispatcher@binhan.com',
            'password' => Hash::make('password'),
        ]);
        $dispatcher->assignRole('dispatcher');

        // Accountant user
        $accountant = User::create([
            'name' => 'Kế toán',
            'email' => 'accountant@binhan.com',
            'password' => Hash::make('password'),
        ]);
        $accountant->assignRole('accountant');

        // Driver user
        $driver = User::create([
            'name' => 'Tài xế',
            'email' => 'driver@binhan.com',
            'password' => Hash::make('password'),
        ]);
        $driver->assignRole('driver');

        $this->command->info('Test users created!');
        $this->command->info('Login credentials:');
        $this->command->info('Admin: admin@binhan.com / password');
        $this->command->info('Dispatcher: dispatcher@binhan.com / password');
        $this->command->info('Accountant: accountant@binhan.com / password');
        $this->command->info('Driver: driver@binhan.com / password');
    }
}
