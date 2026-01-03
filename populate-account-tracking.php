<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Services\AccountBalanceService;
use Illuminate\Support\Facades\DB;

echo "=================================================================\n";
echo "POPULATE ACCOUNT TRACKING CHO TAT CA GIAO DICH CU\n";
echo "=================================================================\n\n";

$needUpdate = Transaction::whereNull('from_account')->count();
$total = Transaction::count();

echo "Tong so giao dich:        {$total}\n";
echo "Can cap nhat:             {$needUpdate}\n\n";

if ($needUpdate == 0) {
    echo "✓ Tat ca giao dich da co account tracking!\n";
    exit;
}

echo "Bat dau xu ly...\n\n";

DB::beginTransaction();

try {
    $transactions = Transaction::whereNull('from_account')
        ->orderBy('date')
        ->orderBy('id')
        ->get();
    
    $processed = 0;
    $errors = 0;
    
    foreach ($transactions as $tx) {
        try {
            $accounts = AccountBalanceService::determineAccounts($tx);
            
            $tx->from_account = $accounts['from_account'];
            $tx->to_account = $accounts['to_account'];
            $tx->save(['timestamps' => false]);
            
            $processed++;
            
            if ($processed % 50 == 0) {
                echo "✓ Da xu ly: {$processed}/{$needUpdate}\n";
            }
        } catch (\Exception $e) {
            $errors++;
            echo "✗ Loi GD #{$tx->id} ({$tx->code}): " . $e->getMessage() . "\n";
        }
    }
    
    DB::commit();
    
    echo "\n=================================================================\n";
    echo "✓ HOAN THANH!\n";
    echo "=================================================================\n";
    echo "Da xu ly:  {$processed} giao dich\n";
    echo "Loi:       {$errors}\n";
    echo "=================================================================\n\n";
    
    $stillNull = Transaction::whereNull('from_account')->count();
    echo "Con lai chua co tracking: {$stillNull}\n\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n✗ LOI NGHIEM TRONG: " . $e->getMessage() . "\n";
    echo "Transaction da duoc rollback.\n";
}