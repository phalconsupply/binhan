<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

echo "=== SYNC ACCOUNT BALANCES ===\n\n";

$accounts = Account::all();

foreach ($accounts as $account) {
    $code = $account->code;
    echo "Processing: {$code}\n";
    
    // Get last transaction that touched this account
    $lastTx = Transaction::where(function($q) use ($account) {
        $q->where('from_account_id', $account->id)
          ->orWhere('to_account_id', $account->id);
    })
    ->orderBy('id', 'desc')
    ->first();
    
    if ($lastTx) {
        $newBalance = $lastTx->from_account_id == $account->id 
            ? $lastTx->from_balance_after 
            : $lastTx->to_balance_after;
        
        if ($account->balance != $newBalance) {
            echo "  Old: " . number_format($account->balance) . "đ\n";
            echo "  New: " . number_format($newBalance) . "đ\n";
            
            $account->balance = $newBalance;
            $account->save();
            echo "  ✅ Updated\n";
        } else {
            echo "  ✓ Already correct\n";
        }
    } else {
        echo "  No transactions yet\n";
    }
}

echo "\n✅ Done!\n";
