<?php
/**
 * Script kiểm tra logic vay tiền từ công ty cho xe có chủ
 * 
 * Tình huống:
 * 1. Xe có chủ có lợi nhuận dương nhưng chưa đủ để chi tiêu
 * 2. Khi chi tiền, nếu số dư lợi nhuận không đủ -> tự động tạo giao dịch vay từ công ty
 * 3. Giao dịch vay: type = 'vay_cong_ty', cộng vào tài khoản chủ xe
 * 4. Khi có lợi nhuận sau -> tạo giao dịch trả: type = 'tra_cong_ty', trừ khỏi tài khoản chủ xe
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vehicle;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

echo "=================================================================\n";
echo "KIỂM TRA LOGIC VAY TIỀN TỪ CÔNG TY\n";
echo "=================================================================\n\n";

// Tìm xe có chủ xe
$vehicle = Vehicle::whereHas('owner')->first();

if (!$vehicle) {
    echo "❌ Không tìm thấy xe có chủ xe\n";
    exit;
}

echo "Xe: {$vehicle->license_plate}\n";
echo "Chủ xe: {$vehicle->owner->full_name}\n\n";

// Tính số dư hiện tại
$tongThu = $vehicle->transactions()->revenue()->sum('amount');
$nopQuy = $vehicle->transactions()->fundDeposit()->sum('amount');
$tongChi = $vehicle->transactions()->expense()->sum('amount');
$tongVay = $vehicle->transactions()->borrowFromCompany()->sum('amount');
$tongTra = $vehicle->transactions()->returnToCompany()->sum('amount');

$soDu = $tongThu + $nopQuy - $tongChi + $tongVay - $tongTra;

echo "📊 Số dư hiện tại:\n";
echo "  Tổng thu:     " . number_format($tongThu, 0, ',', '.') . "đ\n";
echo "  Nộp quỹ:      " . number_format($nopQuy, 0, ',', '.') . "đ\n";
echo "  Tổng chi:     " . number_format($tongChi, 0, ',', '.') . "đ\n";
echo "  Đã vay:       " . number_format($tongVay, 0, ',', '.') . "đ\n";
echo "  Đã trả:       " . number_format($tongTra, 0, ',', '.') . "đ\n";
echo "  ─────────────────────────────\n";
echo "  Số dư:        " . number_format($soDu, 0, ',', '.') . "đ\n\n";

$soDuVay = $tongVay - $tongTra;
if ($soDuVay > 0) {
    echo "⚠️  Đang mượn công ty: " . number_format($soDuVay, 0, ',', '.') . "đ\n\n";
} else {
    echo "✅ Không có nợ công ty\n\n";
}

echo "=================================================================\n";
echo "MÔ PHỎNG TÌNH HUỐNG VAY TIỀN\n";
echo "=================================================================\n\n";

// Giả sử cần chi 5,000,000đ nhưng số dư chỉ còn ít
$soTienCanChi = 5000000;

echo "Cần chi: " . number_format($soTienCanChi, 0, ',', '.') . "đ\n";
echo "Số dư:   " . number_format($soDu, 0, ',', '.') . "đ\n\n";

if ($soDu < $soTienCanChi) {
    $soTienThieu = $soTienCanChi - $soDu;
    echo "⚠️  Thiếu: " . number_format($soTienThieu, 0, ',', '.') . "đ\n\n";
    echo "💡 GIẢI PHÁP:\n";
    echo "1. Tạo giao dịch vay từ công ty: " . number_format($soTienThieu, 0, ',', '.') . "đ\n";
    echo "   - Type: vay_cong_ty\n";
    echo "   - Cộng vào tài khoản chủ xe\n";
    echo "   - Trừ khỏi tài khoản công ty\n\n";
    echo "2. Sau đó mới chi tiền: " . number_format($soTienCanChi, 0, ',', '.') . "đ\n\n";
    
    echo "🔔 Lưu ý: Khoản vay sẽ được thống kê riêng và hiển thị cảnh báo\n";
} else {
    echo "✅ Đủ tiền để chi, không cần vay\n";
}

echo "\n=================================================================\n";
echo "CẤU TRÚC GIAO DỊCH VAY - TRẢ\n";
echo "=================================================================\n\n";

echo "GIAO DỊCH VAY (type = 'vay_cong_ty'):\n";
echo "  - vehicle_id: ID của xe\n";
echo "  - type: 'vay_cong_ty'\n";
echo "  - amount: Số tiền vay\n";
echo "  - note: 'Vay từ công ty để chi trả'\n";
echo "  - category: 'vay_tạm_ứng'\n\n";

echo "GIAO DỊCH TRẢ (type = 'tra_cong_ty'):\n";
echo "  - vehicle_id: ID của xe\n";
echo "  - type: 'tra_cong_ty'\n";
echo "  - amount: Số tiền trả\n";
echo "  - note: 'Trả nợ công ty'\n";
echo "  - category: 'hoàn_trả'\n\n";

echo "=================================================================\n";
echo "HIỂN THỊ TRONG VIEW\n";
echo "=================================================================\n\n";

echo "Nếu có nợ công ty > 0, hiển thị cảnh báo:\n";
echo "┌─────────────────────────────────────────────────────────────┐\n";
echo "│ ⚠️  Đang vay từ công ty: 10.000.000đ                        │\n";
echo "│                                                             │\n";
echo "│ Chủ xe đang mượn tiền từ công ty để chi trả.               │\n";
echo "│ Số tiền này cần được hoàn trả lại.                         │\n";
echo "│ Tháng này: +5.000.000đ                                      │\n";
echo "└─────────────────────────────────────────────────────────────┘\n\n";

echo "=================================================================\n";
