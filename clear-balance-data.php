<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

echo "=== FULL RECALCULATION - CLEAR ALL BALANCE DATA ===\n\n";

DB::beginTransaction();

try {
    // Clear all balance tracking data
    echo "Clearing all balance data...\n";
    Transaction::query()->update([
        'from_balance_before' => null,
        'from_balance_after' => null,
        'to_balance_before' => null,
        'to_balance_after' => null,
    ]);
    
    echo "✅ Cleared balance data for " . Transaction::count() . " transactions\n\n";
    
    DB::commit();
    
    echo "Now run: php artisan transactions:recalculate-balances\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Error: " . $e->getMessage() . "\n";
}
