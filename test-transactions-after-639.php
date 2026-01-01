<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;

echo "=== TRANSACTIONS SAU ID 639 LIEN QUAN COMPANY_FUND ===\n\n";

$txs = Transaction::where('id', '>', 639)
    ->where(function($q) {
        $q->where('from_account', 'company_fund')
          ->orWhere('to_account', 'company_fund');
    })
    ->orderBy('id')
    ->get();

echo "Found " . $txs->count() . " transactions\n\n";

foreach ($txs as $tx) {
    echo "ID: {$tx->id} | {$tx->code} | {$tx->type} | " . number_format($tx->amount) . "đ\n";
    echo "  From: {$tx->from_account} (after: " . number_format($tx->from_balance_after ?? 0) . ")\n";
    echo "  To: {$tx->to_account} (after: " . number_format($tx->to_balance_after ?? 0) . ")\n\n";
}
