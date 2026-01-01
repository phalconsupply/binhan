<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

echo "=== PHÂN TÍCH: XÓA GIAO DỊCH ĐẢO NGƯỢC REV20260101174800 ===\n\n";

// Lấy giao dịch reversal
$reversal = Transaction::where('code', 'REV20260101174800')->first();

if (!$reversal) {
    echo "❌ Không tìm thấy giao dịch REV20260101174800\n";
    exit;
}

echo "📊 THÔNG TIN GIAO DỊCH ĐẢO NGƯỢC:\n";
echo "   Code: {$reversal->code}\n";
echo "   Type: {$reversal->type}\n";
echo "   Amount: " . number_format($reversal->amount, 0, ',', '.') . "đ\n";
echo "   From: {$reversal->from_account}\n";
echo "   To: {$reversal->to_account}\n";
echo "   Status: {$reversal->lifecycle_status}\n";
echo "   Reverses Transaction ID: {$reversal->reverses_transaction_id}\n\n";

// Lấy giao dịch gốc
$original = Transaction::find($reversal->reverses_transaction_id);

if ($original) {
    echo "📌 GIAO DỊCH GỐC (bị đảo ngược):\n";
    echo "   Code: {$original->code}\n";
    echo "   Type: {$original->type}\n";
    echo "   Amount: " . number_format($original->amount, 0, ',', '.') . "đ\n";
    echo "   From: {$original->from_account}\n";
    echo "   To: {$original->to_account}\n";
    echo "   Status: {$original->lifecycle_status}\n";
    echo "   Reversed by Transaction ID: {$original->reversed_by_transaction_id}\n\n";
}

echo "⚠️  NẾU XÓA GIAO DỊCH ĐẢO NGƯỢC REV20260101174800:\n\n";

echo "1. 🔴 HẬU QUẢ TỨC THÌ:\n";
echo "   - Giao dịch đảo ngược biến mất khỏi hệ thống\n";
echo "   - Giao dịch gốc {$original->code} VẪN ở trạng thái 'reversed'\n";
echo "   - Nhưng không còn giao dịch đảo ngược để cân bằng\n";
echo "   - Số dư tài khoản sẽ SAI!\n\n";

echo "2. 💰 ẢNH HƯỞNG ĐẾN SỐ DƯ:\n";
$fromAccount = $reversal->fromAccount;
$toAccount = $reversal->toAccount;

if ($fromAccount && $toAccount) {
    echo "   - {$fromAccount->name}: Thiếu {$reversal->amount}đ (không trừ khi reversal bị xóa)\n";
    echo "   - {$toAccount->name}: Thiếu {$reversal->amount}đ (không cộng khi reversal bị xóa)\n\n";
}

echo "3. 📖 ẢNH HƯỞNG ĐẾN JOURNAL ENTRIES:\n";
$lines = $reversal->lines;
echo "   Giao dịch đảo ngược có " . $lines->count() . " journal entries sẽ biến mất:\n";
foreach ($lines as $line) {
    $type = $line->entry_type === 'debit' ? 'DEBIT ' : 'CREDIT';
    echo "   - {$type} {$line->account_code}: " . number_format($line->amount, 0, ',', '.') . "đ\n";
}
echo "\n";

echo "4. 🔗 TÌNH TRẠNG QUAN HỆ:\n";
echo "   - Giao dịch gốc {$original->code}:\n";
echo "     * reversed_by_transaction_id = {$original->reversed_by_transaction_id}\n";
echo "     * Nhưng giao dịch ID {$original->reversed_by_transaction_id} không tồn tại (đã xóa)\n";
echo "     * => BROKEN RELATIONSHIP (quan hệ bị hỏng)\n\n";

