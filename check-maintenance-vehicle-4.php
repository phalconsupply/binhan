<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\{Vehicle, Transaction};
use Carbon\Carbon;

$vehicle = Vehicle::find(4);
echo "Xe: {$vehicle->license_plate}\n\n";

echo "CAC GIAO DICH BAO TRI (chi):\n";
echo str_repeat('=', 80) . "\n";
$maintenances = Transaction::where('vehicle_id', 4)
    ->where('type', 'chi')
    ->orderBy('date')
    ->orderBy('id')
    ->get();

foreach ($maintenances as $t) {
    echo sprintf(
        "ID %-4s | %s | -%s | %s\n",
        $t->id,
        Carbon::parse($t->date)->format('d/m/Y'),
        str_pad(number_format($t->amount, 0, ',', '.'), 15, ' ', STR_PAD_LEFT),
        $t->note ?? 'N/A'
    );
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "GIAO DICH VAY HIEN TAI:\n";
echo str_repeat('=', 80) . "\n";
$borrows = Transaction::where('vehicle_id', 4)
    ->where('type', 'vay_cong_ty')
    ->orderBy('date')
    ->orderBy('id')
    ->get();

foreach ($borrows as $b) {
    echo sprintf(
        "ID %-4s | %s | +%s\n",
        $b->id,
        Carbon::parse($b->date)->format('d/m/Y'),
        str_pad(number_format($b->amount, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)
    );
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "TINH TOAN SO DU THEO TUNG GIAO DICH:\n";
echo str_repeat('=', 80) . "\n";

$balance = 0;
$allTransactions = Transaction::where('vehicle_id', 4)
    ->whereIn('type', ['thu', 'chi', 'vay_cong_ty'])
    ->orderBy('date')
    ->orderBy('id')
    ->get();

foreach ($allTransactions as $t) {
    $change = 0;
    if ($t->type == 'thu' || $t->type == 'vay_cong_ty') {
        $change = $t->amount;
        $balance += $change;
        $sign = '+';
    } else {
        $change = $t->amount;
        $balance -= $change;
        $sign = '-';
    }
    
    echo sprintf(
        "ID %-4s | %s | %s | %s%s | So du: %s %s\n",
        $t->id,
        Carbon::parse($t->date)->format('d/m/Y'),
        str_pad($t->type, 12),
        $sign,
        str_pad(number_format($change, 0, ',', '.'), 13, ' ', STR_PAD_LEFT),
        $balance >= 0 ? ' ' : '',
        str_pad(number_format($balance, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)
    );
}

echo "\n" . str_repeat('=', 80) . "\n";
echo "SO DU CUOI CUNG: " . number_format($balance, 0, ',', '.') . "d\n";
