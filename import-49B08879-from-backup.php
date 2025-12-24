<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$backupFile = __DIR__ . '/database/backup_before_clean_49B08879_20251225_012250.sql';

if (!file_exists($backupFile)) {
    echo "❌ Backup file not found!\n";
    exit(1);
}

echo "📁 Reading backup file...\n";
$sql = file_get_contents($backupFile);

// Extract incidents data for vehicle_id = 4
preg_match('/INSERT INTO `incidents` VALUES (.+?);/s', $sql, $incidentsMatch);
if (!$incidentsMatch) {
    echo "❌ No incidents data found\n";
    exit(1);
}

// Extract transactions data for vehicle_id = 4  
preg_match('/INSERT INTO `transactions` VALUES (.+?);/s', $sql, $transactionsMatch);
if (!$transactionsMatch) {
    echo "❌ No transactions data found\n";
    exit(1);
}

// Parse incidents
$incidentsData = $incidentsMatch[1];
// Split by ),( to get individual rows
$incidentRows = preg_split('/\),\(/', $incidentsData);
$incidentRows[0] = ltrim($incidentRows[0], '(');
$incidentRows[count($incidentRows)-1] = rtrim($incidentRows[count($incidentRows)-1], ')');

$vehicle4Incidents = [];
foreach ($incidentRows as $row) {
    // Parse row to check vehicle_id
    $fields = str_getcsv($row);
    if (count($fields) >= 3 && $fields[2] == '4') { // vehicle_id is 3rd field
        $vehicle4Incidents[] = '(' . $row . ')';
    }
}

echo "📋 Found " . count($vehicle4Incidents) . " incidents for vehicle 49B08879\n";

// Parse transactions
$transactionsData = $transactionsMatch[1];
$transactionRows = preg_split('/\),\(/', $transactionsData);
$transactionRows[0] = ltrim($transactionRows[0], '(');
$transactionRows[count($transactionRows)-1] = rtrim($transactionRows[count($transactionRows)-1], ')');

$vehicle4Transactions = [];
foreach ($transactionRows as $row) {
    // Parse row to check vehicle_id (5th field)
    $fields = str_getcsv($row);
    if (count($fields) >= 5 && $fields[4] == '4') {
        $vehicle4Transactions[] = '(' . $row . ')';
    }
}

echo "💰 Found " . count($vehicle4Transactions) . " transactions for vehicle 49B08879\n";

// Confirm before import
echo "\n⚠️  This will import:\n";
echo "   - " . count($vehicle4Incidents) . " incidents\n";
echo "   - " . count($vehicle4Transactions) . " transactions\n";
echo "\nContinue? (yes/no): ";
$handle = fopen("php://stdin", "r");
$confirm = trim(fgets($handle));

if (strtolower($confirm) !== 'yes') {
    echo "❌ Import cancelled\n";
    exit(0);
}

DB::beginTransaction();

try {
    // Import incidents
    if (!empty($vehicle4Incidents)) {
        $incidentsSql = "INSERT INTO `incidents` VALUES " . implode(',', $vehicle4Incidents);
        DB::statement($incidentsSql);
        echo "✅ Imported " . count($vehicle4Incidents) . " incidents\n";
    }
    
    // Import transactions
    if (!empty($vehicle4Transactions)) {
        $transactionsSql = "INSERT INTO `transactions` VALUES " . implode(',', $vehicle4Transactions);
        DB::statement($transactionsSql);
        echo "✅ Imported " . count($vehicle4Transactions) . " transactions\n";
    }
    
    DB::commit();
    echo "\n🎉 Import completed successfully!\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Import failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
