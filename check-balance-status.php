<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;

echo "=== KIEM TRA COMPANY_FUND SAU KHI XOA TX 639 ===\n\n";

// Last transaction
$last = Transaction::where(function($q) {
    $q->where('from_account', 'company_fund')
      ->orWhere('to_account', 'company_fund');
})
->orderBy('id', 'desc')
->first();

echo "Last Transaction:\n";
echo "  ID: {$last->id}\n";
echo "  Code: {$last->code}\n";
echo "  Date: {$last->date}\n";
echo "  Type: {$last->type}\n";
echo "  Amount: " . number_format($last->amount, 0, ',', '.') . " VND\n\n";

echo "From: {$last->from_account}\n";
echo "  Balance before: " . number_format($last->from_balance_before ?? 0, 0, ',', '.') . "\n";
echo "  Balance after:  " . number_format($last->from_balance_after ?? 0, 0, ',', '.') . "\n\n";

echo "To: {$last->to_account}\n";
echo "  Balance before: " . number_format($last->to_balance_before ?? 0, 0, ',', '.') . "\n";
echo "  Balance after:  " . number_format($last->to_balance_after ?? 0, 0, ',', '.') . "\n\n";

// Calculate current balance
$balance = 0;
$txs = Transaction::where(function($q) {
    $q->where('from_account', 'company_fund')
      ->orWhere('to_account', 'company_fund');
})
->orderBy('date')
->orderBy('id')
->get();

echo "Total transactions: " . $txs->count() . "\n\n";

foreach ($txs as $tx) {
    if ($tx->from_account === 'company_fund') {
        $balance -= $tx->amount;
    }
    if ($tx->to_account === 'company_fund') {
        $balance += $tx->amount;
    }
}

echo "=== BALANCE SUMMARY ===\n";
echo "Calculated balance:  " . number_format($balance, 0, ',', '.') . " VND\n";
echo "Last recorded (from): " . number_format($last->from_account === 'company_fund' ? $last->from_balance_after : 0, 0, ',', '.') . " VND\n";
echo "Last recorded (to):   " . number_format($last->to_account === 'company_fund' ? $last->to_balance_after : 0, 0, ',', '.') . " VND\n";

$lastRecorded = $last->from_account === 'company_fund' ? $last->from_balance_after : $last->to_balance_after;
$diff = abs($balance - $lastRecorded);

echo "\nDifference: " . number_format($diff, 0, ',', '.') . " VND\n";

if ($diff < 0.01) {
    echo "✅ BALANCED!\n";
} else {
    echo "❌ DISCREPANCY FOUND!\n";
}
