<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\VehicleMaintenance;

echo "=== Tạo Transactions cho VehicleMaintenance còn thiếu ===\n\n";

$missingTransactions = VehicleMaintenance::whereDoesntHave('transaction')
    ->with(['maintenanceService', 'vehicle'])
    ->orderBy('date', 'desc')
    ->get();

$totalMissing = $missingTransactions->count();
echo "Tổng số VehicleMaintenance chưa có transaction: {$totalMissing}\n\n";

if ($totalMissing === 0) {
    echo "✓ Tất cả VehicleMaintenance đã có transaction!\n";
    exit;
}

$created = 0;
$errors = 0;

foreach ($missingTransactions as $m) {
    $serviceName = $m->maintenanceService ? $m->maintenanceService->name : 'N/A';
    $vehicle = $m->vehicle->license_plate;
    
    echo "Processing ID {$m->id} | {$vehicle} | {$m->date} | {$serviceName} | " . number_format($m->cost) . " VND ... ";
    
    try {
        $transaction = $m->createTransaction();
        if ($transaction) {
            echo "✓ Created Transaction #{$transaction->id}\n";
            $created++;
        } else {
            echo "✗ createTransaction() returned null\n";
            $errors++;
        }
    } catch (\Exception $e) {
        echo "✗ Error: {$e->getMessage()}\n";
        $errors++;
    }
}

echo "\n=== Kết quả ===\n";
echo "✓ Tạo thành công: {$created}\n";
echo "✗ Lỗi: {$errors}\n";
echo "Tổng: {$totalMissing}\n";
