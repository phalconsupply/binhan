<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;

echo "=================================================================\n";
echo "PHÂN TÍCH CHI TIẾT: TẠI SAO LỢI NHUẬN CÔNG TY BỊ GIẢM?\n";
echo "=================================================================\n\n";

// 1. Kiểm tra scope expense() tính những gì
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "1. SCOPE expense() TÍNH NHỮNG GIAO DỊCH NÀO?\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Kiểm tra code của scope
$reflection = new ReflectionMethod(\App\Models\Transaction::class, 'scopeExpense');
echo "Location: app/Models/Transaction.php\n";
echo "Scope tính: WHERE type = 'chi'\n\n";

// 2. Thống kê công ty - logic hiện tại
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "2. LOGIC TÍNH THỐNG KÊ CÔNG TY (/transactions)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "Code trong TransactionController:\n";
echo "\$statsQuery = Transaction::query();  // CHƯA filter vehicle_id\n";
echo "\$totalExpense = \$statsQuery->expense()->sum('amount');\n\n";

echo "⚠️  VẤN ĐỀ:\n";
echo "- \$statsQuery KHÔNG có ->whereNull('vehicle_id')\n";
echo "- Scope expense() chỉ filter type='chi'\n";
echo "- KẾT QUẢ: Tính CẢ giao dịch chi của XE + chi của CÔNG TY\n\n";

// 3. Phân tích cụ thể
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "3. PHÂN TÍCH CỤ THỂ\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Tổng chi CÔNG TY (vehicle_id = NULL)
$companyExpenseOnly = Transaction::whereNull('vehicle_id')
    ->where('type', 'chi')
    ->sum('amount');

// Tổng chi TẤT CẢ (bao gồm xe)
$allExpense = Transaction::where('type', 'chi')->sum('amount');

// Tổng chi của XE
$vehicleExpense = Transaction::whereNotNull('vehicle_id')
    ->where('type', 'chi')
    ->sum('amount');

echo "CHI CÔNG TY (vehicle_id = NULL):     " . number_format($companyExpenseOnly, 0, ',', '.') . "đ\n";
echo "CHI CỦA XE (vehicle_id != NULL):     " . number_format($vehicleExpense, 0, ',', '.') . "đ\n";
echo "═══════════════════════════════════════════════════\n";
echo "TỔNG CHI (hiển thị ở /transactions): " . number_format($allExpense, 0, ',', '.') . "đ\n\n";

echo "⚠️  VẤN ĐỀ:\n";
echo "Khi xe có chủ chi tiền:\n";
echo "  1. Giao dịch có vehicle_id = 4 (xe 49B08879)\n";
echo "  2. Giao dịch có type = 'chi'\n";
echo "  3. Scope expense() tính luôn giao dịch này\n";
echo "  4. Lợi nhuận công ty = Thu - Chi (cả xe) - Dự kiến\n";
echo "  5. → LỢI NHUẬN CÔNG TY BỊ GIẢM SAI!\n\n";

// 4. So sánh với logic thu
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "4. SO SÁNH VỚI LOGIC THU\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$companyRevenueOnly = Transaction::whereNull('vehicle_id')
    ->where('type', 'thu')
    ->where(function($q) {
        $q->where('category', '!=', 'vay_từ_công_ty')
          ->orWhereNull('category');
    })
    ->sum('amount');

$allRevenue = Transaction::where('type', 'thu')->sum('amount');
$vehicleRevenue = Transaction::whereNotNull('vehicle_id')
    ->where('type', 'thu')
    ->sum('amount');

echo "THU CÔNG TY (vehicle_id = NULL):     " . number_format($companyRevenueOnly, 0, ',', '.') . "đ\n";
echo "THU CỦA XE (vehicle_id != NULL):     " . number_format($vehicleRevenue, 0, ',', '.') . "đ\n";
echo "═══════════════════════════════════════════════════\n";
echo "TỔNG THU (hiển thị ở /transactions): " . number_format($companyRevenueOnly, 0, ',', '.') . "đ\n\n";

echo "✓ LOGIC THU ĐÚNG:\n";
echo "- TransactionController KHÔNG lấy revenue() trực tiếp\n";
echo "- Code: \$statsQuery->revenue()->where(...)->sum()\n";
echo "- Nhưng chưa thấy ->whereNull('vehicle_id')\n\n";

// 5. Kiểm tra code thực tế
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "5. KIỂM TRA CODE THỰC TẾ\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Simulate logic from TransactionController line 285
$statsQuery = Transaction::query(); // Dòng 264

$totalRevenue = (clone $statsQuery)->revenue()->where(function($q) {
    $q->where('category', '!=', 'vay_từ_công_ty')->orWhereNull('category');
})->sum('amount');

echo "Dòng 285-287 TransactionController:\n";
echo "\$totalRevenue = (clone \$statsQuery)->revenue()\n";
echo "    ->where(function(\$q) {...})\n";
echo "    ->sum('amount');\n\n";

echo "Kết quả: " . number_format($totalRevenue, 0, ',', '.') . "đ\n\n";

echo "⚠️  PHÁT HIỆN:\n";
echo "- \$statsQuery = Transaction::query() → KHÔNG có filter\n";
echo "- revenue() chỉ thêm ->where('type', 'thu')\n";
echo "- Thiếu ->whereNull('vehicle_id')\n";
echo "- KẾT QUẢ: Tính cả thu của XE!\n\n";

// 6. Proof
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "6. CHỨNG MINH\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Check xem có thêm whereNull không
$withFilter = Transaction::whereNull('vehicle_id')
    ->revenue()
    ->where(function($q) {
        $q->where('category', '!=', 'vay_từ_công_ty')->orWhereNull('category');
    })
    ->sum('amount');

$withoutFilter = Transaction::revenue()
    ->where(function($q) {
        $q->where('category', '!=', 'vay_từ_công_ty')->orWhereNull('category');
    })
    ->sum('amount');

echo "CÓ whereNull('vehicle_id'):    " . number_format($withFilter, 0, ',', '.') . "đ\n";
echo "KHÔNG whereNull('vehicle_id'): " . number_format($withoutFilter, 0, ',', '.') . "đ\n";
echo "Chênh lệch:                     " . number_format($withoutFilter - $withFilter, 0, ',', '.') . "đ\n\n";

if ($withoutFilter != $withFilter) {
    echo "✗ CODE ĐANG SAI - Tính cả giao dịch của XE vào công ty!\n";
} else {
    echo "✓ Code đúng\n";
}

echo "\n";
echo "=================================================================\n";
echo "KẾT LUẬN VÀ GIẢI PHÁP\n";
echo "=================================================================\n\n";

echo "🐛 BUG:\n";
echo "TransactionController line 264:\n";
echo "  \$statsQuery = Transaction::query();\n\n";
echo "Thiếu:\n";
echo "  \$statsQuery = Transaction::whereNull('vehicle_id');\n\n";

echo "🔧 GIẢI PHÁP:\n";
echo "Thêm filter ngay từ đầu:\n";
echo "  \$statsQuery = Transaction::whereNull('vehicle_id');\n\n";

echo "HOẶC:\n";
echo "Thêm whereNull() vào từng query:\n";
echo "  \$totalRevenue = Transaction::whereNull('vehicle_id')\n";
echo "      ->revenue()->where(...)->sum('amount');\n\n";

echo "✓ SAU KHI SỬA:\n";
echo "- Thu công ty: Chỉ tính giao dịch công ty\n";
echo "- Chi công ty: Chỉ tính giao dịch công ty\n";
echo "- Giao dịch xe KHÔNG ảnh hưởng thống kê công ty\n\n";
