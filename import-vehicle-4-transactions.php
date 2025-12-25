<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Incident;
use Illuminate\Support\Facades\DB;

// Read backup file
$backupFile = __DIR__ . '/backup_transactions_vehicle_4_20251224125937.json';

if (!file_exists($backupFile)) {
    echo "Backup file not found!\n";
    exit(1);
}

$transactions = json_decode(file_get_contents($backupFile), true);

if (!$transactions) {
    echo "Failed to parse JSON!\n";
    exit(1);
}

echo "Found " . count($transactions) . " transactions to import\n";

DB::beginTransaction();

try {
    $importedCount = 0;
    $skippedCount = 0;
    $incidentIds = [];
    
    foreach ($transactions as $txn) {
        // Check if transaction already exists (by id or note)
        $existing = Transaction::where('id', $txn['id'])
            ->orWhere(function($q) use ($txn) {
                $q->where('note', $txn['note'])
                  ->where('amount', $txn['amount'])
                  ->where('date', $txn['date']);
            })
            ->first();
            
        if ($existing) {
            $skippedCount++;
            continue;
        }
        
        // Create transaction
        Transaction::create([
            'id' => $txn['id'],
            'incident_id' => $txn['incident_id'],
            'staff_id' => $txn['staff_id'],
            'vehicle_id' => $txn['vehicle_id'],
            'vehicle_maintenance_id' => $txn['vehicle_maintenance_id'],
            'type' => $txn['type'],
            'category' => $txn['category'],
            'transaction_category' => $txn['transaction_category'],
            'is_active' => $txn['is_active'],
            'replaced_by' => $txn['replaced_by'],
            'edited_at' => $txn['edited_at'],
            'edited_by' => $txn['edited_by'],
            'amount' => $txn['amount'],
            'method' => $txn['method'],
            'payment_method' => $txn['payment_method'],
            'note' => $txn['note'],
            'recorded_by' => $txn['recorded_by'],
            'date' => $txn['date'],
            'created_at' => $txn['created_at'],
            'updated_at' => $txn['updated_at'],
        ]);
        
        $importedCount++;
        
        // Track incident IDs
        if ($txn['incident_id']) {
            $incidentIds[$txn['incident_id']] = true;
        }
    }
    
    DB::commit();
    
    echo "\n✅ Import completed successfully!\n";
    echo "- Imported: $importedCount transactions\n";
    echo "- Skipped (duplicates): $skippedCount\n";
    echo "- Related incidents: " . count($incidentIds) . "\n";
    
    // List incident IDs that need to be checked
    if (count($incidentIds) > 0) {
        echo "\nIncident IDs in imported data:\n";
        foreach (array_keys($incidentIds) as $incidentId) {
            $incident = Incident::find($incidentId);
            if ($incident) {
                echo "  ✓ Incident #{$incidentId} exists\n";
            } else {
                echo "  ⚠️  Incident #{$incidentId} NOT FOUND - need to import incident data\n";
            }
        }
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Import failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
