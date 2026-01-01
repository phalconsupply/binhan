<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;

echo "╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║  LÀM RÕ: HỆ THỐNG REVERSAL KHÔNG TỰ ĐỘNG - NGƯỜI DÙNG CHỌN!        ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

$original = Transaction::where('code', 'GD20251218-0694')->first();

echo "📌 VÍ DỤ VỚI GIAO DỊCH: {$original->code}\n";
echo "   Type: {$original->type} (CHI)\n";
echo "   Amount: " . number_format($original->amount, 0, ',', '.') . "đ\n";
echo "   From: Quỹ công ty → To: Bên ngoài\n";
echo "   Note: Chi phí gì đó\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "❌ HIỂU NHẦM (SAI):\n";
echo "   \"Khi tôi XÓA giao dịch GD20251218-0694,\n";
echo "    hệ thống TỰ ĐỘNG tạo giao dịch đảo ngược\"\n\n";

echo "✅ THỰC TẾ (ĐÚNG):\n";
echo "   Hệ thống KHÔNG TỰ ĐỘNG làm gì cả!\n";
echo "   NGƯỜI DÙNG phải CHỌN một trong các hành động:\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "🎯 TÌNH HUỐNG: Bạn phát hiện giao dịch {$original->code} cần xử lý\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "📋 BẠN CÓ 4 LỰA CHỌN:\n\n";

echo "┌────────────────────────────────────────────────────────────────────┐\n";
echo "│ LỰA CHỌN 1: REVERSAL (Đảo ngược giao dịch)                        │\n";
echo "└────────────────────────────────────────────────────────────────────┘\n\n";

echo "   KHI NÀO DÙNG:\n";
echo "   - Giao dịch đã GHI ĐÚNG nhưng cần HỦY BỎ\n";
echo "   - Ví dụ: Chi 360,000đ cho nhà cung cấp A, nhưng hóa đơn bị hủy\n";
echo "   - Giao dịch đã xảy ra trong thực tế, cần có trong sổ sách\n\n";

echo "   NGƯỜI DÙNG LÀM:\n";
echo "   1. Vào màn hình giao dịch {$original->code}\n";
echo "   2. Click nút [ĐẢO NGƯỢC] (hoặc chạy command)\n";
echo "   3. Nhập lý do: 'Hủy hóa đơn'\n";
echo "   4. Xác nhận\n\n";

echo "   HỆ THỐNG LÀM:\n";
echo "   ✓ TẠO giao dịch mới REV20260101174800:\n";
echo "     - Type: THU (ngược lại với CHI)\n";
echo "     - Amount: 360,000đ (giống gốc)\n";
echo "     - From: Bên ngoài → To: Quỹ công ty (ĐẢONGU)\n";
echo "     - Note: 'ĐẢONGU: GD20251218-0694 - Lý do: Hủy hóa đơn'\n";
echo "   ✓ CẬP NHẬT giao dịch gốc:\n";
echo "     - lifecycle_status = 'reversed'\n";
echo "     - reversed_by_transaction_id = 810\n";
echo "   ✓ GIỮ NGUYÊN cả 2 giao dịch trong database\n\n";

echo "   KẾT QUẢ:\n";
echo "   - Giao dịch gốc: CHI 360,000đ (reversed)\n";
echo "   - Giao dịch đảo: THU 360,000đ (active)\n";
echo "   - Tổng impact: 0đ (cân bằng)\n";
echo "   - Audit trail: Đầy đủ lịch sử\n\n";

echo "   CODE:\n";
echo "   php artisan transaction:reverse GD20251218-0694 'Hủy hóa đơn'\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "┌────────────────────────────────────────────────────────────────────┐\n";
echo "│ LỰA CHỌN 2: SOFT DELETE (Xóa mềm)                                 │\n";
echo "└────────────────────────────────────────────────────────────────────┘\n\n";

echo "   KHI NÀO DÙNG:\n";
echo "   - Giao dịch NHẬP NHẦM HOÀN TOÀN\n";
echo "   - Ví dụ: Nhập nhầm 360,000đ thay vì 36,000đ\n";
echo "   - Giao dịch CHƯA XẢY RA trong thực tế\n\n";

