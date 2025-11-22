<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Vehicle;

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

if (!$vehicle) {
    echo "Vehicle not found!\n";
    exit;
}

echo "Vehicle: {$vehicle->license_plate} (ID: {$vehicle->id})\n";
echo "Has Owner: " . ($vehicle->hasOwner() ? 'Yes' : 'No') . "\n";

if ($vehicle->owner) {
    echo "Owner: {$vehicle->owner->full_name}\n";
}

echo "\n--- Maintenance Records ---\n";
$maintenances = $vehicle->vehicleMaintenances()->with(['maintenanceService', 'transaction'])->get();
echo "Total Maintenances: {$maintenances->count()}\n\n";

foreach ($maintenances as $m) {
    echo "Maintenance #{$m->id}:\n";
    echo "  Date: {$m->date}\n";
    echo "  Service: " . ($m->maintenanceService ? $m->maintenanceService->name : 'N/A') . "\n";
    echo "  Cost: " . number_format($m->cost, 0, ',', '.') . "đ\n";
    echo "  Transaction: " . ($m->transaction ? "ID {$m->transaction->id} - Category: {$m->transaction->category}" : 'NULL') . "\n";
    echo "\n";
}

echo "\n--- Recent Transactions ---\n";
$transactions = $vehicle->transactions()->orderBy('date', 'desc')->limit(10)->get();
echo "Total Transactions: {$transactions->count()}\n\n";

foreach ($transactions as $t) {
    echo "Transaction #{$t->id}:\n";
    echo "  Type: {$t->type}\n";
    echo "  Category: {$t->category}\n";
    echo "  Amount: " . number_format($t->amount, 0, ',', '.') . "đ\n";
    echo "  Note: {$t->note}\n";
    echo "  Maintenance ID: " . ($t->vehicle_maintenance_id ?? 'NULL') . "\n";
    echo "\n";
}
