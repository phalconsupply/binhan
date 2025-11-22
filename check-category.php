<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;

$t = Transaction::find(49);

if (!$t) {
    echo "Transaction not found!\n";
    exit;
}

echo "Transaction #49:\n";
echo "Category: [{$t->category}]\n";
echo "Length: " . strlen($t->category) . "\n";
echo "Hex: " . bin2hex($t->category) . "\n";
echo "Match test 1: " . ($t->category == 'bảo_trì_xe_chủ_riêng' ? 'YES' : 'NO') . "\n";
echo "Match test 2: " . ($t->category === 'bảo_trì_xe_chủ_riêng' ? 'YES' : 'NO') . "\n";

// Test the condition in blade
$maintenance = \App\Models\VehicleMaintenance::find(5);
echo "\nMaintenance #5:\n";
echo "Has transaction: " . ($maintenance->transaction ? 'YES' : 'NO') . "\n";
if ($maintenance->transaction) {
    echo "Transaction category: [{$maintenance->transaction->category}]\n";
    echo "Condition result: " . ($maintenance->transaction->category == 'bảo_trì_xe_chủ_riêng' ? 'MATCH - Should show 🏠 Xe chủ riêng' : 'NO MATCH') . "\n";
}
