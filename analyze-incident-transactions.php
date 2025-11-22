<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Incident;
use App\Models\Transaction;

echo "=== Analyzing Incident Transaction Management ===\n\n";

// Get a sample incident with transactions
$incident = Incident::with(['transactions', 'staff'])->has('transactions')->first();

if (!$incident) {
    echo "No incidents with transactions found.\n";
    exit;
}

echo "Sample Incident: #{$incident->id}\n";
echo "Date: {$incident->date->format('d/m/Y H:i')}\n";
echo "Vehicle: " . ($incident->vehicle ? $incident->vehicle->license_plate : 'N/A') . "\n";
echo "\n";

// Analyze transactions
$transactions = $incident->transactions;
echo "Total Transactions: {$transactions->count()}\n";
echo "-----------------------------------\n";

$groupedByType = $transactions->groupBy('type');

foreach ($groupedByType as $type => $trans) {
    echo "\n{$type} ({$trans->count()} transactions):\n";
    foreach ($trans as $t) {
        $source = '';
        if ($t->staff_id) $source = '[Staff: ' . $t->staff_id . ']';
        if ($t->vehicle_maintenance_id) $source = '[Maintenance: ' . $t->vehicle_maintenance_id . ']';
        
        echo "  - ID {$t->id}: " . number_format($t->amount, 0) . "đ {$source} - {$t->note}\n";
    }
}

echo "\n\n=== Current Edit Logic Analysis ===\n";
echo "-----------------------------------\n";

echo "\n1. In update() method:\n";
echo "   ✓ Deletes old wage transactions (staff wages)\n";
echo "   ✓ Recreates wage transactions\n";
echo "   ✓ Deletes old commission transactions\n";
echo "   ✓ Recreates commission if partner exists\n";

echo "\n2. Missing in update() method:\n";
echo "   ✗ Main revenue transaction (amount_thu)\n";
echo "   ✗ Main expense transaction (amount_chi)\n";
echo "   ✗ Additional services revenue\n";
echo "   ✗ Additional expenses\n";
echo "   ✗ Maintenance transactions\n";

echo "\n3. Potential Issues:\n";
echo "   ⚠️ Old revenue/expense transactions NOT deleted when editing\n";
echo "   ⚠️ Could create duplicate transactions on each edit\n";
echo "   ⚠️ Manual transactions in Transaction table might be lost\n";

echo "\n\n=== Checking for Orphaned Transactions ===\n";
echo "-------------------------------------------\n";

// Check for transactions that are not linked properly
$allTransactions = Transaction::where('incident_id', $incident->id)->get();
$linkedTypes = [
    'staff_wage' => $allTransactions->whereNotNull('staff_id')->count(),
    'maintenance' => $allTransactions->whereNotNull('vehicle_maintenance_id')->count(),
    'other' => $allTransactions->whereNull('staff_id')->whereNull('vehicle_maintenance_id')->count(),
];

echo "Transaction breakdown:\n";
foreach ($linkedTypes as $type => $count) {
    echo "  - {$type}: {$count}\n";
}

echo "\n";
