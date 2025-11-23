<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\Transaction;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking Transaction #257 ===\n\n";

$transaction = Transaction::find(257);

if (!$transaction) {
    echo "Transaction #257 not found!\n";
    exit;
}

echo "Transaction Details:\n";
echo "ID: {$transaction->id}\n";
echo "Type: {$transaction->type}\n";
echo "Amount: " . number_format($transaction->amount) . "đ\n";
echo "Date: {$transaction->date}\n";
echo "Note: {$transaction->note}\n";
echo "Method: {$transaction->method}\n";
echo "Incident ID: " . ($transaction->incident_id ?? 'NULL') . "\n";
echo "Vehicle ID: " . ($transaction->vehicle_id ?? 'NULL') . "\n";
echo "Staff ID: " . ($transaction->staff_id ?? 'NULL') . "\n";

echo "\n=== Checking Grouping Logic ===\n";

// Simulate the controller logic
$allTransactions = Transaction::with(['vehicle', 'incident.patient', 'recorder'])
    ->orderBy('date', 'desc')
    ->get();

echo "Total transactions: " . $allTransactions->count() . "\n";

// Group by incident_id
$groupedTransactions = $allTransactions->groupBy('incident_id');

echo "Total groups: " . $groupedTransactions->count() . "\n";

// Check null group (transactions without incident)
$nullGroup = $groupedTransactions->get(null);
if ($nullGroup) {
    echo "Transactions with NULL incident_id: " . $nullGroup->count() . "\n";
    echo "Transaction #257 in NULL group: " . ($nullGroup->where('id', 257)->count() > 0 ? "YES" : "NO") . "\n";
    
    // Show first few from null group
    echo "\nFirst 5 transactions in NULL group:\n";
    foreach ($nullGroup->take(5) as $t) {
        echo "  ID: {$t->id} | Date: {$t->date} | Amount: " . number_format($t->amount) . "đ | Note: " . substr($t->note, 0, 40) . "\n";
    }
}

// Process groups like controller does
$processedGroups = $groupedTransactions->map(function($group) {
    return [
        'incident_id' => $group->first()->incident_id,
        'date' => $group->first()->date,
        'count' => $group->count(),
    ];
})->sortByDesc('date')->values();

echo "\n=== Group Positions (sorted by date desc) ===\n";
$position = 1;
$found = false;
foreach ($processedGroups as $group) {
    if ($group['incident_id'] === null) {
        echo "Position #{$position}: NULL group (Date: {$group['date']}, {$group['count']} transactions) ";
        if ($nullGroup && $nullGroup->where('id', 257)->count() > 0) {
            echo "<-- Transaction #257 is HERE";
            $found = true;
        }
        echo "\n";
    } else {
        echo "Position #{$position}: Incident #{$group['incident_id']} (Date: {$group['date']}, {$group['count']} transactions)\n";
    }
    $position++;
    
    if ($position > 25) {
        echo "... (showing first 25 groups)\n";
        break;
    }
}

if ($found) {
    $perPage = 20;
    $page = ceil($position / $perPage);
    echo "\n=== Pagination Info ===\n";
    echo "Per page: {$perPage}\n";
    echo "NULL group (containing #257) is at position ~{$position}\n";
    echo "Would be on page: {$page}\n";
}
