<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Services\TransactionLifecycleService;

echo "=== TEST SOFT DELETE ===\n\n";

// Tạo test transaction
$testTx = Transaction::create([
    'code' => 'TEST-SD-' . date('His'),
    'vehicle_id' => 4,
    'type' => 'chi',
    'category' => 'nhiên_liệu',
    'amount' => 100000,
    'method' => 'cash',
    'note' => 'Test soft delete',
    'recorded_by' => 1,
    'date' => now(),
    'is_active' => true,
    'from_account_id' => 5, // company_fund
    'to_account_id' => 3, // external
    'from_account' => 'company_fund',
    'to_account' => 'external',
    'lifecycle_status' => 'active',
]);

echo "✅ Created test transaction: {$testTx->code} (ID: {$testTx->id})\n\n";

// Test soft delete
$service = new TransactionLifecycleService();

try {
    echo "🗑️  Soft deleting...\n";
    $service->softDeleteTransaction($testTx, 'Test soft delete functionality');
    
    echo "✅ Soft delete successful!\n\n";
    
    // Verify
    $deleted = Transaction::withTrashed()->find($testTx->id);
    echo "📊 Transaction status after soft delete:\n";
    echo "   Code: {$deleted->code}\n";
    echo "   Lifecycle status: {$deleted->lifecycle_status}\n";
    echo "   Deleted at: " . ($deleted->deleted_at ? $deleted->deleted_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "   Is trashed: " . ($deleted->trashed() ? 'YES' : 'NO') . "\n\n";
    
    // Test restore
    echo "♻️  Restoring transaction...\n";
    $restored = $service->restoreTransaction($deleted->id);
    
    echo "✅ Restore successful!\n\n";
    echo "📊 Transaction status after restore:\n";
    echo "   Code: {$restored->code}\n";
    echo "   Lifecycle status: {$restored->lifecycle_status}\n";
    echo "   Deleted at: " . ($restored->deleted_at ? $restored->deleted_at->format('Y-m-d H:i:s') : 'NULL') . "\n";
    echo "   Is trashed: " . ($restored->trashed() ? 'YES' : 'NO') . "\n\n";
    
    // Clean up - hard delete the test transaction
    $restored->forceDelete();
    echo "🧹 Cleaned up test transaction\n";
    
    echo "\n✅ ALL TESTS PASSED!\n";
    
} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    echo $e->getTraceAsString() . "\n";
}
