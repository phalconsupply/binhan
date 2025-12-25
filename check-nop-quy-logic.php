<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;

echo "=== KIEM TRA GIAO DICH NOP QUY GD20251212-0772 ===\n\n";

$tx = Transaction::where('code', 'GD20251212-0772')->first();

if (!$tx) {
    echo "Khong tim thay giao dich!\n";
    exit;
}

echo "Ma GD: " . $tx->code . "\n";
echo "Loai: " . $tx->type . "\n";
echo "Xe: " . $tx->vehicle->license_plate . "\n";
echo "Xe co chu: " . ($tx->vehicle->hasOwner() ? 'CO' : 'KHONG') . "\n";
echo "So tien: " . number_format($tx->amount, 0, ',', '.') . " VND\n\n";

echo "TRACKING HIEN TAI:\n";
echo "From account: " . ($tx->from_account ?? 'NULL') . "\n";
echo "To account: " . ($tx->to_account ?? 'NULL') . "\n";
echo "To balance after: " . number_format($tx->to_balance_after ?? 0, 0, ',', '.') . " VND\n\n";

echo "LOGIC DUNG:\n";
if ($tx->vehicle->hasOwner()) {
    echo "✓ Xe CO chu -> Tien phai vao SO DU XE\n";
    echo "  From: customer\n";
    echo "  To: vehicle_{$tx->vehicle_id}\n";
    echo "  So du xe tang: +" . number_format($tx->amount, 0, ',', '.') . " VND\n";
} else {
    echo "✓ Xe KHONG chu -> Tien vao CONG TY\n";
    echo "  From: customer\n";
    echo "  To: company_fund\n";
    echo "  So du cong ty tang: +" . number_format($tx->amount, 0, ',', '.') . " VND\n";
}

echo "\n=== KET LUAN ===\n";
if ($tx->to_account === 'company_fund' && $tx->vehicle->hasOwner()) {
    echo "❌ SAI! Xe co chu ma tien lai vao cong ty\n";
    echo "   Can sua AccountBalanceService->determineAccounts()\n";
} else {
    echo "✓ Dung!\n";
}
