<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;

echo "🔍 Kiểm tra mã giao dịch sau khôi phục\n\n";

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

// Check recent transactions
$recentTransactions = Transaction::where('vehicle_id', $vehicle->id)
    ->whereNotNull('incident_id')
    ->orderBy('id', 'desc')
    ->limit(20)
    ->get();

echo "📊 20 giao dịch chuyến đi gần nhất:\n\n";

$withoutCode = 0;
$withCode = 0;

foreach ($recentTransactions as $trans) {
    $codeStatus = $trans->code ? "✓ {$trans->code}" : "✗ KHÔNG CÓ MÃ";
    echo "#{$trans->id} - {$codeStatus} - Chuyến #{$trans->incident_id} - {$trans->type_label}\n";
    
    if ($trans->code) {
        $withCode++;
    } else {
        $withoutCode++;
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📈 Thống kê:\n";
echo "   - Có mã: {$withCode}\n";
echo "   - Không có mã: {$withoutCode}\n";

if ($withoutCode > 0) {
    echo "\n⚠️ CẦN CHẠY SCRIPT TẠO MÃ CHO CÁC GIAO DỊCH!\n";
    echo "   Chạy: php generate-transaction-codes.php\n";
}
