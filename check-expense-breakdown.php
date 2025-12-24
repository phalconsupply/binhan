<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Vehicle;

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

if (!$vehicle) {
    echo "Không tìm thấy xe 49B08879\n";
    exit;
}

echo "=== PHÂN TÍCH CHI TIẾT CHO XE 49B08879 ===\n\n";

// Chi (expense transactions)
$expenses = $vehicle->transactions()->expense()->orderBy('date')->get();
$totalExpense = 0;

echo "1. CÁC KHOẢN CHI (type = 'chi'):\n";
echo str_repeat("-", 80) . "\n";
printf("%-12s %-12s %-35s %15s\n", "Mã GD", "Ngày", "Ghi chú", "Số tiền");
echo str_repeat("-", 80) . "\n";

foreach ($expenses as $expense) {
    printf("%-12s %-12s %-35s %15s\n", 
        $expense->code,
        $expense->date,
        substr($expense->note ?? '', 0, 35),
        number_format($expense->amount)
    );
    $totalExpense += $expense->amount;
}

echo str_repeat("-", 80) . "\n";
printf("TỔNG CHI: %s đ\n\n", number_format($totalExpense));

// Tra no (return to company transactions)
$returns = $vehicle->transactions()->returnToCompany()->orderBy('date')->get();
$totalReturned = 0;

echo "2. CÁC KHOẢN TRẢ NỢ (type = 'tra_cong_ty'):\n";
echo str_repeat("-", 80) . "\n";
printf("%-12s %-12s %-35s %15s\n", "Mã GD", "Ngày", "Ghi chú", "Số tiền");
echo str_repeat("-", 80) . "\n";

foreach ($returns as $return) {
    printf("%-12s %-12s %-35s %15s\n", 
        $return->code,
        $return->date,
        substr($return->note ?? '', 0, 35),
        number_format($return->amount)
    );
    $totalReturned += $return->amount;
}

echo str_repeat("-", 80) . "\n";
printf("TỔNG TRẢ NỢ: %s đ\n\n", number_format($totalReturned));

echo "=== TỔNG KẾT ===\n";
echo "Tổng chi (chi): " . number_format($totalExpense) . " đ\n";
echo "Tổng trả nợ (tra_cong_ty): " . number_format($totalReturned) . " đ\n";
echo "TỔNG CHI HIỂN THỊ: " . number_format($totalExpense + $totalReturned) . " đ\n";
