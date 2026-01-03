<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Services\AccountBalanceService;
use Illuminate\Support\Facades\DB;

echo "=================================================================\n";
echo "UPDATE BALANCE ĐÚNG - CHỈ TÍNH GIAO DỊCH CHƯA XÓA\n";
echo "=================================================================\n\n";

$total = Transaction::count();

echo "Tổng số giao dịch: {$total}\n";
echo "⚠️  CHẾ ĐỘ: Chỉ tính balance từ giao dịch CHƯA BỊ XÓA\n";
echo "Bắt đầu update balance...\n\n";

// Lấy TẤT CẢ giao dịch theo thứ tự thời gian (bao gồm cả deleted để update)
$allTransactions = Transaction::withTrashed()->orderBy('date')->orderBy('id')->get();

// Track balance chỉ với các giao dịch CHƯA XÓA
$accountBalances = [];

$processed = 0;
$updated = 0;
$errors = 0;

foreach ($allTransactions as $tx) {
    try {
        // Get accounts
        $fromAccount = $tx->from_account;
        $toAccount = $tx->to_account;
        
        if (!$fromAccount || !$toAccount) {
            // Nếu chưa có account tracking, skip
            continue;
        }
        
        // Initialize balances if not exists
        if (!isset($accountBalances[$fromAccount])) {
            $accountBalances[$fromAccount] = 0;
        }
        if (!isset($accountBalances[$toAccount])) {
            $accountBalances[$toAccount] = 0;
        }
        
        if ($tx->trashed()) {
            // Giao dịch đã xóa: Chỉ set balance = NULL, KHÔNG tính vào cumulative
            $tx->from_balance_before = null;
            $tx->from_balance_after = null;
            $tx->to_balance_before = null;
            $tx->to_balance_after = null;
            $tx->save(['timestamps' => false]);
        } else {
            // Giao dịch chưa xóa: Tính balance bình thường
            $fromBalanceBefore = $accountBalances[$fromAccount];
            $toBalanceBefore = $accountBalances[$toAccount];
            
            // Update balances
            $accountBalances[$fromAccount] -= $tx->amount;
            $accountBalances[$toAccount] += $tx->amount;
            
            $fromBalanceAfter = $accountBalances[$fromAccount];
            $toBalanceAfter = $accountBalances[$toAccount];
            
            // Update transaction record
            $tx->from_balance_before = $fromBalanceBefore;
            $tx->from_balance_after = $fromBalanceAfter;
            $tx->to_balance_before = $toBalanceBefore;
            $tx->to_balance_after = $toBalanceAfter;
            $tx->save(['timestamps' => false]);
            
            $updated++;
        }
        
        $processed++;
        
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
$activeWithBalance = Transaction::whereNotNull('from_balance_before')->count();
$deletedWithBalance = Transaction::onlyTrashed()->whereNotNull('from_balance_before')->count();

echo "Giao dịch chưa xóa có balance: {$activeWithBalance}\n";
echo "Giao dịch đã xóa có balance:   {$deletedWithBalance} (nên là 0)\n\n";

if ($deletedWithBalance > 0) {
    echo "⚠️  Vẫn còn giao dịch đã xóa có balance!\n";
} else {
    echo "✅ Tất cả giao dịch đã xóa đã được xóa balance!\n";
}

echo "\n📊 SỐ DƯ CUỐI CÙNG CÁC TÀI KHOẢN (CHỈ TÍNH CHƯA XÓA):\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
arsort($accountBalances);
foreach ($accountBalances as $account => $balance) {
    $displayName = AccountBalanceService::getAccountDisplayName($account);
    $color = $balance >= 0 ? '' : '⚠️  ';
    echo sprintf(
        "%s%-30s: %20s đ\n",
        $color,
        $displayName,
        number_format($balance, 0, ',', '.')
    );
}

echo "\n";
