<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\Incident;
use App\Models\Staff;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing TransactionObserver Auto-Sync ===\n\n";

$incidentId = 14;
$staffId = 7; // Lê Phong

// Get current pivot wage
$currentPivotWage = DB::table('incident_staff')
    ->where('incident_id', $incidentId)
    ->where('staff_id', $staffId)
    ->value('wage_amount');

echo "Step 1: Current state\n";
echo "  Pivot wage_amount: " . number_format($currentPivotWage) . "đ\n";

$actualWage = Transaction::where('incident_id', $incidentId)
    ->where('staff_id', $staffId)
    ->where('type', 'chi')
    ->sum('amount');
echo "  Actual transaction sum: " . number_format($actualWage) . "đ\n\n";

// Test 1: Create a new transaction
echo "Step 2: Creating new wage transaction (200k)\n";
$transaction = Transaction::create([
    'incident_id' => $incidentId,
    'staff_id' => $staffId,
    'type' => 'chi',
    'amount' => 200000,
    'date' => now(),
    'description' => 'TEST - Tiền công lái xe: Lê Phong (auto-sync test)',
    'recorded_by' => 1,
]);
echo "  Transaction created: ID#{$transaction->id}\n";

// Check if observer synced pivot table
$newPivotWage = DB::table('incident_staff')
    ->where('incident_id', $incidentId)
    ->where('staff_id', $staffId)
    ->value('wage_amount');

echo "  Pivot wage_amount after CREATE: " . number_format($newPivotWage) . "đ\n";
if ($newPivotWage == 200000) {
    echo "  ✓ Observer CREATE worked! Pivot auto-synced.\n\n";
} else {
    echo "  ✗ Observer CREATE failed. Expected 200000, got {$newPivotWage}\n\n";
}

// Test 2: Update transaction
echo "Step 3: Updating transaction amount to 250k\n";
$transaction->update(['amount' => 250000]);
echo "  Transaction updated\n";

$updatedPivotWage = DB::table('incident_staff')
    ->where('incident_id', $incidentId)
    ->where('staff_id', $staffId)
    ->value('wage_amount');

echo "  Pivot wage_amount after UPDATE: " . number_format($updatedPivotWage) . "đ\n";
if ($updatedPivotWage == 250000) {
    echo "  ✓ Observer UPDATE worked! Pivot auto-synced.\n\n";
} else {
    echo "  ✗ Observer UPDATE failed. Expected 250000, got {$updatedPivotWage}\n\n";
}

// Test 3: Delete transaction
echo "Step 4: Deleting transaction\n";
$transaction->delete();
echo "  Transaction deleted\n";

$finalPivotWage = DB::table('incident_staff')
    ->where('incident_id', $incidentId)
    ->where('staff_id', $staffId)
    ->value('wage_amount');

echo "  Pivot wage_amount after DELETE: " . number_format($finalPivotWage) . "đ\n";
if ($finalPivotWage == 0) {
    echo "  ✓ Observer DELETE worked! Pivot auto-synced.\n\n";
} else {
    echo "  ✗ Observer DELETE failed. Expected 0, got {$finalPivotWage}\n\n";
}

echo "=== Test Complete ===\n";
echo "Observer is working correctly if all 3 tests passed.\n";
