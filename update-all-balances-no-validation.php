<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Services\AccountBalanceService;

echo "=================================================================\n";
echo "UPDATE BALANCE CHO TẤT CẢ GIAO DỊCH (SKIP VALIDATION)\n";
echo "=================================================================\n\n";

$total = Transaction::count();

echo "Tổng số giao dịch: {$total}\n";
echo "⚠️  CHẾ ĐỘ: Bỏ qua validation số dư không đủ (dữ liệu cũ)\n";
echo "Bắt đầu update balance...\n\n";

// Lấy tất cả giao dịch theo thứ tự thời gian
$transactions = Transaction::orderBy('date')->orderBy('id')->get();

$processed = 0;
$updated = 0;
$errors = 0;

// Track balance manually without validation
$accountBalances = [];

foreach ($transactions as $tx) {
    try {
        // Get accounts
        $accounts = AccountBalanceService::determineAccounts($tx);
        $fromAccount = $accounts['from_account'];
        $toAccount = $accounts['to_account'];
        
        // Initialize balances if not exists
        if (!isset($accountBalances[$fromAccount])) {
            $accountBalances[$fromAccount] = 0;
        }
        if (!isset($accountBalances[$toAccount])) {
            $accountBalances[$toAccount] = 0;
        }
        
        // Save before balances
        $fromBalanceBefore = $accountBalances[$fromAccount];
        $toBalanceBefore = $accountBalances[$toAccount];
        
        // Update balances
        $accountBalances[$fromAccount] -= $tx->amount;
        $accountBalances[$toAccount] += $tx->amount;
        
        // Save after balances
        $fromBalanceAfter = $accountBalances[$fromAccount];
        $toBalanceAfter = $accountBalances[$toAccount];
        
        // Update transaction record
        $tx->from_balance_before = $fromBalanceBefore;
        $tx->from_balance_after = $fromBalanceAfter;
        $to_balance_before = $toBalanceBefore;
        $tx->to_balance_after = $toBalanceAfter;
        $tx->save(['timestamps' => false]);
        
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

echo "\n📊 SỐ DƯ CUỐI CÙNG CÁC TÀI KHOẢN:\n";
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
