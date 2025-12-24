<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Vehicle;
use App\Models\Transaction;

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

// Get all transactions for incident 115
$incident115Transactions = Transaction::where('incident_id', 115)
    ->orderBy('date')
    ->get();

echo "=== DEBUG CHUYẾN #115 ===\n\n";

echo "Transactions:\n";
foreach ($incident115Transactions as $tx) {
    echo sprintf("  %s | %s | %s | %s\n", 
        $tx->code,
        $tx->type,
        $tx->date,
        number_format($tx->amount)
    );
}

echo "\nGroup calculation:\n";
$revenueTypes = ['thu', 'vay_cong_ty', 'nop_quy'];
$expenseTypes = ['chi', 'tra_cong_ty', 'du_kien_chi'];

$totalRevenue = $incident115Transactions->filter(function($t) use ($revenueTypes) { 
    return in_array($t->type, $revenueTypes);
})->sum('amount');

$totalExpense = $incident115Transactions->filter(function($t) use ($expenseTypes) { 
    return in_array($t->type, $expenseTypes);
})->sum('amount');

$realRevenue = $incident115Transactions->filter(function($t) { return $t->type === 'thu'; })->sum('amount');
$realExpense = $incident115Transactions->filter(function($t) { return $t->type === 'chi'; })->sum('amount');

echo "  total_revenue: " . number_format($totalRevenue) . "\n";
echo "  total_expense: " . number_format($totalExpense) . "\n";
echo "  real_revenue (thu only): " . number_format($realRevenue) . "\n";
echo "  real_expense (chi only): " . number_format($realExpense) . "\n";

$revenueForFee = $realRevenue - $realExpense;
$managementFee = ($vehicle->hasOwner() && $revenueForFee > 0) ? $revenueForFee * 0.15 : 0;

echo "  revenue_for_fee: " . number_format($revenueForFee) . "\n";
echo "  management_fee (15%): " . number_format($managementFee) . "\n";
echo "  net_amount: " . number_format($totalRevenue - $totalExpense) . "\n";
echo "  profit_after_fee: " . number_format(($totalRevenue - $totalExpense) - $managementFee) . "\n";

echo "\nKỲ VỌNG (từ ảnh):\n";
echo "  Thu: 0đ (SAI - nên là 2,000,000đ)\n";
echo "  Chi: 350,000đ\n";
echo "  Lợi nhuận: -350,000đ (SAI - nên là 1,650,000đ)\n";
echo "  Phí 15%: Không hiển thị (SAI - nên là 277,500đ)\n";
