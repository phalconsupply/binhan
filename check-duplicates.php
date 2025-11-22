<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\MaintenanceService;
use App\Models\Partner;
use App\Models\VehicleMaintenance;

echo "=== Checking for Duplicate Data ===\n\n";

// Check MaintenanceService duplicates
echo "1. MaintenanceService Duplicates:\n";
echo "-----------------------------------\n";
$serviceDuplicates = DB::table('maintenance_services')
    ->select('name', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id) as ids'))
    ->groupBy('name')
    ->having('count', '>', 1)
    ->get();

if ($serviceDuplicates->count() > 0) {
    foreach ($serviceDuplicates as $dup) {
        echo "Service: '{$dup->name}' - {$dup->count} records (IDs: {$dup->ids})\n";
        
        // Get all records
        $ids = explode(',', $dup->ids);
        $services = MaintenanceService::whereIn('id', $ids)->get();
        foreach ($services as $service) {
            $usageCount = VehicleMaintenance::where('maintenance_service_id', $service->id)->count();
            echo "  - ID {$service->id}: Used in {$usageCount} maintenances\n";
        }
        echo "\n";
    }
} else {
    echo "No duplicates found.\n\n";
}

// Check Partner duplicates (maintenance type)
echo "2. Partner Duplicates (type=maintenance):\n";
echo "-----------------------------------\n";
$partnerDuplicates = DB::table('partners')
    ->select('name', 'type', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id) as ids'))
    ->where('type', 'maintenance')
    ->groupBy('name', 'type')
    ->having('count', '>', 1)
    ->get();

if ($partnerDuplicates->count() > 0) {
    foreach ($partnerDuplicates as $dup) {
        echo "Partner: '{$dup->name}' (type: {$dup->type}) - {$dup->count} records (IDs: {$dup->ids})\n";
        
        // Get all records
        $ids = explode(',', $dup->ids);
        $partners = Partner::whereIn('id', $ids)->get();
        foreach ($partners as $partner) {
            $usageCount = VehicleMaintenance::where('partner_id', $partner->id)->count();
            echo "  - ID {$partner->id}: Used in {$usageCount} maintenances\n";
        }
        echo "\n";
    }
} else {
    echo "No duplicates found.\n\n";
}

// Summary
echo "=== Summary ===\n";
echo "Total MaintenanceService duplicates: " . $serviceDuplicates->count() . "\n";
echo "Total Partner duplicates: " . $partnerDuplicates->count() . "\n";
