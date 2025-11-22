<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\VehicleMaintenance;

echo "=== Creating Transactions for Existing Maintenance Records ===\n\n";

DB::beginTransaction();

try {
    // Get all maintenances without transactions
    $maintenances = VehicleMaintenance::with(['vehicle', 'maintenanceService', 'partner'])
        ->whereDoesntHave('transaction')
        ->get();
    
    echo "Found {$maintenances->count()} maintenance records without transactions\n\n";
    
    $created = 0;
    $skipped = 0;
    
    foreach ($maintenances as $maintenance) {
        try {
            $transaction = $maintenance->createTransaction();
            
            if ($transaction) {
                $vehicle = $maintenance->vehicle;
                $hasOwner = $vehicle->hasOwner() ? 'Xe chủ riêng' : 'Công ty';
                $category = $transaction->category;
                
                echo "✓ Created transaction #{$transaction->id} for maintenance #{$maintenance->id}\n";
                echo "  Vehicle: {$vehicle->license_plate} ({$hasOwner})\n";
                echo "  Category: {$category}\n";
                echo "  Amount: " . number_format($transaction->amount, 0, ',', '.') . "đ\n";
                echo "  Note: {$transaction->note}\n\n";
                
                $created++;
            } else {
                echo "⚠ Skipped maintenance #{$maintenance->id} (already has transaction)\n\n";
                $skipped++;
            }
        } catch (\Exception $e) {
            echo "✗ Error creating transaction for maintenance #{$maintenance->id}: {$e->getMessage()}\n\n";
            $skipped++;
        }
    }
    
    DB::commit();
    
    echo "=== Summary ===\n";
    echo "Created: {$created} transactions\n";
    echo "Skipped: {$skipped} records\n";
    echo "\nAll transactions created successfully!\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n!!! ERROR !!!\n";
    echo "Transaction rolled back: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
