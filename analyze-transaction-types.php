<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

if (!$vehicle) {
    echo "Vehicle not found\n";
    exit;
}

echo "=== Các loại TYPE trong transactions của xe {$vehicle->license_plate} ===\n\n";

$types = Transaction::where('vehicle_id', $vehicle->id)
    ->select('type', \DB::raw('COUNT(*) as count'), \DB::raw('SUM(amount) as total'))
    ->groupBy('type')
    ->orderBy('type')
    ->get();

foreach ($types as $type) {
    echo sprintf(
        "Type: %-20s | Count: %3d | Total: %s\n",
        $type->type,
        $type->count,
        number_format($type->total)
    );
}

echo "\n=== Các CATEGORY trong transactions ===\n\n";

$categories = Transaction::where('vehicle_id', $vehicle->id)
    ->select('category', 'type', \DB::raw('COUNT(*) as count'), \DB::raw('SUM(amount) as total'))
    ->groupBy('category', 'type')
    ->orderBy('type')
    ->orderBy('category')
    ->get();

foreach ($categories as $cat) {
    $category = $cat->category ?? '(null)';
    echo sprintf(
        "Type: %-20s | Category: %-30s | Count: %3d | Total: %s\n",
        $cat->type,
        $category,
        $cat->count,
        number_format($cat->total)
    );
}

echo "\n=== Ví dụ transaction type='vay_cong_ty' ===\n\n";
$borrowSamples = Transaction::where('vehicle_id', $vehicle->id)
    ->where('type', 'vay_cong_ty')
    ->take(3)
    ->get();

foreach ($borrowSamples as $t) {
    echo sprintf(
        "ID: %d | Date: %s | Type: %s | Category: %s | Amount: %s | Note: %s\n",
        $t->id,
        $t->date->format('Y-m-d'),
        $t->type,
        $t->category ?? '(null)',
        number_format($t->amount),
        $t->note
    );
}