echo "   NGƯỜI DÙNG LÀM:\n";
echo "   1. Vào màn hình giao dịch {$original->code}\n";
echo "   2. Click nút [XÓA]\n";
echo "   3. Nhập lý do: 'Nhập nhầm số tiền'\n";
echo "   4. Xác nhận\n\n";

echo "   HỆ THỐNG LÀM:\n";
echo "   ✓ CẬP NHẬT giao dịch:\n";
echo "     - lifecycle_status = 'cancelled'\n";
echo "     - deleted_at = NOW()\n";
echo "   ✗ KHÔNG TẠO giao dịch đảo ngược\n";
echo "   ✓ ẨN khỏi danh sách (nhưng vẫn trong DB)\n\n";

echo "   KẾT QUẢ:\n";
echo "   - Giao dịch gốc: Ẩn (có thể restore)\n";
echo "   - Không có giao dịch đảo ngược\n";
echo "   - Số dư quay về như chưa có giao dịch này\n\n";

echo "   CODE:\n";
echo "   \$service->softDeleteTransaction(\$tx, 'Nhập nhầm số tiền');\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "┌────────────────────────────────────────────────────────────────────┐\n";
echo "│ LỰA CHỌN 3: REPLACEMENT (Thay thế)                                │\n";
echo "└────────────────────────────────────────────────────────────────────┘\n\n";

echo "   KHI NÀO DÙNG:\n";
echo "   - Giao dịch có THÔNG TIN SAI (số tiền, tài khoản, etc)\n";
echo "   - Ví dụ: Ghi 360,000đ nhưng thực tế là 320,000đ\n";
echo "   - Cần tạo giao dịch MỚI ĐÚNG\n\n";

echo "   NGƯỜI DÙNG LÀM:\n";
echo "   1. Vào màn hình giao dịch {$original->code}\n";
echo "   2. Click nút [THAY THẾ]\n";
echo "   3. Nhập dữ liệu ĐÚNG: Amount = 320,000đ\n";
echo "   4. Nhập lý do: 'Sửa số tiền đúng theo hóa đơn'\n";
echo "   5. Xác nhận\n\n";

echo "   HỆ THỐNG LÀM:\n";
echo "   ✓ TẠO giao dịch mới:\n";
echo "     - Code: GD20260102-XXXX\n";
echo "     - Amount: 320,000đ (số đúng)\n";
echo "     - lifecycle_status = 'active'\n";
echo "   ✓ CẬP NHẬT giao dịch cũ:\n";
echo "     - lifecycle_status = 'replaced'\n";
echo "     - replaced_by = (ID giao dịch mới)\n";
echo "   ✓ GIỮ NGUYÊN cả 2 trong database\n\n";

echo "   KẾT QUẢ:\n";
echo "   - Giao dịch cũ: CHI 360,000đ (replaced, ẩn khỏi báo cáo)\n";
echo "   - Giao dịch mới: CHI 320,000đ (active, dùng cho báo cáo)\n";
echo "   - Audit trail: Biết đã sửa gì\n\n";

echo "   CODE:\n";
echo "   \$service->replaceTransaction(\$old, ['amount' => 320000], 'Sửa số tiền');\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "┌────────────────────────────────────────────────────────────────────┐\n";
echo "│ LỰA CHỌN 4: KHÔNG LÀM GÌ (Giữ nguyên)                             │\n";
echo "└────────────────────────────────────────────────────────────────────┘\n\n";

echo "   KHI NÀO DÙNG:\n";
echo "   - Giao dịch ĐÚNG, không cần thay đổi\n\n";

echo "   KẾT QUẢ:\n";
echo "   - Giao dịch vẫn ở trạng thái 'active'\n";
echo "   - Không có thay đổi gì\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "🎯 SO SÁNH CÁC LỰA CHỌN:\n\n";

