<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

echo "=== CHECK TRANSACTION ACCOUNT IDS ===\n\n";

$total = Transaction::count();
echo "Total transactions: $total\n\n";

$withIds = Transaction::whereNotNull('from_account_id')
    ->whereNotNull('to_account_id')
    ->count();
echo "With both IDs: $withIds\n";

$withFromOnly = Transaction::whereNotNull('from_account_id')
    ->whereNull('to_account_id')
    ->count();
echo "With from_account_id only: $withFromOnly\n";

$withToOnly = Transaction::whereNull('from_account_id')
    ->whereNotNull('to_account_id')
    ->count();
echo "With to_account_id only: $withToOnly\n";

$withNone = Transaction::whereNull('from_account_id')
    ->whereNull('to_account_id')
    ->count();
echo "With no IDs: $withNone\n\n";

// Sample some transactions
echo "Sample 5 transactions:\n";
$samples = Transaction::orderBy('id')->take(5)->get(['id', 'code', 'from_account', 'to_account', 'from_account_id', 'to_account_id', 'amount']);

foreach ($samples as $tx) {
    echo "TX #{$tx->id}: from={$tx->from_account}({$tx->from_account_id}) to={$tx->to_account}({$tx->to_account_id}) {$tx->amount}đ\n";
}
