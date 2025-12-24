<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vehicle;
use App\Models\Transaction;

$vehicle = Vehicle::find(4);

echo "=================================================================\n";
echo "KIEM TRA HIEN THI CANH BAO NO VA NUT TRA NO\n";
echo "=================================================================\n\n";

echo "Xe: {$vehicle->license_plate}\n";
echo "Chu xe: {$vehicle->owner->full_name}\n\n";

// Tính toán giống như trong VehicleController
$statsQuery = $vehicle->transactions();

$totalRevenue = $statsQuery->revenue()->sum('amount');
$totalFundDeposit = $statsQuery->fundDeposit()->sum('amount');
$totalExpense = $statsQuery->expense()->sum('amount');

$totalOwnerMaintenance = $statsQuery->expense()->where('category', 'bảo_trì_xe_chủ_riêng')->sum('amount');

// Tính borrowed
$totalBorrowed = $statsQuery->borrowFromCompany()->sum('amount');
$totalReturned = $statsQuery->returnToCompany()->sum('amount');
$stats_total_borrowed = $totalBorrowed - $totalReturned;

// Tính revenue_display và profit
$stats_total_revenue_display = $totalRevenue + $totalFundDeposit;
$stats_total_profit_after_fee = $stats_total_revenue_display - $totalExpense;

echo "THONG SO TINH TOAN:\n";
echo "  has_owner:              true\n";
echo "  total_revenue:          " . number_format($totalRevenue, 0, ',', '.') . "d\n";
echo "  total_fund_deposit:     " . number_format($totalFundDeposit, 0, ',', '.') . "d\n";
echo "  total_revenue_display:  " . number_format($stats_total_revenue_display, 0, ',', '.') . "d\n";
echo "  total_expense:          " . number_format($totalExpense, 0, ',', '.') . "d\n";
echo "  total_profit_after_fee: " . number_format($stats_total_profit_after_fee, 0, ',', '.') . "d\n";
echo "  total_borrowed:         " . number_format($stats_total_borrowed, 0, ',', '.') . "d\n\n";

echo "DIEU KIEN HIEN THI CANH BAO:\n";
echo "  1. has_owner:                    " . ($vehicle->hasOwner() ? 'true' : 'false') . "\n";
echo "  2. isset(total_borrowed):        " . (isset($stats_total_borrowed) ? 'true' : 'false') . "\n";
echo "  3. total_borrowed > 0:           " . ($stats_total_borrowed > 0 ? 'true' : 'false') . "\n";
echo "  => Hien thi canh bao?            " . ($vehicle->hasOwner() && isset($stats_total_borrowed) && $stats_total_borrowed > 0 ? 'CO' : 'KHONG') . "\n\n";

echo "DIEU KIEN HIEN THI NUT TRA NO:\n";
echo "  1. Canh bao hien thi:            " . ($vehicle->hasOwner() && isset($stats_total_borrowed) && $stats_total_borrowed > 0 ? 'CO' : 'KHONG') . "\n";
echo "  2. total_profit_after_fee > 0:   " . ($stats_total_profit_after_fee > 0 ? 'true' : 'false') . "\n";
echo "  => Hien thi nut tra no?          " . ($stats_total_profit_after_fee > 0 ? 'CO' : 'KHONG') . "\n\n";

if (!($vehicle->hasOwner() && isset($stats_total_borrowed) && $stats_total_borrowed > 0)) {
    echo "NGUYEN NHAN KHONG HIEN THI:\n";
    if (!$vehicle->hasOwner()) {
        echo "  - Xe khong co chu xe\n";
    }
    if (!isset($stats_total_borrowed)) {
        echo "  - Bien total_borrowed khong duoc set\n";
    }
    if ($stats_total_borrowed <= 0) {
        echo "  - total_borrowed = 0 hoac am\n";
    }
} else if ($stats_total_profit_after_fee <= 0) {
    echo "LY DO KHONG CO NUT TRA NO:\n";
    echo "  - Canh bao hien thi NHUNG so du khong duong\n";
    echo "  - So du = " . number_format($stats_total_profit_after_fee, 0, ',', '.') . "d\n";
    echo "  - Can co so du > 0 moi co the tra no\n";
}

echo "\n=================================================================\n";
