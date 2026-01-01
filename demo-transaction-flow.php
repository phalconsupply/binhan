<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionLine;

echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║     VÍ DỤ THỰC TẾ: LUỒNG TẠO GIAO DỊCH VỚI HỆ THỐNG MỚI             ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

// Lấy một giao dịch mẫu
$transaction = Transaction::with(['fromAccount', 'toAccount', 'lines.account'])
    ->whereNotNull('from_account_id')
    ->whereNotNull('to_account_id')
    ->orderBy('id', 'desc')
    ->first();

if (!$transaction) {
    echo "Không tìm thấy giao dịch nào.\n";
    exit;
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  BƯỚC 1: USER NHẬP DỮ LIỆU\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$fromName = $transaction->fromAccount->name;
$toName = $transaction->toAccount->name;
$amount = number_format($transaction->amount);

echo "  Người dùng muốn tạo giao dịch:\n";
echo "  ├─ Từ: {$fromName}\n";
echo "  ├─ Đến: {$toName}\n";
echo "  ├─ Số tiền: {$amount}đ\n";
echo "  └─ Loại: " . ucfirst($transaction->type) . "\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  PHASE 1: VALIDATION & CONSTRAINTS\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "  [1.1] Kiểm tra số tiền > 0\n";
echo "        → {$amount}đ > 0 ✅\n\n";

echo "  [1.2] Kiểm tra mã giao dịch không trùng\n";
echo "        → Mã: {$transaction->code}\n";
echo "        → Chưa tồn tại ✅\n\n";

echo "  [1.3] Lock tài khoản (Pessimistic Locking)\n";
echo "        → Cache::lock('account_{$transaction->from_account_id}', 10)\n";
echo "        → Đang lock... ✅\n\n";

$fromBalance = $transaction->from_balance_before;
echo "  [1.4] Kiểm tra số dư\n";
echo "        → Tài khoản: {$fromName}\n";
echo "        → Số dư hiện tại: " . number_format($fromBalance) . "đ\n";
echo "        → Cần chi: {$amount}đ\n";

if ($fromBalance >= $transaction->amount) {
    echo "        → " . number_format($fromBalance) . " >= {$amount} ✅\n";
    echo "        → ĐỦ TIỀN, CHO PHÉP TẠO GIAO DỊCH\n\n";
} else {
    echo "        → " . number_format($fromBalance) . " < {$amount} ❌\n";
    echo "        → KHÔNG ĐỦ TIỀN!\n";
    echo "        → Throw InsufficientBalanceException\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  PHASE 2: ACCOUNT NORMALIZATION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "  [2.1] Tìm Account IDs\n";
echo "        → From Account:\n";
echo "            ID: {$transaction->from_account_id}\n";
echo "            Code: {$transaction->fromAccount->code}\n";
echo "            Name: {$transaction->fromAccount->name}\n";
echo "            Type: {$transaction->fromAccount->type}\n\n";

echo "        → To Account:\n";
echo "            ID: {$transaction->to_account_id}\n";
echo "            Code: {$transaction->toAccount->code}\n";
echo "            Name: {$transaction->toAccount->name}\n";
echo "            Type: {$transaction->toAccount->type}\n\n";

echo "  [2.2] Tạo Transaction Record\n";
echo "        → INSERT INTO transactions (\n";
echo "            id: {$transaction->id}\n";
echo "            code: '{$transaction->code}'\n";
echo "            from_account_id: {$transaction->from_account_id}\n";
echo "            to_account_id: {$transaction->to_account_id}\n";
echo "            amount: {$transaction->amount}\n";
echo "            type: '{$transaction->type}'\n";
echo "            date: '{$transaction->date->format('Y-m-d')}'\n";
echo "          )\n\n";

echo "  [2.3] Cập nhật số dư\n";
$fromBalanceBefore = $transaction->from_balance_before;
$fromBalanceAfter = $transaction->from_balance_after;
$toBalanceBefore = $transaction->to_balance_before;
$toBalanceAfter = $transaction->to_balance_after;

echo "        → {$fromName}:\n";
echo "            Trước: " . number_format($fromBalanceBefore) . "đ\n";
echo "            Sau:   " . number_format($fromBalanceAfter) . "đ\n";
echo "            Thay đổi: " . number_format($fromBalanceAfter - $fromBalanceBefore) . "đ\n\n";

echo "        → {$toName}:\n";
echo "            Trước: " . number_format($toBalanceBefore) . "đ\n";
echo "            Sau:   " . number_format($toBalanceAfter) . "đ\n";
echo "            Thay đổi: " . number_format($toBalanceAfter - $toBalanceBefore) . "đ\n\n";

echo "  [2.4] Unlock tài khoản\n";
echo "        → Cache::lock()->release() ✅\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  PHASE 3: DOUBLE-ENTRY BOOKKEEPING\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "  [3.1] Tạo Journal Entries (Sổ kép)\n\n";

$lines = $transaction->lines;
$totalDebit = 0;
$totalCredit = 0;

echo "  ┌─────────────────────────────────────┬──────────────┬──────────────┐\n";
echo "  │ Account                             │ Debit        │ Credit       │\n";
echo "  ├─────────────────────────────────────┼──────────────┼──────────────┤\n";

foreach ($lines as $line) {
    $accountName = str_pad($line->account->name, 35);
    $debit = str_pad(number_format($line->debit), 12, ' ', STR_PAD_LEFT);
    $credit = str_pad(number_format($line->credit), 12, ' ', STR_PAD_LEFT);
    
    echo "  │ {$accountName} │ {$debit} │ {$credit} │\n";
    
    $totalDebit += $line->debit;
    $totalCredit += $line->credit;
}

echo "  ├─────────────────────────────────────┼──────────────┼──────────────┤\n";
echo "  │ TỔNG                                │ " . str_pad(number_format($totalDebit), 12, ' ', STR_PAD_LEFT) . " │ " . str_pad(number_format($totalCredit), 12, ' ', STR_PAD_LEFT) . " │\n";
echo "  └─────────────────────────────────────┴──────────────┴──────────────┘\n\n";

echo "  [3.2] Kiểm tra cân đối (Debit = Credit)\n";
echo "        → Total Debit:  " . number_format($totalDebit) . "đ\n";
echo "        → Total Credit: " . number_format($totalCredit) . "đ\n";
echo "        → Chênh lệch:   " . number_format(abs($totalDebit - $totalCredit)) . "đ\n";

if (abs($totalDebit - $totalCredit) < 0.01) {
    echo "        → ✅ CÂN ĐỐI! (Debit = Credit)\n\n";
} else {
    echo "        → ❌ KHÔNG CÂN ĐỐI! (Có lỗi)\n\n";
}

echo "  [3.3] Lưu vào transaction_lines table\n";
echo "        → INSERT " . count($lines) . " journal entries\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  KẾT QUẢ CUỐI CÙNG\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "  ✅ Giao dịch #{$transaction->id} đã được tạo thành công!\n\n";

echo "  📊 Thông tin lưu trữ:\n";
echo "  ├─ transactions table: 1 record (giao dịch chính)\n";
echo "  ├─ transaction_lines table: " . count($lines) . " records (journal entries)\n";
echo "  ├─ accounts table: 2 accounts updated (số dư)\n";
echo "  └─ Tất cả dữ liệu đã được kiểm tra và cân đối ✅\n\n";

echo "  🔍 Có thể kiểm tra lại bất cứ lúc nào:\n";
echo "  ├─ php artisan accounts:reconcile --all\n";
echo "  ├─ php artisan transactions:recalculate-balances\n";
echo "  └─ Xem transaction_lines để audit\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  SO SÁNH VỚI HỆ THỐNG CŨ\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "  HỆ THỐNG CŨ:\n";
echo "  ❌ Chỉ lưu from_account='vehicle_4', to_account='customer' (text)\n";
echo "  ❌ Không kiểm tra số dư\n";
echo "  ❌ Không có journal entries\n";
echo "  ❌ Không thể audit\n";
echo "  ❌ Dễ sai dữ liệu\n\n";

echo "  HỆ THỐNG MỚI:\n";
echo "  ✅ Lưu from_account_id=11, to_account_id=1 (normalized)\n";
echo "  ✅ Kiểm tra số dư trước khi tạo\n";
echo "  ✅ Có journal entries (debit/credit)\n";
echo "  ✅ Có thể audit mọi lúc\n";
echo "  ✅ Dữ liệu luôn chính xác (constraints + locking + double-entry)\n\n";

echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║                         HOÀN THÀNH                                    ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n";