echo "┌──────────────┬─────────────┬─────────────┬──────────────┬─────────────┐\n";
echo "│ Tình huống   │ Reversal    │ Soft Delete │ Replacement  │ Không làm gì│\n";
echo "├──────────────┼─────────────┼─────────────┼──────────────┼─────────────┤\n";
echo "│ Hủy hóa đơn  │ ✅ DÙNG     │ ❌          │ ❌           │ ❌          │\n";
echo "│ Nhập nhầm    │ ❌          │ ✅ DÙNG     │ ❌           │ ❌          │\n";
echo "│ Sửa số tiền  │ ❌          │ ❌          │ ✅ DÙNG      │ ❌          │\n";
echo "│ Giao dịch OK │ ❌          │ ❌          │ ❌           │ ✅ DÙNG     │\n";
echo "└──────────────┴─────────────┴─────────────┴──────────────┴─────────────┘\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "💡 WORKFLOW THỰC TẾ:\n\n";

echo "BƯC 1: NGƯỜI DÙNG phát hiện cần xử lý giao dịch\n";
echo "       ↓\n";
echo "BƯỚC 2: NGƯỜI DÙNG xem xét tình huống\n";
echo "       ↓\n";
echo "BƯỚC 3: NGƯỜI DÙNG CHỌN hành động (Reversal/Delete/Replace/Không)\n";
echo "       ↓\n";
echo "BƯỚC 4: NGƯỜI DÙNG thực hiện action (click nút/chạy command)\n";
echo "       ↓\n";
echo "BƯỚC 5: HỆ THỐNG xử lý theo lựa chọn của người dùng\n\n";

echo "⚠️  QUAN TRỌNG:\n";
echo "   - HỆ THỐNG KHÔNG BAO GIỜ TỰ ĐỘNG TẠO REVERSAL\n";
echo "   - NGƯỜI DÙNG phải CHỦ ĐỘNG chọn 'Reversal'\n";
echo "   - Nút [XÓA] ≠ Reversal, chỉ là Soft Delete\n";
echo "   - Nếu muốn reversal → phải dùng nút/command riêng [ĐẢO NGƯỢC]\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "📊 VÍ DỤ CỤ THỂ VỚI GD20251218-0694:\n\n";

echo "Tình huống A: Hóa đơn bị hủy (giao dịch đã xảy ra nhưng cần hủy)\n";
echo "→ CHỌN: Reversal\n";
echo "→ COMMAND: php artisan transaction:reverse GD20251218-0694 'Hủy HĐ'\n";
echo "→ KẾT QUẢ: 2 giao dịch (gốc + reversal), tổng = 0đ\n\n";

echo "Tình huống B: Nhập nhầm (không có hóa đơn thực tế)\n";
echo "→ CHỌN: Soft Delete\n";
echo "→ CODE: \$service->softDeleteTransaction(...)\n";
echo "→ KẾT QUẢ: 1 giao dịch bị ẩn, không có reversal\n\n";

echo "Tình huống C: Sai số tiền (360k → 320k)\n";
echo "→ CHỌN: Replacement\n";
echo "→ CODE: \$service->replaceTransaction(...)\n";
echo "→ KẾT QUẢ: 2 giao dịch (cũ replaced, mới active)\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "✅ KẾT LUẬN:\n\n";
echo "1. HỆ THỐNG KHÔNG TỰ ĐỘNG - Người dùng chủ động chọn\n";
echo "2. Reversal ≠ Xóa - Là 2 hành động KHÁC NHAU\n";
echo "3. Reversal = Tạo giao dịch đối nghịch (giữ nguyên gốc)\n";
echo "4. Xóa = Ẩn giao dịch (không tạo đối nghịch)\n";
echo "5. Mỗi tình huống dùng phương pháp khác nhau\n\n";

echo "🎯 CÁCH SỬ DỤNG ĐÚNG:\n";
echo "   - Hủy nghiệp vụ đã ghi → Reversal\n";
echo "   - Nhập nhầm hoàn toàn → Soft Delete\n";
echo "   - Sửa thông tin sai → Replacement\n";
echo "   - Giao dịch đúng → Không làm gì\n\n";
