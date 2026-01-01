<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Services\AccountBalanceService;

echo "=== TEST RECALCULATE TRANSACTION 639 ===\n\n";

$tx = Transaction::find(639);

echo "Before:\n";
echo "  From balance after: " . number_format($tx->from_balance_after ?? 0) . "\n";
echo "  To balance after: " . number_format($tx->to_balance_after ?? 0) . "\n\n";

// Manual recalculate
try {
    AccountBalanceService::updateTransactionBalances($tx, true);
    
    $tx->refresh();
    
    echo "After:\n";
    echo "  From balance after: " . number_format($tx->from_balance_after ?? 0) . "\n";
    echo "  To balance after: " . number_format($tx->to_balance_after ?? 0) . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
