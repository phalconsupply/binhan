<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Services\AccountBalanceService;
use Illuminate\Support\Facades\DB;

echo "=================================================================\n";
echo "RESET VÀ TÍNH LẠI BALANCE TỪ ĐẦU\n";
echo "=================================================================\n\n";

// BƯỚC 1: Xóa hết balance cũ
echo "BƯỚC 1: Xóa tất cả balance cũ...\n";
DB::table('transactions')->update([
    'from_balance_before' => null,
    'from_balance_after' => null,
    'to_balance_before' => null,
    'to_balance_after' => null
]);
echo "✓ Đã xóa tất cả balance\n\n";

// BƯỚC 2: Tính lại balance CHỈ với giao dịch chưa xóa
echo "BƯỚC 2: Tính lại balance (CHỈ giao dịch chưa xóa)...\n\n";

$allTransactions = Transaction::orderBy('date')->orderBy('id')->get();

$accountBalances = [];
$processed = 0;
$updated = 0;

foreach ($allTransactions as $tx) {
    // Get accounts
    $fromAccount = $tx->from_account;
    $toAccount = $tx->to_account;
    
    if (!$fromAccount || !$toAccount) {
        continue;
    }
    
    // Initialize balances
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
    
    // Update transaction
    $tx->from_balance_before = $fromBalanceBefore;
    $tx->from_balance_after = $fromBalanceAfter;
    $tx->to_balance_before = $toBalanceBefore;
    $tx->to_balance_after = $toBalanceAfter;
    $tx->save(['timestamps' => false]);
    
    $processed++;
    $updated++;
    
    if ($processed % 50 == 0) {
        echo "✓ Đã xử lý: {$processed}\n";
    }
}

echo "\n=================================================================\n";
echo "✓ HOÀN THÀNH!\n";
echo "=================================================================\n";
echo "Đã xử lý:  {$processed} giao dịch\n";
echo "Updated:   {$updated}\n";
echo "=================================================================\n\n";

echo "📊 SỐ DƯ CUỐI CÙNG CÁC TÀI KHOẢN:\n";
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

// Kiểm tra xe 49B08879
echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✓ KiểmTRA XE 49B08879:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
if (isset($accountBalances['vehicle_4'])) {
    echo "Số dư vehicle_4: " . number_format($accountBalances['vehicle_4'], 0, ',', '.') . "đ\n";
} else {
    echo "Không tìm thấy vehicle_4\n";
}

echo "\n";
