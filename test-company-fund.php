<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;

echo "=== KIEM TRA GIAO DICH CUOI CUNG CUA COMPANY_FUND ===\n\n";

$lastTx = Transaction::where(function($q) {
    $q->where('from_account', 'company_fund')
      ->orWhere('to_account', 'company_fund');
})
->orderBy('date', 'desc')
->orderBy('id', 'desc')
->first();

if (!$lastTx) {
    echo "Khong tim thay giao dich nao!\n";
    exit;
}

echo "Last Transaction ID: {$lastTx->id}\n";
echo "Code: {$lastTx->code}\n";
echo "Date: {$lastTx->date}\n";
echo "Type: {$lastTx->type}\n";
echo "Amount: " . number_format($lastTx->amount, 0, ',', '.') . " VND\n\n";

echo "From: {$lastTx->from_account}\n";
echo "  Balance before: " . number_format($lastTx->from_balance_before ?? 0, 0, ',', '.') . "\n";
echo "  Balance after:  " . number_format($lastTx->from_balance_after ?? 0, 0, ',', '.') . "\n\n";

echo "To: {$lastTx->to_account}\n";
echo "  Balance before: " . number_format($lastTx->to_balance_before ?? 0, 0, ',', '.') . "\n";
echo "  Balance after:  " . number_format($lastTx->to_balance_after ?? 0, 0, ',', '.') . "\n\n";

// Tính balance hiện tại
$balance = 0;
$txs = Transaction::where(function($q) {
    $q->where('from_account', 'company_fund')
      ->orWhere('to_account', 'company_fund');
})
->orderBy('date')
->orderBy('id')
->get();

foreach ($txs as $tx) {
    if ($tx->from_account === 'company_fund') {
        $balance -= $tx->amount;
    }
    if ($tx->to_account === 'company_fund') {
        $balance += $tx->amount;
    }
}

echo "=== CALCULATED BALANCE ===\n";
echo "Current balance: " . number_format($balance, 0, ',', '.') . " VND\n";
echo "Last recorded:   " . number_format($lastTx->from_account === 'company_fund' ? $lastTx->from_balance_after : $lastTx->to_balance_after, 0, ',', '.') . " VND\n";
