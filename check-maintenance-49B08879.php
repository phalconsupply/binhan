<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Vehicle;

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

if (!$vehicle) {
    echo "Vehicle 49B08879 not found\n";
    exit;
}

echo "Vehicle ID: {$vehicle->id}\n";
echo "Vehicle: {$vehicle->license_plate}\n\n";

// Count VehicleMaintenance records
$maintenancesCount = $vehicle->vehicleMaintenances()->count();
echo "Total VehicleMaintenance records: {$maintenancesCount}\n";

// Count Transactions with vehicle_maintenance_id
$maintenanceTransactionsCount = $vehicle->transactions()
    ->whereNotNull('vehicle_maintenance_id')
    ->count();
echo "Total Transactions with vehicle_maintenance_id: {$maintenanceTransactionsCount}\n\n";

// List all maintenances
echo "=== All VehicleMaintenance Records ===\n";
$maintenances = $vehicle->vehicleMaintenances()
    ->with(['maintenanceService'])
    ->orderBy('date', 'desc')
    ->get();

foreach ($maintenances as $m) {
    $hasTransaction = $m->transaction ? 'YES' : 'NO';
    $serviceName = $m->maintenanceService ? $m->maintenanceService->name : 'N/A';
    echo sprintf(
        "%s | %s | %s VND | Has Transaction: %s\n",
        $m->date,
        $serviceName,
        number_format($m->cost),
        $hasTransaction
    );
}

echo "\n=== Maintenance Transactions (grouped by vehicle_maintenance_id) ===\n";
$maintenanceTransactions = $vehicle->transactions()
    ->with(['vehicleMaintenance.maintenanceService'])
    ->whereNotNull('vehicle_maintenance_id')
    ->orderBy('date', 'desc')
    ->get();

$grouped = $maintenanceTransactions->groupBy('vehicle_maintenance_id');
foreach ($grouped as $maintenanceId => $transactions) {
    $first = $transactions->first();
    $serviceName = $first->vehicleMaintenance && $first->vehicleMaintenance->maintenanceService 
        ? $first->vehicleMaintenance->maintenanceService->name 
        : 'N/A';
    $totalExpense = $transactions->where('type', 'chi')->sum('amount');
    
    echo sprintf(
        "Maintenance ID %d | %s | %s | %d transactions | Total: %s VND\n",
        $maintenanceId,
        $first->date,
        $serviceName,
        $transactions->count(),
        number_format($totalExpense)
    );
}
