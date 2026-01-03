<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

$totalActive = $vehicle->transactions()->count();
$totalWithDeleted = $vehicle->transactions()->withTrashed()->count();
$totalDeleted = $totalWithDeleted - $totalActive;

echo "Tổng giao dịch ACTIVE: {$totalActive}\n";
echo "Tổng giao dịch DELETED: {$totalDeleted}\n";
echo "Tổng tất cả: {$totalWithDeleted}\n\n";

// Tính số dư theo scope
$totalRevenue = $vehicle->transactions()->revenue()->sum('amount');
$totalExpense = $vehicle->transactions()->expense()->sum('amount');
$totalFundDeposit = $vehicle->transactions()->fundDeposit()->sum('amount');
$totalBorrowed = $vehicle->transactions()->borrowFromCompany()->sum('amount');
$totalReturned = $vehicle->transactions()->returnToCompany()->sum('amount');

$balance = $totalRevenue + $totalFundDeposit + $totalBorrowed - $totalExpense - $totalReturned;

echo "Số dư theo scope: " . number_format($balance, 0, ',', '.') . "đ\n";
