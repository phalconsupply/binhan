<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Incident;
use App\Models\Transaction;

echo "=== Fixing Double Wage Transactions ===\n\n";

DB::beginTransaction();

try {
    // Find all incidents with potential double wage transactions
    $incidents = Incident::with(['transactions', 'staff'])->get();
    
    $fixedCount = 0;
    $deletedCount = 0;
    $affectedIncidents = [];
    
    foreach ($incidents as $incident) {
        $hasIssue = false;
        
        // Check each staff member
        foreach ($incident->staff as $staff) {
            $expectedWage = $staff->pivot->wage_amount ?? 0;
            
            if ($expectedWage <= 0) {
                continue; // Skip if no wage
            }
            
            // Get all wage transactions for this staff in this incident
            $wageTransactions = $incident->transactions
                ->where('staff_id', $staff->id)
                ->where('type', 'chi');
            
            if ($wageTransactions->count() > 1) {
                // Multiple transactions found - keep only the oldest one
                $hasIssue = true;
                
                echo "Incident #{$incident->id} - Staff: {$staff->full_name} (ID: {$staff->id})\n";
                echo "  Expected wage: " . number_format($expectedWage, 0, ',', '.') . "đ\n";
                echo "  Found {$wageTransactions->count()} transactions:\n";
                
                // Sort by created_at to keep the oldest
                $sorted = $wageTransactions->sortBy('created_at')->values();
                
                foreach ($sorted as $index => $trans) {
                    echo "    #{$trans->id}: " . number_format($trans->amount, 0, ',', '.') . "đ - {$trans->created_at->format('d/m/Y H:i:s')} - {$trans->note}\n";
                }
                
                // Keep the first (oldest) transaction
                $keepTransaction = $sorted->first();
                
                // Delete all others
                $toDelete = $sorted->slice(1);
                
                foreach ($toDelete as $trans) {
                    echo "    → Deleting duplicate transaction #{$trans->id}\n";
                    Transaction::where('id', $trans->id)->delete();
                    $deletedCount++;
                }
                
                echo "  ✓ Kept transaction #{$keepTransaction->id}\n";
                echo "\n";
                
                $fixedCount++;
                $affectedIncidents[] = $incident->id;
            }
        }
    }
    
    if ($fixedCount > 0) {
        DB::commit();
        
        echo "=== Summary ===\n";
        echo "Incidents fixed: {$fixedCount}\n";
        echo "Transactions deleted: {$deletedCount}\n";
        echo "Affected incidents: " . implode(', ', $affectedIncidents) . "\n";
        echo "\n✓ All fixes applied successfully!\n";
    } else {
        DB::rollBack();
        echo "✓ No double transactions found. Database is clean!\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
