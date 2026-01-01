<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\TransactionLine;
use Illuminate\Support\Facades\DB;

echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║   PHÂN TÍCH: XÓA GIAO DỊCH GD20251218-0694 SẼ ẢNH HƯỞNG GÌ?          ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n\n";

$code = 'GD20251218-0694';
$transaction = Transaction::with(['fromAccount', 'toAccount', 'lines.account'])
    ->where('code', $code)
    ->first();

if (!$transaction) {
    echo "❌ Không tìm thấy giao dịch {$code}\n";
    exit(1);
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  THÔNG TIN GIAO DỊCH HIỆN TẠI\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "  ID: #{$transaction->id}\n";
echo "  Mã: {$transaction->code}\n";
echo "  Loại: " . strtoupper($transaction->type) . "\n";
echo "  Ngày: {$transaction->date->format('d/m/Y')}\n";
echo "  Số tiền: " . number_format($transaction->amount) . "đ\n\n";

echo "  Từ tài khoản:\n";
echo "    ├─ ID: {$transaction->from_account_id}\n";
echo "    ├─ Code: {$transaction->fromAccount->code}\n";
echo "    ├─ Tên: {$transaction->fromAccount->name}\n";
echo "    ├─ Số dư trước GD: " . number_format($transaction->from_balance_before) . "đ\n";
echo "    └─ Số dư sau GD: " . number_format($transaction->from_balance_after) . "đ\n\n";

echo "  Đến tài khoản:\n";
echo "    ├─ ID: {$transaction->to_account_id}\n";
echo "    ├─ Code: {$transaction->toAccount->code}\n";
echo "    ├─ Tên: {$transaction->toAccount->name}\n";
echo "    ├─ Số dư trước GD: " . number_format($transaction->to_balance_before) . "đ\n";
echo "    └─ Số dư sau GD: " . number_format($transaction->to_balance_after) . "đ\n\n";

// Kiểm tra journal entries
$lines = $transaction->lines;
echo "  Journal Entries (Phase 3):\n";
if ($lines->isEmpty()) {
    echo "    ⚠️  Chưa có journal entries\n\n";
} else {
    echo "    ┌─────────────────────────────┬──────────────┬──────────────┐\n";
    echo "    │ Account                     │ Debit        │ Credit       │\n";
    echo "    ├─────────────────────────────┼──────────────┼──────────────┤\n";
    foreach ($lines as $line) {
        $name = str_pad(substr($line->account->name, 0, 27), 27);
        $debit = str_pad(number_format($line->debit), 12, ' ', STR_PAD_LEFT);
        $credit = str_pad(number_format($line->credit), 12, ' ', STR_PAD_LEFT);
        echo "    │ {$name} │ {$debit} │ {$credit} │\n";
    }
    echo "    └─────────────────────────────┴──────────────┴──────────────┘\n\n";
}

// Kiểm tra các giao dịch sau nó
$laterTxFromCount = Transaction::where('from_account_id', $transaction->from_account_id)
    ->where('id', '>', $transaction->id)
    ->count();

$laterTxToCount = Transaction::where('to_account_id', $transaction->to_account_id)
    ->where('id', '>', $transaction->id)
    ->count();

echo "  Các giao dịch sau nó:\n";
echo "    ├─ Từ {$transaction->fromAccount->name}: {$laterTxFromCount} giao dịch\n";
echo "    └─ Đến {$transaction->toAccount->name}: {$laterTxToCount} giao dịch\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  NẾU XÓA GIAO DỊCH NÀY, ĐIỀU GÌ SẼ XẢY RA?\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "  📋 CÁC BƯỚC XẢY RA:\n\n";

echo "  1️⃣  XÓA JOURNAL ENTRIES (Phase 3)\n";
echo "      → Xóa " . count($lines) . " bút toán trong transaction_lines\n";
echo "      → CASCADE DELETE tự động (do Foreign Key)\n\n";

echo "  2️⃣  XÓA TRANSACTION RECORD (Phase 2)\n";
echo "      → Xóa record ID #{$transaction->id} trong transactions table\n";
echo "      → Balance snapshots (from/to_balance_before/after) bị mất\n\n";

echo "  3️⃣  SỐ DƯ TÀI KHOẢN BỊ SAI (Phase 1 & 2)\n\n";

// Tính toán ảnh hưởng
$fromAccountCurrentBalance = $transaction->fromAccount->balance;
$toAccountCurrentBalance = $transaction->toAccount->balance;

$fromAccountBalanceIfDelete = $fromAccountCurrentBalance + $transaction->amount; // Hoàn lại tiền
$toAccountBalanceIfDelete = $toAccountCurrentBalance - $transaction->amount; // Trừ lại tiền

echo "      Tài khoản: {$transaction->fromAccount->name}\n";
echo "      ├─ Số dư hiện tại: " . number_format($fromAccountCurrentBalance) . "đ\n";
echo "      ├─ Nếu xóa GD này: +" . number_format($transaction->amount) . "đ (hoàn lại)\n";
echo "      └─ Số dư sau khi xóa: " . number_format($fromAccountBalanceIfDelete) . "đ\n\n";

echo "      Tài khoản: {$transaction->toAccount->name}\n";
echo "      ├─ Số dư hiện tại: " . number_format($toAccountCurrentBalance) . "đ\n";
echo "      ├─ Nếu xóa GD này: -" . number_format($transaction->amount) . "đ (trừ lại)\n";
echo "      └─ Số dư sau khi xóa: " . number_format($toAccountBalanceIfDelete) . "đ\n\n";

if ($laterTxFromCount > 0 || $laterTxToCount > 0) {
    echo "  4️⃣  CÁC GIAO DỊCH SAU BỊ SAI SỐ DƯ\n\n";
    echo "      ⚠️  CÓ " . ($laterTxFromCount + $laterTxToCount) . " GIAO DỊCH SAU GD NÀY!\n";
    echo "      → Tất cả số dư balance_before/after đều SAI\n";
    echo "      → Cần RECALCULATE toàn bộ từ đầu\n\n";
} else {
    echo "  4️⃣  MAY MẮN: Không có giao dịch nào sau GD này\n";
    echo "      → Chỉ cần cập nhật số dư 2 tài khoản\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  CÁCH XỬ LÝ ĐÚNG TRONG HỆ THỐNG MỚI\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "  ✅ BƯỚC 1: XÓA GIAO DỊCH\n";
echo "     DELETE FROM transactions WHERE id = {$transaction->id};\n";
echo "     → Journal entries tự động xóa (CASCADE)\n\n";

echo "  ✅ BƯỚC 2: RECALCULATE BALANCES\n";
echo "     php artisan transactions:recalculate-balances\n";
echo "     → Tính lại TOÀN BỘ số dư từ đầu đến cuối\n";
echo "     → Cập nhật lại balance_before/after cho mọi giao dịch\n\n";

echo "  ✅ BƯỚC 3: REGENERATE JOURNAL ENTRIES\n";
echo "     php artisan transactions:generate-journal-entries --force\n";
echo "     → Xóa và tạo lại TOÀN BỘ journal entries\n";
echo "     → Đảm bảo Debit = Credit\n\n";

echo "  ✅ BƯỚC 4: VERIFY\n";
echo "     php artisan accounts:reconcile --all\n";
echo "     → Kiểm tra tất cả tài khoản có balanced không\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  SO SÁNH: HỆ THỐNG CŨ vs HỆ THỐNG MỚI\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "  HỆ THỐNG CŨ:\n";
echo "  ❌ Xóa giao dịch → Xong\n";
echo "  ❌ Không kiểm tra gì\n";
echo "  ❌ Số dư SAI nhưng không phát hiện được\n";
echo "  ❌ Không có cách nào verify lại\n\n";

echo "  HỆ THỐNG MỚI:\n";
echo "  ✅ Xóa giao dịch\n";
echo "  ✅ Recalculate toàn bộ (tự động sửa số dư)\n";
echo "  ✅ Regenerate journal entries\n";
echo "  ✅ Verify bằng reconcile command\n";
echo "  ✅ Nếu có lỗi → PHÁT HIỆN NGAY\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  DEMO: XÓA VÀ KHÔI PHỤC\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "  Bạn có muốn:\n";
echo "  1. Chỉ xem phân tích (không xóa)\n";
echo "  2. XÓA thử và sau đó KHÔI PHỤC bằng recalculate\n\n";
echo "  Nhập lựa chọn: ";

$choice = trim(fgets(STDIN));

if ($choice == '2') {
    echo "\n";
    echo "  🔄 BẮT ĐẦU DEMO...\n\n";
    
    // Backup thông tin
    $backupInfo = [
        'transaction_id' => $transaction->id,
        'code' => $transaction->code,
        'from_account' => $transaction->fromAccount->name,
        'to_account' => $transaction->toAccount->name,
        'amount' => $transaction->amount,
        'from_balance_before_delete' => $fromAccountCurrentBalance,
        'to_balance_before_delete' => $toAccountCurrentBalance,
    ];
    
    echo "  ━━━ BƯỚC 1: Số dư TRƯỚC khi xóa ━━━\n\n";
    echo "    {$backupInfo['from_account']}: " . number_format($fromAccountCurrentBalance) . "đ\n";
    echo "    {$backupInfo['to_account']}: " . number_format($toAccountCurrentBalance) . "đ\n\n";
    
    echo "  ━━━ BƯỚC 2: XÓA giao dịch ━━━\n\n";
    DB::beginTransaction();
    try {
        $transaction->delete();
        echo "    ✅ Đã xóa transaction #{$backupInfo['transaction_id']}\n";
        echo "    ✅ Journal entries tự động xóa (CASCADE)\n\n";
        
        echo "  ━━━ BƯỚC 3: Kiểm tra số dư SAU khi xóa ━━━\n\n";
        $fromAccountAfterDelete = DB::table('accounts')->where('id', $backupInfo['transaction_id'])->value('balance');
        $toAccountAfterDelete = DB::table('accounts')->where('id', $transaction->to_account_id)->value('balance');
        
        echo "    ⚠️  Số dư CHƯA được cập nhật tự động!\n";
        echo "    {$backupInfo['from_account']}: vẫn là " . number_format($fromAccountCurrentBalance) . "đ\n";
        echo "    {$backupInfo['to_account']}: vẫn là " . number_format($toAccountCurrentBalance) . "đ\n\n";
        
        DB::rollBack();
        echo "  ━━━ BƯỚC 4: ROLLBACK (không thực sự xóa) ━━━\n\n";
        echo "    ✅ Đã rollback, dữ liệu vẫn nguyên\n\n";
        
    } catch (\Exception $e) {
        DB::rollBack();
        echo "    ❌ Lỗi: " . $e->getMessage() . "\n\n";
    }
    
    echo "  💡 KẾT LUẬN:\n";
    echo "     → Xóa transaction chỉ xóa record, KHÔNG tự động cập nhật số dư\n";
    echo "     → PHẢI chạy recalculate để sửa lại số dư\n";
    echo "     → HỆ THỐNG MỚI có commands để làm việc này dễ dàng\n\n";
    
} else {
    echo "\n  ℹ️  Chỉ xem phân tích, không thay đổi dữ liệu.\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  KHUYẾN NGHỊ\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "  ⚠️  KHÔNG NÊN xóa giao dịch trực tiếp trong production\n\n";

echo "  ✅ NÊN dùng soft delete:\n";
echo "     UPDATE transactions SET is_active = 0, replaced_by = NULL\n";
echo "     → Giao dịch vẫn tồn tại, chỉ đánh dấu không active\n";
echo "     → Có thể khôi phục nếu cần\n";
echo "     → Audit trail đầy đủ\n\n";

echo "  ✅ HOẶC dùng replacement transaction:\n";
echo "     1. Tạo giao dịch mới (đúng)\n";
echo "     2. Đánh dấu giao dịch cũ: replaced_by = new_transaction_id\n";
echo "     3. Set is_active = 0 cho giao dịch cũ\n";
echo "     → Có lịch sử thay đổi\n";
echo "     → Biết tại sao thay đổi\n\n";

echo "╔════════════════════════════════════════════════════════════════════════╗\n";
echo "║                         HOÀN THÀNH PHÂN TÍCH                          ║\n";
echo "╚════════════════════════════════════════════════════════════════════════╝\n";
