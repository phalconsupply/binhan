<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Vehicle;

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

echo "=== Vehicle: {$vehicle->license_plate} ===\n";
echo "Has Owner: " . ($vehicle->hasOwner() ? 'YES' : 'NO') . "\n";
if ($vehicle->owner) {
    echo "Owner: {$vehicle->owner->full_name}\n";
}
echo "\n";

// Get all transactions
$allTransactions = $vehicle->transactions()->orderBy('date', 'desc')->get();
echo "Total Transactions: {$allTransactions->count()}\n\n";

// Calculate stats
$totalRevenue = $allTransactions->where('type', 'thu')->sum('amount');
$totalExpense = $allTransactions->where('type', 'chi')->sum('amount');
$totalOwnerMaintenance = $allTransactions->where('type', 'chi')->where('category', 'bảo_trì_xe_chủ_riêng')->sum('amount');
$totalCompanyExpense = $totalExpense - $totalOwnerMaintenance;

echo "--- Financial Summary ---\n";
echo "Total Revenue: " . number_format($totalRevenue, 0, ',', '.') . "đ\n";
echo "Total Expense: " . number_format($totalExpense, 0, ',', '.') . "đ\n";
echo "  - Company Expense: " . number_format($totalCompanyExpense, 0, ',', '.') . "đ\n";
echo "  - Owner Maintenance: " . number_format($totalOwnerMaintenance, 0, ',', '.') . "đ\n";
echo "\n";

$netBeforeFee = $totalRevenue - $totalCompanyExpense;
$managementFee = $netBeforeFee > 0 ? $netBeforeFee * 0.15 : 0;
$profitAfterFee = $netBeforeFee - $managementFee - $totalOwnerMaintenance;

echo "Net Before Fee: " . number_format($netBeforeFee, 0, ',', '.') . "đ\n";
echo "Management Fee (15%): " . number_format($managementFee, 0, ',', '.') . "đ\n";
echo "Owner Maintenance: " . number_format($totalOwnerMaintenance, 0, ',', '.') . "đ\n";
echo "Final Profit (After Fee & Maintenance): " . number_format($profitAfterFee, 0, ',', '.') . "đ\n";
echo "\n";

// List maintenance transactions
echo "--- Owner Maintenance Transactions ---\n";
$maintenanceTransactions = $allTransactions->where('category', 'bảo_trì_xe_chủ_riêng');
echo "Count: {$maintenanceTransactions->count()}\n\n";

foreach ($maintenanceTransactions as $t) {
    echo "Transaction #{$t->id}:\n";
    echo "  Date: {$t->date->format('d/m/Y')}\n";
    echo "  Amount: " . number_format($t->amount, 0, ',', '.') . "đ\n";
    echo "  Note: {$t->note}\n";
    echo "\n";
}
