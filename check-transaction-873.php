<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;

echo "🔍 Kiểm tra giao dịch GD20251224-0873\n\n";

$transaction = Transaction::where('code', 'GD20251224-0873')->first();

if (!$transaction) {
    echo "❌ Không tìm thấy giao dịch\n";
    exit;
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 Thông tin giao dịch:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "ID: #{$transaction->id}\n";
echo "Mã: {$transaction->code}\n";
echo "Loại: {$transaction->type} ({$transaction->type_label})\n";
echo "Số tiền: " . number_format($transaction->amount) . "đ\n";
echo "vehicle_id: " . ($transaction->vehicle_id ?? 'NULL') . "\n";
echo "incident_id: " . ($transaction->incident_id ?? 'NULL') . "\n";
echo "Ghi chú: {$transaction->note}\n";
echo "Ngày: " . $transaction->date->format('d/m/Y H:i') . "\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔍 Phân tích:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

if ($transaction->type === 'tra_cong_ty') {
    echo "✓ Đây là giao dịch TRẢ NỢ cho công ty\n";
    echo "✓ Loại giao dịch này KHÔNG phải 'thu', 'chi', hay 'du_kien_chi'\n\n";
    
    // Check if there's a corresponding thu transaction
    echo "🔍 Tìm giao dịch THU tương ứng...\n";
    
    // Search for related thu transaction created around same time
    $relatedThu = Transaction::where('type', 'thu')
        ->whereNull('vehicle_id')
        ->where('amount', $transaction->amount)
        ->where('date', '>=', $transaction->date->subMinutes(5))
        ->where('date', '<=', $transaction->date->addMinutes(5))
        ->get();
    
    if ($relatedThu->isEmpty()) {
        echo "❌ KHÔNG TÌM THẤY giao dịch THU tương ứng!\n\n";
        echo "⚠️  VẤN ĐỀ: Khi xe trả nợ thủ công, cần tạo 2 giao dịch:\n";
        echo "   1. tra_cong_ty (trừ tiền từ xe) - ĐÃ CÓ\n";
        echo "   2. thu (cộng tiền vào công ty) - THIẾU!\n\n";
        
        echo "💡 Giải pháp: Cần sửa VehicleController@repayCompany\n";
        echo "   để tạo thêm giao dịch 'thu' cho công ty\n";
    } else {
        echo "✓ Tìm thấy " . $relatedThu->count() . " giao dịch THU tương ứng:\n\n";
        foreach ($relatedThu as $thu) {
            echo "  #{$thu->id} - {$thu->code}\n";
            echo "  Số tiền: " . number_format($thu->amount) . "đ\n";
            echo "  Ghi chú: {$thu->note}\n";
            echo "  ---\n";
        }
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 Kiểm tra logic tính lợi nhuận:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$totalRevenue = Transaction::where('type', 'thu')->sum('amount');
$totalExpense = Transaction::where('type', 'chi')->sum('amount');
$totalPlanned = Transaction::where('type', 'du_kien_chi')->sum('amount');

echo "Tổng thu (type='thu'): " . number_format($totalRevenue) . "đ\n";
echo "Tổng chi (type='chi'): " . number_format($totalExpense) . "đ\n";
echo "Dự kiến chi: " . number_format($totalPlanned) . "đ\n";
echo "Lợi nhuận = Thu - Chi - Dự kiến = " . number_format($totalRevenue - $totalExpense - $totalPlanned) . "đ\n";

echo "\n⚠️  Giao dịch tra_cong_ty KHÔNG được tính vào thu/chi/lợi nhuận\n";
echo "   → Cần có giao dịch 'thu' tương ứng để tính vào lợi nhuận!\n";
