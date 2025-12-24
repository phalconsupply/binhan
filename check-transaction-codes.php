<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;

echo "🔍 Kiểm tra mã giao dịch\n\n";

// Get some recent transactions
$transactions = Transaction::orderBy('id', 'desc')
    ->limit(10)
    ->get();

echo "📊 10 giao dịch gần nhất:\n\n";

foreach ($transactions as $transaction) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "ID: #{$transaction->id}\n";
    echo "Mã GD: {$transaction->code}\n";
    echo "Loại: {$transaction->type_label}\n";
    echo "Số tiền: " . number_format($transaction->amount) . "đ\n";
    echo "Ngày: " . $transaction->date->format('d/m/Y') . "\n";
    echo "Ghi chú: " . ($transaction->note ?? 'N/A') . "\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Test search by code
$testCode = $transactions->first()->code;
echo "🔍 Kiểm tra tìm kiếm theo mã: {$testCode}\n";

$found = Transaction::where('code', 'like', "%{$testCode}%")->count();
echo "✓ Tìm thấy {$found} giao dịch\n\n";

// Count transactions with code
$withCode = Transaction::whereNotNull('code')->where('code', '!=', '')->count();
$total = Transaction::count();

echo "📈 Thống kê:\n";
echo "   - Tổng số giao dịch: {$total}\n";
echo "   - Có mã: {$withCode}\n";
echo "   - Chưa có mã: " . ($total - $withCode) . "\n";
