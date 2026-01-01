<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Services\TransactionLifecycleService;

echo "=== TEST LOCK MECHANISM ===\n\n";

// Lấy giao dịch test
$tx = Transaction::where('code', 'GD20251218-0694')->first();
echo "📌 Testing with: {$tx->code} (Status: {$tx->lifecycle_status})\n";
echo "   Is locked: " . ($tx->is_locked ? 'YES' : 'NO') . "\n\n";

$service = new TransactionLifecycleService();

try {
    // Test 1: Lock transaction
    echo "🔒 Test 1: Locking transaction...\n";
    $service->lockTransaction($tx, 'Testing lock mechanism');
    
    $tx->refresh();
    echo "✅ Lock successful!\n";
    echo "   Is locked: " . ($tx->is_locked ? 'YES' : 'NO') . "\n";
    echo "   Locked at: " . ($tx->locked_at ? $tx->locked_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "   Locked by: User ID {$tx->locked_by}\n\n";
    
    // Test 2: Try to reverse locked transaction (should fail)
    echo "🔄 Test 2: Attempting to reverse locked transaction (should fail)...\n";
    try {
        $service->reverseTransaction($tx, 'This should fail');
        echo "❌ FAIL: Should have thrown exception!\n";
    } catch (\Exception $e) {
        echo "✅ PASS: Exception thrown as expected\n";
        echo "   Error: {$e->getMessage()}\n\n";
    }
    
    // Test 3: Unlock
    echo "🔓 Test 3: Unlocking transaction...\n";
    $tx->update([
        'is_locked' => false,
        'locked_at' => null,
        'locked_by' => null,
    ]);
    
    $tx->refresh();
    echo "✅ Unlock successful!\n";
    echo "   Is locked: " . ($tx->is_locked ? 'YES' : 'NO') . "\n\n";
    
    echo "✅ ALL LOCK TESTS PASSED!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    echo $e->getTraceAsString() . "\n";
}
