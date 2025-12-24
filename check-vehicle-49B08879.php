<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Vehicle;
use App\Models\Incident;
use App\Models\Transaction;

echo "🔍 Kiểm tra xe 49B08879\n\n";

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

if (!$vehicle) {
    echo "❌ Không tìm thấy xe 49B08879\n";
    exit;
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 Thông tin xe:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ID: {$vehicle->id}\n";
echo "Biển số: {$vehicle->license_plate}\n";
echo "Có chủ: " . ($vehicle->hasOwner() ? 'CÓ' : 'KHÔNG') . "\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 Các chuyến đi của xe:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$incidents = Incident::where('vehicle_id', $vehicle->id)->get();

if ($incidents->isEmpty()) {
    echo "❌ Không có chuyến đi nào\n";
} else {
    foreach ($incidents as $incident) {
        echo "\nChuyến #{$incident->id} - " . $incident->date->format('d/m/Y') . "\n";
        echo "  Bệnh nhân: " . ($incident->patient->name ?? 'N/A') . "\n";
        
        $incidentTransactions = $incident->transactions;
        echo "  Giao dịch: {$incidentTransactions->count()} giao dịch\n";
        
        if ($incidentTransactions->isEmpty()) {
            echo "  ⚠️ CHƯA CÓ GIAO DỊCH\n";
        } else {
            $revenue = $incidentTransactions->where('type', 'thu')->sum('amount');
            $expense = $incidentTransactions->where('type', 'chi')->sum('amount');
            echo "  Thu: " . number_format($revenue) . "đ\n";
            echo "  Chi: " . number_format($expense) . "đ\n";
            echo "  Lợi nhuận: " . number_format($revenue - $expense) . "đ\n";
        }
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 Giao dịch của xe (không tính chuyến đi):\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$vehicleTransactions = Transaction::where('vehicle_id', $vehicle->id)
    ->whereNull('incident_id')
    ->get();

echo "Tổng: {$vehicleTransactions->count()} giao dịch\n\n";

$groupedByType = $vehicleTransactions->groupBy('type');

foreach ($groupedByType as $type => $transactions) {
    $total = $transactions->sum('amount');
    echo "  {$type}: {$transactions->count()} giao dịch = " . number_format($total) . "đ\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 Chi tiết 10 giao dịch gần nhất:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$recentTransactions = Transaction::where('vehicle_id', $vehicle->id)
    ->orderBy('date', 'desc')
    ->orderBy('id', 'desc')
    ->limit(10)
    ->get();

foreach ($recentTransactions as $trans) {
    $incidentInfo = $trans->incident_id ? " [Chuyến #{$trans->incident_id}]" : "";
    echo "#{$trans->id} - {$trans->code} - {$trans->date->format('d/m/Y')}\n";
    echo "  Loại: {$trans->type_label}{$incidentInfo}\n";
    echo "  Số tiền: " . number_format($trans->amount) . "đ\n";
    echo "  Ghi chú: " . ($trans->note ?? 'N/A') . "\n";
    echo "  ---\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "💡 Gợi ý:\n";
echo "  - Nếu chuyến đi chưa có giao dịch → Cần tạo giao dịch thu/chi cho chuyến\n";
echo "  - Nếu đã xóa giao dịch trước đó → Cần khôi phục từ backup\n";
