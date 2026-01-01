<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Account;
use Illuminate\Support\Facades\DB;

echo "=== DETAILED ACCOUNT ANALYSIS ===\n\n";

$accounts = Account::whereIn('code', ['customer', 'vehicle_2', 'vehicle_3', 'vehicle_4', 'external', 'staff_4', 'staff_6', 'staff_7'])
    ->get();

foreach ($accounts as $account) {
    echo "Account: {$account->code} ({$account->name})\n";
    echo "Recorded Balance: " . number_format($account->balance) . "đ\n";
    
    // Calculate from debits - credits
    $debits = DB::table('transactions')
        ->where('to_account_id', $account->id)
        ->sum('amount');
    
    $credits = DB::table('transactions')
        ->where('from_account_id', $account->id)
        ->sum('amount');
    
    $calculated = $debits - $credits;
    
    echo "Debits (IN): " . number_format($debits) . "đ\n";
    echo "Credits (OUT): " . number_format($credits) . "đ\n";
    echo "Calculated Balance: " . number_format($calculated) . "đ\n";
    echo "Difference: " . number_format($account->balance - $calculated) . "đ\n";
    
    // Get last transaction balance
    $lastTx = DB::table('transactions')
        ->where(function($q) use ($account) {
            $q->where('from_account_id', $account->id)
              ->orWhere('to_account_id', $account->id);
        })
        ->orderBy('id', 'desc')
        ->first();
    
    if ($lastTx) {
        $lastBalance = $lastTx->from_account_id == $account->id 
            ? $lastTx->from_balance_after 
            : $lastTx->to_balance_after;
        echo "Last Transaction Balance: " . number_format($lastBalance ?? 0) . "đ\n";
    }
    
    echo "\n" . str_repeat("-", 80) . "\n\n";
}
