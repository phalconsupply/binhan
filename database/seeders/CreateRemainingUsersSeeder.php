<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateRemainingUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Điều phối viên',
                'email' => 'dispatcher@binhan.com',
                'role' => 'dispatcher'
            ],
            [
                'name' => 'Kế toán',
                'email' => 'accountant@binhan.com',
                'role' => 'accountant'
            ],
            [
                'name' => 'Tài xế',
                'email' => 'driver@binhan.com',
                'role' => 'driver'
            ],
        ];

        foreach ($users as $userData) {
            if (!User::where('email', $userData['email'])->exists()) {
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password'),
                ]);
                $user->assignRole($userData['role']);
                $this->command->info('Created: ' . $userData['email']);
            }
        }
        
        // Assign role to existing admin user
        $admin = User::where('email', 'admin@binhan.com')->first();
        if ($admin && !$admin->hasRole('admin')) {
            $admin->assignRole('admin');
            $this->command->info('Assigned admin role to admin@binhan.com');
        }
    }
}
