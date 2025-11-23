<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Incident;
use App\Models\Transaction;

echo "=== Syncing incident_staff.wage_amount with actual transactions ===\n\n";

DB::beginTransaction();

try {
    $incidents = Incident::with(['staff', 'transactions'])->get();
    
    $syncCount = 0;
    $totalUpdated = 0;
    
    foreach ($incidents as $incident) {
        $incidentHasChanges = false;
        
        foreach ($incident->staff as $staff) {
            $pivotWage = $staff->pivot->wage_amount ?? 0;
            
            // Calculate actual wage from transactions
            $actualWage = $incident->transactions
                ->where('staff_id', $staff->id)
                ->where('type', 'chi')
                ->sum('amount');
            
            if ($pivotWage != $actualWage) {
                echo "Incident #{$incident->id} - {$staff->full_name} ({$staff->employee_code}):\n";
                echo "  Pivot wage: " . number_format($pivotWage, 0, ',', '.') . "đ\n";
                echo "  Actual wage (from transactions): " . number_format($actualWage, 0, ',', '.') . "đ\n";
                echo "  → Updating to: " . number_format($actualWage, 0, ',', '.') . "đ\n\n";
                
                // Update pivot table
                DB::table('incident_staff')
                    ->where('incident_id', $incident->id)
                    ->where('staff_id', $staff->id)
                    ->update(['wage_amount' => $actualWage]);
                
                $incidentHasChanges = true;
                $totalUpdated++;
            }
        }
        
        if ($incidentHasChanges) {
            $syncCount++;
        }
    }
    
    if ($totalUpdated > 0) {
        DB::commit();
        
        echo "=== Summary ===\n";
        echo "Incidents synced: {$syncCount}\n";
        echo "Staff wage records updated: {$totalUpdated}\n";
        echo "\n✓ All wage amounts synced with actual transactions!\n";
    } else {
        DB::rollBack();
        echo "✓ All wage amounts already in sync. No updates needed.\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
