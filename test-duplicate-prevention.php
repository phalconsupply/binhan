<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Location;
use App\Models\Patient;

echo "=== Testing Duplicate Prevention Logic ===\n\n";

// Test 1: Location case-insensitive search
echo "1. Testing Location Search (Case-Insensitive)\n";
echo "----------------------------------------------\n";

$testCases = [
    'Bệnh viện A',
    'bệnh viện a',
    'BỆNH VIỆN A',
    '  Bệnh viện A  ', // with spaces
];

foreach ($testCases as $testName) {
    $normalizedName = trim($testName);
    
    $location = Location::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)])
        ->first();
    
    if ($location) {
        echo "✓ Found existing: '{$testName}' → ID {$location->id} (name: '{$location->name}')\n";
    } else {
        echo "✗ Not found: '{$testName}' → Would create new\n";
    }
}

echo "\n";

// Test 2: Patient search
echo "2. Testing Patient Search (Case-Insensitive)\n";
echo "----------------------------------------------\n";

$patientTests = [
    ['name' => 'Nguyễn Văn A', 'phone' => '0123456789'],
    ['name' => 'nguyễn văn a', 'phone' => '0123456789'],
    ['name' => 'NGUYỄN VĂN A', 'phone' => '0123456789'],
    ['name' => 'Nguyễn Văn A', 'phone' => null],
];

foreach ($patientTests as $test) {
    $normalizedName = trim($test['name']);
    $normalizedPhone = !empty($test['phone']) ? trim($test['phone']) : null;
    
    $query = Patient::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)]);
    
    if ($normalizedPhone) {
        $query->where('phone', $normalizedPhone);
    } else {
        $query->whereNull('phone');
    }
    
    $patient = $query->first();
    
    if ($patient) {
        echo "✓ Found: '{$test['name']}' / " . ($test['phone'] ?? 'NULL') . " → ID {$patient->id}\n";
    } else {
        echo "✗ Not found: '{$test['name']}' / " . ($test['phone'] ?? 'NULL') . " → Would create new\n";
    }
}

echo "\n";

// Test 3: Check current database
echo "3. Current Database Status\n";
echo "----------------------------------------------\n";
echo "Total Locations: " . Location::count() . "\n";
echo "Total Patients: " . Patient::count() . "\n";

// Check for potential duplicates (names that differ only in case)
$locations = Location::all();
$locationsByLower = [];
foreach ($locations as $loc) {
    $key = mb_strtolower($loc->name);
    if (!isset($locationsByLower[$key])) {
        $locationsByLower[$key] = [];
    }
    $locationsByLower[$key][] = $loc;
}

$duplicates = array_filter($locationsByLower, function($locs) {
    return count($locs) > 1;
});

if (count($duplicates) > 0) {
    echo "\n⚠️ Found " . count($duplicates) . " potential location duplicates (differ only in case):\n";
    foreach ($duplicates as $key => $locs) {
        echo "  '{$key}': ";
        echo implode(', ', array_map(function($l) { return "'{$l->name}' (ID: {$l->id})"; }, $locs));
        echo "\n";
    }
} else {
    echo "\n✓ No case-sensitive duplicates found in locations\n";
}

echo "\n=== Test Complete ===\n";
