<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

echo "=================================================================\n";
echo "KIỂM TRA XEM UI CÓ TÍNH CẢ DELETED KHÔNG\n";
echo "=================================================================\n\n";

// Tính với deleted
$totalExpenseWithDeleted = $vehicle->transactions()->withTrashed()->expense()->sum('amount');
$totalExpenseActive = $vehicle->transactions()->expense()->sum('amount');

echo "Tổng chi (ACTIVE):         " . number_format($totalExpenseActive, 0, ',', '.') . "đ\n";
echo "Tổng chi (bao gồm DELETED): " . number_format($totalExpenseWithDeleted, 0, ',', '.') . "đ\n";
echo "Chênh lệch (deleted):      " . number_format($totalExpenseWithDeleted - $totalExpenseActive, 0, ',', '.') . "đ\n\n";

$uiValue = 105573425;
$diff1 = $uiValue - $totalExpenseActive;
$diff2 = $uiValue - $totalExpenseWithDeleted;

echo "UI hiển thị:               " . number_format($uiValue, 0, ',', '.') . "đ\n";
echo "Chênh vs ACTIVE:           " . number_format($diff1, 0, ',', '.') . "đ\n";
echo "Chênh vs WITH DELETED:     " . number_format($diff2, 0, ',', '.') . "đ\n\n";

if (abs($diff2) < 0.01) {
    echo "✅ UI đang tính CẢ GIAO DỊCH ĐÃ XÓA!\n";
    echo "   Đây là BUG - UI không nên tính giao dịch đã xóa.\n";
} elseif (abs($diff1) < 1000000 && abs($diff2) > abs($diff1)) {
    echo "💡 UI có thể đang tính active + một phần gì đó khác\n";
    echo "   Kiểm tra xem có cache hoặc logic sai không.\n";
}

echo "\n";
