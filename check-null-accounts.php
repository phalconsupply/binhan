<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

echo "=== CHECK NULL ACCOUNTS ===\n\n";

$nullAccounts = Transaction::whereNull('from_account_id')
    ->orWhereNull('to_account_id')
    ->orderBy('id')
    ->get(['id', 'code', 'description', 'type', 'from_account_id', 'to_account_id', 'amount']);

echo "Transactions with null accounts: " . $nullAccounts->count() . "\n\n";

if ($nullAccounts->count() > 0) {
    echo "ID\tCode\tType\tFrom Account\tTo Account\tAmount\tDescription\n";
    echo str_repeat("-", 100) . "\n";
    
    foreach ($nullAccounts as $tx) {
        echo sprintf(
            "%d\t%s\t%s\t%s\t%s\t%s\t%s\n",
            $tx->id,
            $tx->code ?? 'NULL',
            $tx->type,
            $tx->from_account_id ?? 'NULL',
            $tx->to_account_id ?? 'NULL',
            number_format($tx->amount),
            substr($tx->description, 0, 40)
        );
    }
}
