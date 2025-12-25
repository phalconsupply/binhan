<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Vehicle;
use App\Models\VehicleMaintenance;

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

if (!$vehicle) {
    echo "Vehicle 49B08879 not found\n";
    exit;
}

echo "=== VehicleMaintenance KHÔNG có Transaction ===\n\n";

$missingTransactions = VehicleMaintenance::where('vehicle_id', $vehicle->id)
    ->whereDoesntHave('transaction')
    ->with(['maintenanceService', 'user'])
    ->orderBy('date', 'desc')
    ->get();

foreach ($missingTransactions as $m) {
    $serviceName = $m->maintenanceService ? $m->maintenanceService->name : 'N/A';
    $userName = $m->user ? $m->user->name : 'N/A';
    
    echo sprintf(
        "ID: %d | Date: %s | Service: %s | Cost: %s | Created by: %s | Created at: %s\n",
        $m->id,
        $m->date,
        $serviceName,
        number_format($m->cost),
        $userName,
        $m->created_at
    );
}

echo "\n\nTổng: " . $missingTransactions->count() . " bảo trì chưa có transaction\n";

// Test createTransaction() cho một record
if ($missingTransactions->isNotEmpty()) {
    $first = $missingTransactions->first();
    echo "\n=== Test createTransaction() cho ID {$first->id} ===\n";
    
    try {
        $transaction = $first->createTransaction();
        if ($transaction) {
            echo "✓ Transaction created successfully\n";
            echo "  Transaction ID: {$transaction->id}\n";
            echo "  Type: {$transaction->type}\n";
            echo "  Category: {$transaction->category}\n";
            echo "  Amount: " . number_format($transaction->amount) . "\n";
        } else {
            echo "✗ createTransaction() returned null\n";
        }
    } catch (\Exception $e) {
        echo "✗ Error: {$e->getMessage()}\n";
    }
}
