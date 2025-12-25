<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;
use App\Services\AccountBalanceService;

echo "=== KIEM TRA SO DU XE 49B08879 (vehicle_4) ===\n\n";

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

echo "Xe: " . $vehicle->license_plate . "\n";
echo "Co chu: " . ($vehicle->hasOwner() ? 'CO' : 'KHONG') . "\n\n";

// Lấy số dư từ service
$balance = AccountBalanceService::getCurrentBalance("vehicle_{$vehicle->id}");
echo "So du hien tai: " . number_format($balance, 0, ',', '.') . " VND\n\n";

// Lấy các giao dịch liên quan
$transactions = Transaction::where(function($q) use ($vehicle) {
    $q->where('from_account', "vehicle_{$vehicle->id}")
      ->orWhere('to_account', "vehicle_{$vehicle->id}");
})
->orderBy('date')
->orderBy('id')
->get();

echo "Cac giao dich lien quan (" . $transactions->count() . " giao dich):\n";
echo str_repeat('-', 120) . "\n";

$runningBalance = 0;
foreach ($transactions as $tx) {
    if ($tx->to_account === "vehicle_{$vehicle->id}") {
        $runningBalance += $tx->amount;
        $sign = '+';
    } else {
        $runningBalance -= $tx->amount;
        $sign = '-';
    }
    
    printf("%s | %s | %-15s | %s%14s | %15s | %-40s\n",
        $tx->code ?? 'N/A',
        \Carbon\Carbon::parse($tx->date)->format('d/m/Y'),
        $tx->type,
        $sign,
        number_format($tx->amount, 0, ',', '.'),
        number_format($runningBalance, 0, ',', '.'),
        substr($tx->note ?? '', 0, 40)
    );
}

echo str_repeat('-', 120) . "\n";
echo "So du cuoi: " . number_format($runningBalance, 0, ',', '.') . " VND\n";
