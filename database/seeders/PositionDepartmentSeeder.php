<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Position;

class PositionDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Common departments
        $departments = [
            'Hành chính',
            'Nhân sự',
            'Kế toán',
            'Kinh doanh',
            'Vận hành',
            'Y tế',
            'Kỹ thuật',
            'Điều hành',
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['name' => $dept],
                ['is_active' => true]
            );
        }

        // Common positions
        $positions = [
            'Giám đốc',
            'Phó giám đốc',
            'Trưởng phòng',
            'Phó phòng',
            'Nhân viên',
            'Trưởng nhóm',
            'Chuyên viên',
            'Lái xe',
            'Điều dưỡng',
            'Bác sĩ',
            'Y tá',
            'Kỹ thuật viên',
        ];

        foreach ($positions as $pos) {
            Position::firstOrCreate(
                ['name' => $pos],
                ['is_active' => true]
            );
        }

        $this->command->info('✓ Đã tạo ' . count($departments) . ' phòng ban và ' . count($positions) . ' chức vụ');
    }
}
