<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;

echo "🔍 Kiểm tra giao dịch #754\n\n";

$transaction = Transaction::find(754);

if (!$transaction) {
    echo "❌ Không tìm thấy giao dịch #754\n";
    exit;
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Thông tin giao dịch #754:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Mã GD: {$transaction->code}\n";
echo "Loại: {$transaction->type}\n";
echo "Số tiền: " . number_format($transaction->amount) . "đ\n";
echo "vehicle_id: " . ($transaction->vehicle_id ?? 'NULL') . "\n";
echo "incident_id: " . ($transaction->incident_id ?? 'NULL') . "\n";
echo "Ghi chú: {$transaction->note}\n";
echo "Ngày: " . $transaction->date->format('d/m/Y') . "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Check if it's counted in revenue
$totalRevenue = Transaction::revenue()->sum('amount');
echo "📊 Tổng thu (ALL transactions type='thu'): " . number_format($totalRevenue) . "đ\n";

$revenueWithVehicle = Transaction::revenue()->whereNotNull('vehicle_id')->sum('amount');
echo "📊 Tổng thu (có vehicle_id): " . number_format($revenueWithVehicle) . "đ\n";

$revenueWithoutVehicle = Transaction::revenue()->whereNull('vehicle_id')->sum('amount');
echo "📊 Tổng thu (không có vehicle_id = quỹ công ty): " . number_format($revenueWithoutVehicle) . "đ\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✓ Giao dịch #754 với vehicle_id=" . ($transaction->vehicle_id ?? 'NULL') . "\n";
echo "✓ Có được tính vào tổng thu: " . ($transaction->type == 'thu' ? 'CÓ' : 'KHÔNG') . "\n";
echo "✓ Có được tính vào quỹ công ty: " . ($transaction->vehicle_id === null ? 'CÓ' : 'KHÔNG') . "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
