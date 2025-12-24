<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vehicle;
use App\Models\Transaction;

$vehicle = Vehicle::find(4);

echo "Test query:\n";
echo "Transaction::where('vehicle_id', 4)->count() = " . Transaction::where('vehicle_id', 4)->count() . "\n";
echo "\$vehicle->transactions()->count() = " . $vehicle->transactions()->count() . "\n";
echo "\$vehicle->transactions->count() = " . $vehicle->transactions->count() . "\n";

echo "\nGiao dich vay:\n";
$borrowed = Transaction::where('vehicle_id', 4)->where('type', 'vay_cong_ty')->get();
foreach ($borrowed as $b) {
    echo "  ID: {$b->id}, Amount: {$b->amount}, Date: {$b->date}\n";
}

echo "\nDung relationship:\n";
$borrowed2 = $vehicle->transactions()->where('type', 'vay_cong_ty')->get();
foreach ($borrowed2 as $b) {
    echo "  ID: {$b->id}, Amount: {$b->amount}, Date: {$b->date}\n";
}

echo "\nDung scope:\n";
$borrowed3 = $vehicle->transactions()->borrowFromCompany()->get();
foreach ($borrowed3 as $b) {
    echo "  ID: {$b->id}, Amount: {$b->amount}, Date: {$b->date}\n";
}
