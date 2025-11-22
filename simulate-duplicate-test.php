<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Location;
use App\Models\Patient;

echo "=== Simulating Duplicate Prevention in Real Usage ===\n\n";

DB::beginTransaction();

try {
    // Simulate user inputs with different cases
    $testInputs = [
        'Bệnh viện Đa khoa Trung ương',
        'bệnh viện đa khoa trung ương',
        'BỆNH VIỆN ĐA KHOA TRUNG ƯƠNG',
        '  Bệnh viện Đa khoa Trung ương  ',
    ];

    echo "1. Testing Location Creation\n";
    echo "------------------------------\n";
    
    $createdIds = [];
    foreach ($testInputs as $input) {
        $normalizedName = trim($input);
        
        // Use the same logic as IncidentController
        $location = Location::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)])
            ->first();
        
        if (!$location) {
            $location = Location::create([
                'name' => $normalizedName,
                'type' => 'from',
                'is_active' => true
            ]);
            echo "✓ CREATED: '{$input}' → ID {$location->id}\n";
        } else {
            echo "✓ REUSED:  '{$input}' → ID {$location->id} (existing: '{$location->name}')\n";
        }
        
        $createdIds[] = $location->id;
    }
    
    echo "\nResult: " . count(array_unique($createdIds)) . " unique location(s) created\n";
    if (count(array_unique($createdIds)) === 1) {
        echo "✅ SUCCESS: All variations use the same ID!\n";
    } else {
        echo "❌ FAILED: Multiple IDs created for same location\n";
    }
    
    echo "\n";
    
    // Test patient creation
    echo "2. Testing Patient Creation\n";
    echo "------------------------------\n";
    
    $patientInputs = [
        ['name' => 'Nguyễn Văn Test', 'phone' => '0987654321'],
        ['name' => 'nguyễn văn test', 'phone' => '0987654321'],
        ['name' => 'NGUYỄN VĂN TEST', 'phone' => '0987654321'],
        ['name' => '  Nguyễn Văn Test  ', 'phone' => '0987654321'],
    ];
    
    $patientIds = [];
    foreach ($patientInputs as $input) {
        $normalizedName = trim($input['name']);
        $normalizedPhone = trim($input['phone']);
        
        $query = Patient::whereRaw('LOWER(name) = ?', [mb_strtolower($normalizedName)]);
        $query->where('phone', $normalizedPhone);
        
        $patient = $query->first();
        
        if (!$patient) {
            $patient = Patient::create([
                'name' => $normalizedName,
                'phone' => $normalizedPhone,
            ]);
            echo "✓ CREATED: '{$input['name']}' / {$input['phone']} → ID {$patient->id}\n";
        } else {
            echo "✓ REUSED:  '{$input['name']}' / {$input['phone']} → ID {$patient->id} (existing: '{$patient->name}')\n";
        }
        
        $patientIds[] = $patient->id;
    }
    
    echo "\nResult: " . count(array_unique($patientIds)) . " unique patient(s) created\n";
    if (count(array_unique($patientIds)) === 1) {
        echo "✅ SUCCESS: All variations use the same ID!\n";
    } else {
        echo "❌ FAILED: Multiple IDs created for same patient\n";
    }
    
    DB::rollBack();
    echo "\n✓ Test completed (rolled back, no data saved)\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n✗ Error: " . $e->getMessage() . "\n";
}
