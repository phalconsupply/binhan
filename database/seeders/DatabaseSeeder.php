<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Chạy các seeders theo thứ tự: roles → master data → users
        $this->call([
            RoleSeeder::class,        // Tạo 8 roles và 28 permissions
            PositionSeeder::class,     // Tạo positions (lái xe, y tá, bác sĩ, etc.)
            DepartmentSeeder::class,   // Tạo departments
            UserSeeder::class,         // Tạo 4 test users với roles
        ]);
        
        $this->command->info('✓ All seeders completed successfully!');
        $this->command->newLine();
        $this->command->info('Test accounts created:');
        $this->command->info('  Admin: admin@binhan.com / password');
        $this->command->info('  Dispatcher: dispatcher@binhan.com / password');
        $this->command->info('  Accountant: accountant@binhan.com / password');
        $this->command->info('  Driver: driver@binhan.com / password');
    }
}