echo "5. 🎯 KẾT LUẬN:\n";
echo "   🔴 XÓA GIAO DỊCH ĐẢO NGƯỢC là NGUY HIỂM vì:\n";
echo "   - Phá vỡ tính toàn vẹn của reversal mechanism\n";
echo "   - Giao dịch gốc mất khả năng audit (không biết reversal ở đâu)\n";
echo "   - Số dư tài khoản sai\n";
echo "   - Journal entries không cân bằng\n\n";

echo "✅ GIẢI PHÁP ĐÚNG:\n\n";

echo "   Option 1: XÓA CẢ 2 GIAO DỊCH (gốc + reversal)\n";
echo "   -----------------------------------------\n";
echo "   Nếu cả 2 giao dịch đều sai/không cần thiết:\n";
echo "   1. Soft delete reversal: {$reversal->code}\n";
echo "   2. Soft delete original: {$original->code}\n";
echo "   => Cả 2 biến mất, hệ thống quay về trạng thái như chưa có giao dịch\n\n";

echo "   Option 2: REVERSE THE REVERSAL (đảo ngược giao dịch đảo ngược)\n";
echo "   -------------------------------------------------------------\n";
echo "   Nếu muốn phục hồi giao dịch gốc:\n";
echo "   1. Tạo reversal của reversal (tức là giao dịch giống gốc)\n";
echo "   2. Đánh dấu {$original->code} quay về 'active'\n";
echo "   => Giao dịch gốc được phục hồi, có đầy đủ audit trail\n\n";

echo "   Option 3: RESTORE GIAO DỊCH GỐC\n";
echo "   --------------------------------\n";
echo "   Nếu giao dịch gốc là ĐÚNG, không nên reverse:\n";
echo "   1. Xóa reversal\n";
echo "   2. Cập nhật giao dịch gốc:\n";
echo "      - lifecycle_status = 'active'\n";
echo "      - reversed_by_transaction_id = NULL\n";
echo "   3. Recalculate balances\n";
echo "   => Giao dịch gốc quay lại hoạt động bình thường\n\n";

echo "🎯 DEMO: Thử xem tác động thật:\n\n";

// Calculate current balances
$fromBalanceBefore = $fromAccount ? $fromAccount->balance : 0;
$toBalanceBefore = $toAccount ? $toAccount->balance : 0;

echo "   Số dư TRƯỚC KHI xóa reversal:\n";
if ($fromAccount) echo "   - {$fromAccount->name}: " . number_format($fromBalanceBefore, 0, ',', '.') . "đ\n";
if ($toAccount) echo "   - {$toAccount->name}: " . number_format($toBalanceBefore, 0, ',', '.') . "đ\n";
echo "\n";

echo "   Nếu xóa reversal, số dư sẽ thành:\n";
if ($fromAccount) {
    $newFromBalance = $fromBalanceBefore - $reversal->amount; // Mất giao dịch THU -> balance giảm
    echo "   - {$fromAccount->name}: " . number_format($newFromBalance, 0, ',', '.') . "đ (giảm " . number_format($reversal->amount, 0, ',', '.') . "đ)\n";
}
if ($toAccount) {
    $newToBalance = $toBalanceBefore - $reversal->amount; // Mất giao dịch THU -> balance giảm
    echo "   - {$toAccount->name}: " . number_format($newToBalance, 0, ',', '.') . "đ (giảm " . number_format($reversal->amount, 0, ',', '.') . "đ)\n";
}
echo "\n";

echo "⚠️  CẢNH BÁO: Đừng xóa riêng lẻ một trong hai giao dịch (gốc hoặc reversal)!\n";
echo "   Luôn xử lý CẢ CẶP giao dịch để đảm bảo tính toàn vẹn.\n\n";

// Interactive prompt
echo "💡 Bạn muốn làm gì?\n";
echo "   [1] Demo soft delete CẢ 2 giao dịch (an toàn)\n";
echo "   [2] Demo restore giao dịch gốc (hủy reversal)\n";
echo "   [3] Không làm gì, chỉ xem phân tích\n";
echo "\n";
