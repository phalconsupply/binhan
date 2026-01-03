<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\AccountBalanceService;

echo "╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║  KIỂM TRA TÊN HIỂN THỊ TÀI KHOẢN MỚI                                 ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

echo "📊 HỆ THỐNG TÊN TÀI KHOẢN:\n\n";

$accounts = [
    'company_fund' => 'Tài khoản lợi nhuận công ty (trước: Quỹ công ty)',
    'company_reserved' => 'Tài khoản dự kiến chi (giữ nguyên)',
    'customer' => 'Khách hàng',
    'vehicle_1' => 'Xe số 1',
    'staff_1' => 'Nhân viên số 1',
    'partner' => 'Đối tác',
    'external' => 'Bên ngoài',
];

foreach ($accounts as $accountCode => $description) {
    $displayName = AccountBalanceService::getAccountDisplayName($accountCode);
    echo "  • {$accountCode}\n";
    echo "    Hiển thị: {$displayName}\n";
    echo "    Mô tả: {$description}\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "📝 VÍ DỤ LUỒNG GIAO DỊCH:\n\n";

$flows = [
    ['from' => 'company_fund', 'to' => 'external', 'description' => 'Chi phí từ lợi nhuận công ty'],
    ['from' => 'company_fund', 'to' => 'company_reserved', 'description' => 'Trích dự kiến chi từ lợi nhuận'],
    ['from' => 'company_reserved', 'to' => 'external', 'description' => 'Chi từ quỹ dự kiến'],
    ['from' => 'vehicle_1', 'to' => 'company_fund', 'description' => 'Nộp quỹ từ xe (lợi nhuận)'],
    ['from' => 'customer', 'to' => 'vehicle_1', 'description' => 'Thu từ khách hàng'],
];

foreach ($flows as $idx => $flow) {
    $fromDisplay = AccountBalanceService::getAccountDisplayName($flow['from']);
    $toDisplay = AccountBalanceService::getAccountDisplayName($flow['to']);
    
    echo "  " . ($idx + 1) . ". {$flow['description']}\n";
    echo "     Luồng: {$fromDisplay} → {$toDisplay}\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "✅ TÓM TẮT THAY ĐỔI:\n";
echo "  • company_fund: 🏢 Quỹ công ty → 💰 Lợi nhuận công ty\n";
echo "  • company_reserved: 📊 Quỹ dự kiến chi (giữ nguyên)\n";
echo "  • Không còn hiển thị 'Khả dụng công ty' riêng lẻ\n";
echo "  • Tất cả giao dịch từ/đến company_fund giờ hiển thị là 'Lợi nhuận công ty'\n\n";

echo "💡 LƯU Ý:\n";
echo "  - Trong database vẫn dùng code: company_fund, company_reserved\n";
echo "  - Chỉ thay đổi cách hiển thị cho người dùng\n";
echo "  - Logic tính toán không thay đổi\n";
