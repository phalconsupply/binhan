<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking for Duplicate Data in Incidents ===\n\n";

// 1. Check duplicate locations
echo "1. Checking LOCATIONS (from_location, to_location)\n";
echo "---------------------------------------------------\n";

$locationDuplicates = DB::table('locations')
    ->select('name', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id) as ids'))
    ->groupBy('name')
    ->having('count', '>', 1)
    ->get();

if ($locationDuplicates->isEmpty()) {
    echo "✓ No duplicate locations found\n\n";
} else {
    echo "Found {$locationDuplicates->count()} duplicate location names:\n\n";
    foreach ($locationDuplicates as $dup) {
        echo "Location: '{$dup->name}'\n";
        echo "  Count: {$dup->count}\n";
        echo "  IDs: {$dup->ids}\n";
        
        // Count usage
        $ids = explode(',', $dup->ids);
        foreach ($ids as $id) {
            $fromCount = DB::table('incidents')->where('from_location_id', $id)->count();
            $toCount = DB::table('incidents')->where('to_location_id', $id)->count();
            echo "  - ID {$id}: Used as FROM in {$fromCount} incidents, TO in {$toCount} incidents\n";
        }
        echo "\n";
    }
}

// 2. Check duplicate patients
echo "2. Checking PATIENTS\n";
echo "---------------------------------------------------\n";

$patientDuplicates = DB::table('patients')
    ->select('name', 'phone', DB::raw('COUNT(*) as count'), DB::raw('GROUP_CONCAT(id) as ids'))
    ->groupBy('name', 'phone')
    ->having('count', '>', 1)
    ->get();

if ($patientDuplicates->isEmpty()) {
    echo "✓ No duplicate patients found\n\n";
} else {
    echo "Found {$patientDuplicates->count()} duplicate patient records:\n\n";
    foreach ($patientDuplicates as $dup) {
        echo "Patient: '{$dup->name}' - Phone: " . ($dup->phone ?? 'NULL') . "\n";
        echo "  Count: {$dup->count}\n";
        echo "  IDs: {$dup->ids}\n";
        
        // Count usage
        $ids = explode(',', $dup->ids);
        foreach ($ids as $id) {
            $usageCount = DB::table('incidents')->where('patient_id', $id)->count();
            echo "  - ID {$id}: Used in {$usageCount} incidents\n";
        }
        echo "\n";
    }
}

// 3. Summary
echo "=== Summary ===\n";
echo "Total location duplicates: {$locationDuplicates->count()}\n";
echo "Total patient duplicates: {$patientDuplicates->count()}\n";

if ($locationDuplicates->count() > 0 || $patientDuplicates->count() > 0) {
    echo "\n⚠️ WARNING: Duplicates found! This can happen when:\n";
    echo "- Users type names with different capitalization\n";
    echo "- Spaces or special characters differ\n";
    echo "- Multiple users create the same record simultaneously\n";
}
