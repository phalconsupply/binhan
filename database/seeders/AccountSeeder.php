<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;
use App\Models\Vehicle;
use App\Models\Staff;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. System Accounts
        Account::create([
            'code' => 'SYS-CUSTOMER',
            'name' => 'Khách hàng (Nguồn thu)',
            'type' => 'revenue',
            'category' => 'customer',
            'system_account' => true,
            'description' => 'Tài khoản hệ thống cho nguồn thu từ khách hàng',
        ]);

        Account::create([
            'code' => 'SYS-INCOME',
            'name' => 'Thu nhập (Lợi nhuận)',
            'type' => 'revenue',
            'category' => 'income',
            'system_account' => true,
            'description' => 'Tài khoản hệ thống cho thu nhập chung',
        ]);

        Account::create([
            'code' => 'SYS-EXTERNAL',
            'name' => 'Bên ngoài',
            'type' => 'expense',
            'category' => 'external',
            'system_account' => true,
            'description' => 'Tài khoản hệ thống cho chi tiêu bên ngoài',
        ]);

        Account::create([
            'code' => 'SYS-PARTNER',
            'name' => 'Đối tác',
            'type' => 'expense',
            'category' => 'partner',
            'system_account' => true,
            'description' => 'Tài khoản hệ thống cho thanh toán đối tác',
        ]);

        // 2. Company Accounts
        $companyFund = Account::create([
            'code' => 'COMP-FUND',
            'name' => 'Quỹ công ty',
            'type' => 'asset',
            'category' => 'company_fund',
            'system_account' => true,
            'description' => 'Quỹ chính của công ty',
        ]);

        Account::create([
            'code' => 'COMP-RESERVED',
            'name' => 'Quỹ dự kiến chi',
            'type' => 'asset',
            'category' => 'company_reserved',
            'parent_id' => $companyFund->id,
            'system_account' => true,
            'description' => 'Quỹ dự kiến chi của công ty (đã trừ khỏi lợi nhuận)',
        ]);

        // 3. Vehicle Accounts
        $vehicles = Vehicle::all();
        foreach ($vehicles as $vehicle) {
            Account::create([
                'code' => "VEH-{$vehicle->id}",
                'name' => "Tài khoản xe {$vehicle->license_plate}",
                'type' => 'asset',
                'category' => 'vehicle',
                'reference_type' => Vehicle::class,
                'reference_id' => $vehicle->id,
                'description' => "Tài khoản hoạt động của xe {$vehicle->license_plate}",
            ]);
        }

        // 4. Staff Accounts
        $staffs = Staff::all();
        foreach ($staffs as $staff) {
            Account::create([
                'code' => "STAFF-{$staff->id}",
                'name' => "Tài khoản {$staff->full_name}",
                'type' => 'expense',
                'category' => 'staff',
                'reference_type' => Staff::class,
                'reference_id' => $staff->id,
                'description' => "Tài khoản nhân viên {$staff->full_name}",
            ]);
        }

        $this->command->info('✅ Created ' . Account::count() . ' accounts successfully!');
    }
}
