<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Services\AccountBalanceService;

echo "=================================================================\n";
echo "UPDATE BALANCE CHO TẤT CẢ GIAO DỊCH\n";
echo "=================================================================\n\n";

$total = Transaction::count();

echo "Tổng số giao dịch: {$total}\n";
echo "Bắt đầu update balance...\n\n";

// Lấy tất cả giao dịch theo thứ tự thời gian
$transactions = Transaction::orderBy('date')->orderBy('id')->get();

$processed = 0;
$updated = 0;
$errors = 0;

foreach ($transactions as $tx) {
    try {
        // Gọi AccountBalanceService để tính và update balance
        AccountBalanceService::updateTransactionBalances($tx);
        
        $processed++;
        $updated++;
        
        if ($processed % 50 == 0) {
            echo "✓ Đã xử lý: {$processed}/{$total}\n";
        }
    } catch (\Exception $e) {
        $errors++;
        echo "✗ Lỗi GD #{$tx->id} ({$tx->code}): " . $e->getMessage() . "\n";
    }
}

echo "\n=================================================================\n";
echo "✓ HOÀN THÀNH!\n";
echo "=================================================================\n";
echo "Đã xử lý:  {$processed} giao dịch\n";
echo "Updated:   {$updated}\n";
echo "Lỗi:       {$errors}\n";
echo "=================================================================\n\n";

// Kiểm tra lại
$stillNull = Transaction::whereNull('from_balance_before')->count();
echo "Giao dịch còn thiếu balance: {$stillNull}\n";

if ($stillNull > 0) {
    echo "\n⚠️  Một số giao dịch vẫn chưa có balance. Kiểm tra log lỗi ở trên.\n";
} else {
    echo "\n✅ Tất cả giao dịch đã có đầy đủ balance tracking!\n";
}

echo "\n";
