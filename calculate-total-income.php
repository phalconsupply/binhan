<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

echo "=================================================================\n";
echo "TỔNG THU XE 49B08879 (BAO GỒM NỘP QUỸ)\n";
echo "=================================================================\n\n";

echo "🚗 XE: {$vehicle->license_plate} (ID: {$vehicle->id})\n\n";

// Tổng thu (type = thu)
$totalRevenue = $vehicle->transactions()->revenue()->sum('amount');
$revenueCount = $vehicle->transactions()->revenue()->count();

echo "📈 TỔNG THU (type = 'thu'):\n";
echo "   Số lượng GD: {$revenueCount}\n";
echo "   Tổng tiền:   " . number_format($totalRevenue, 0, ',', '.') . "đ\n\n";

// Tổng nộp quỹ (type = nop_quy)
$totalFundDeposit = $vehicle->transactions()->fundDeposit()->sum('amount');
$fundDepositCount = $vehicle->transactions()->fundDeposit()->count();

echo "💰 TỔNG NỘP QUỸ (type = 'nop_quy'):\n";
echo "   Số lượng GD: {$fundDepositCount}\n";
echo "   Tổng tiền:   " . number_format($totalFundDeposit, 0, ',', '.') . "đ\n\n";

// Tổng cộng
$totalIncome = $totalRevenue + $totalFundDeposit;

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 TỔNG THU + NỘP QUỸ:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "   Số lượng GD: " . ($revenueCount + $fundDepositCount) . "\n";
echo "   TỔNG CỘNG:   " . number_format($totalIncome, 0, ',', '.') . "đ\n\n";

// Chi tiết các giao dịch thu
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 CHI TIẾT CÁC GIAO DỊCH THU\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$revenues = $vehicle->transactions()->revenue()->orderBy('date')->get();
foreach ($revenues as $tx) {
    echo sprintf("%-20s | %-12s | %15s\n",
        $tx->code,
        $tx->date->format('d/m/Y'),
        number_format($tx->amount, 0, ',', '.') . 'đ'
    );
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 CHI TIẾT CÁC GIAO DỊCH NỘP QUỸ\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$fundDeposits = $vehicle->transactions()->fundDeposit()->orderBy('date')->get();
foreach ($fundDeposits as $tx) {
    echo sprintf("%-20s | %-12s | %15s\n",
        $tx->code,
        $tx->date->format('d/m/Y'),
        number_format($tx->amount, 0, ',', '.') . 'đ'
    );
}

// Tổng chi để so sánh
echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 SO SÁNH THU CHI\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$totalExpense = $vehicle->transactions()->expense()->sum('amount');
$expenseCount = $vehicle->transactions()->expense()->count();

echo "Thu + Nộp quỹ:  " . number_format($totalIncome, 0, ',', '.') . "đ ({$revenueCount} + {$fundDepositCount} GD)\n";
echo "Chi:            " . number_format($totalExpense, 0, ',', '.') . "đ ({$expenseCount} GD)\n";
echo "──────────────────────────────────────────\n";
echo "Chênh lệch:     " . number_format($totalIncome - $totalExpense, 0, ',', '.') . "đ\n\n";

// Số dư cuối cùng
$balance = $totalIncome - $totalExpense;
echo "💡 Số dư xe (Thu + Nộp quỹ - Chi): " . number_format($balance, 0, ',', '.') . "đ\n";

echo "\n";
