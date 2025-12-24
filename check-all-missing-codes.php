<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;

echo "🔍 Kiểm tra TẤT CẢ giao dịch không có mã\n\n";

$withoutCode = Transaction::whereNull('code')
    ->orWhere('code', '')
    ->orderBy('id', 'desc')
    ->get();

echo "📊 Tìm thấy {$withoutCode->count()} giao dịch không có mã\n\n";

if ($withoutCode->count() > 0) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📋 Chi tiết 20 giao dịch đầu:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    foreach ($withoutCode->take(20) as $trans) {
        echo "#{$trans->id} - {$trans->date->format('d/m/Y')} - {$trans->type_label}\n";
        echo "  Số tiền: " . number_format($trans->amount) . "đ\n";
        echo "  Ghi chú: " . ($trans->note ?? 'N/A') . "\n";
        echo "  ---\n";
    }
    
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "⚠️ CẦN TẠO MÃ CHỈ ĐỊNH!\n";
    echo "   Chạy lệnh sau:\n";
    echo "   php generate-transaction-codes.php\n";
} else {
    echo "✅ TẤT CẢ giao dịch đều có mã!\n";
}

// Double check recent transactions
$recentAll = Transaction::orderBy('id', 'desc')->limit(10)->get();

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 10 giao dịch mới nhất (tất cả xe):\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

foreach ($recentAll as $trans) {
    $code = $trans->code ?? '❌ KHÔNG CÓ';
    echo "#{$trans->id} - {$code} - {$trans->type_label}\n";
}
