<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\MaintenanceService;
use App\Models\Partner;
use App\Models\VehicleMaintenance;

echo "=== Merging Duplicate Data ===\n\n";

DB::beginTransaction();

try {
    // 1. Merge MaintenanceService duplicates
    echo "1. Merging MaintenanceService duplicates...\n";
    echo "-------------------------------------------\n";
    
    $serviceDuplicates = DB::table('maintenance_services')
        ->select('name', DB::raw('MIN(id) as keep_id'), DB::raw('GROUP_CONCAT(id) as all_ids'))
        ->groupBy('name')
        ->having(DB::raw('COUNT(*)'), '>', 1)
        ->get();

    foreach ($serviceDuplicates as $dup) {
        $allIds = explode(',', $dup->all_ids);
        $keepId = $dup->keep_id;
        $removeIds = array_filter($allIds, fn($id) => $id != $keepId);
        
        echo "Service: '{$dup->name}'\n";
        echo "  Keep ID: {$keepId}\n";
        echo "  Remove IDs: " . implode(', ', $removeIds) . "\n";
        
        // Update vehicle_maintenances to point to the kept record
        foreach ($removeIds as $removeId) {
            $updateCount = VehicleMaintenance::where('maintenance_service_id', $removeId)
                ->update(['maintenance_service_id' => $keepId]);
            echo "  - Updated {$updateCount} maintenances from ID {$removeId} to {$keepId}\n";
        }
        
        // Delete duplicate records
        MaintenanceService::whereIn('id', $removeIds)->delete();
        echo "  - Deleted " . count($removeIds) . " duplicate records\n\n";
    }

    // 2. Merge Partner duplicates
    echo "2. Merging Partner duplicates (type=maintenance)...\n";
    echo "-------------------------------------------\n";
    
    $partnerDuplicates = DB::table('partners')
        ->select('name', 'type', DB::raw('MIN(id) as keep_id'), DB::raw('GROUP_CONCAT(id) as all_ids'))
        ->where('type', 'maintenance')
        ->groupBy('name', 'type')
        ->having(DB::raw('COUNT(*)'), '>', 1)
        ->get();

    foreach ($partnerDuplicates as $dup) {
        $allIds = explode(',', $dup->all_ids);
        $keepId = $dup->keep_id;
        $removeIds = array_filter($allIds, fn($id) => $id != $keepId);
        
        echo "Partner: '{$dup->name}' (type: {$dup->type})\n";
        echo "  Keep ID: {$keepId}\n";
        echo "  Remove IDs: " . implode(', ', $removeIds) . "\n";
        
        // Update vehicle_maintenances to point to the kept record
        foreach ($removeIds as $removeId) {
            $updateCount = VehicleMaintenance::where('partner_id', $removeId)
                ->update(['partner_id' => $keepId]);
            echo "  - Updated {$updateCount} maintenances from ID {$removeId} to {$keepId}\n";
        }
        
        // Delete duplicate records
        Partner::whereIn('id', $removeIds)->delete();
        echo "  - Deleted " . count($removeIds) . " duplicate records\n\n";
    }

    DB::commit();
    
    echo "=== Merge Completed Successfully! ===\n";
    echo "All duplicates have been merged.\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n!!! ERROR !!!\n";
    echo "Transaction rolled back: " . $e->getMessage() . "\n";
}
