<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;
use App\Services\AccountBalanceService;
use Carbon\Carbon;

echo "╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║  KIỂM TRA XỬ LÝ GIAO DỊCH VỚI NGÀY TRONG QUÁ KHỨ                    ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

// Chọn xe 49B08879 (ID: 4)
$vehicle = Vehicle::find(4);

if (!$vehicle) {
    echo "❌ Không tìm thấy xe ID 4\n";
    exit;
}

echo "🚗 XE: {$vehicle->license_plate}\n";
echo "   Có chủ: " . ($vehicle->hasOwner() ? 'CÓ ✓' : 'KHÔNG') . "\n\n";

$accountName = "vehicle_{$vehicle->id}";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 SỐ DƯ HIỆN TẠI CỦA XE:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$currentBalance = AccountBalanceService::getCurrentBalance($accountName);
echo "Số dư hiện tại: " . number_format($currentBalance, 0, ',', '.') . "đ\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📅 LỊCH SỬ 10 GIAO DỊCH GẦN NHẤT:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$recentTx = Transaction::where('vehicle_id', $vehicle->id)
    ->orderBy('date', 'desc')
    ->orderBy('id', 'desc')
    ->limit(10)
    ->get();

foreach ($recentTx as $tx) {
    $balanceDisplay = $tx->type === 'chi' ? $tx->from_balance_after : $tx->to_balance_after;
    echo sprintf(
        "%s | %s | %s %s | %s | Balance: %s\n",
        $tx->code,
        $tx->date->format('d/m/Y H:i'),
        $tx->type === 'chi' ? '❌' : '✅',
        str_pad($tx->type, 3),
        str_pad(number_format($tx->amount, 0, ',', '.') . 'đ', 15, ' ', STR_PAD_LEFT),
        $balanceDisplay !== null ? number_format($balanceDisplay, 0, ',', '.') . 'đ' : 'NULL'
    );
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🧪 SIMULATION: NHẬP GIAO DỊCH CHI VỚI NGÀY TRONG QUÁ KHỨ\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Giả lập tạo giao dịch với ngày 1 tháng trước
$pastDate = Carbon::now()->subMonth();
$testAmount = 500000;

echo "📝 TÌNH HUỐNG:\n";
echo "   - Ngày hiện tại: " . Carbon::now()->format('d/m/Y') . "\n";
echo "   - Ngày giao dịch: " . $pastDate->format('d/m/Y') . " (1 tháng trước)\n";
echo "   - Số tiền chi: " . number_format($testAmount, 0, ',', '.') . "đ\n";
echo "   - Từ tài khoản: {$accountName}\n";
echo "   - Số dư hiện tại xe: " . number_format($currentBalance, 0, ',', '.') . "đ\n\n";

// Tìm giao dịch cuối cùng trước ngày $pastDate
$txBeforePastDate = Transaction::where('vehicle_id', $vehicle->id)
    ->where('date', '<', $pastDate)
    ->orderBy('date', 'desc')
    ->orderBy('id', 'desc')
    ->first();

$txAfterPastDate = Transaction::where('vehicle_id', $vehicle->id)
    ->where('date', '>=', $pastDate)
    ->orderBy('date', 'asc')
    ->orderBy('id', 'asc')
    ->first();

echo "🔍 PHÂN TÍCH:\n\n";

if ($txBeforePastDate) {
    echo "1️⃣ Giao dịch TRƯỚC ngày {$pastDate->format('d/m/Y')}:\n";
    echo "   Code: {$txBeforePastDate->code}\n";
    echo "   Ngày: {$txBeforePastDate->date->format('d/m/Y')}\n";
    $balanceThen = $txBeforePastDate->type === 'chi' ? $txBeforePastDate->from_balance_after : $txBeforePastDate->to_balance_after;
    echo "   Số dư sau GD: " . ($balanceThen !== null ? number_format($balanceThen, 0, ',', '.') . 'đ' : 'NULL') . "\n\n";
} else {
    echo "1️⃣ Không có giao dịch nào trước ngày {$pastDate->format('d/m/Y')}\n\n";
}

if ($txAfterPastDate) {
    echo "2️⃣ Giao dịch SAU ngày {$pastDate->format('d/m/Y')}:\n";
    echo "   Code: {$txAfterPastDate->code}\n";
    echo "   Ngày: {$txAfterPastDate->date->format('d/m/Y')}\n";
    $balanceAfter = $txAfterPastDate->type === 'chi' ? $txAfterPastDate->from_balance_after : $txAfterPastDate->to_balance_after;
    echo "   Số dư sau GD: " . ($balanceAfter !== null ? number_format($balanceAfter, 0, ',', '.') . 'đ' : 'NULL') . "\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "💡 CƠ CHẾ XỬ LÝ CỦA HỆ THỐNG:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "🔄 LUỒNG XỬ LÝ KHI TẠO GIAO DỊCH MỚI:\n\n";

echo "1. Transaction Model - Event 'created':\n";
echo "   → Tự động gọi AccountBalanceService::updateTransactionBalances()\n\n";

echo "2. AccountBalanceService::updateTransactionBalances():\n";
echo "   → Gọi calculateBalance(accountName, transaction->id)\n";
echo "   → Tính số dư TRƯỚC giao dịch mới\n\n";

echo "3. AccountBalanceService::calculateBalance():\n";
echo "   → Query: WHERE id < [new_transaction_id]\n";
echo "   → ORDER BY date, id\n";
echo "   → Tính tổng dựa trên from_account và to_account\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "⚠️  VẤN ĐỀ TIỀM ẨN:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "❌ HIỆN TẠI: HỆ THỐNG TÍNH THEO ID, KHÔNG PHẢI THEO DATE!\n\n";

echo "📌 KHI THÊM GIAO DỊCH VỚI NGÀY TRONG QUÁ KHỨ:\n\n";

echo "Giả sử:\n";
echo "  - GD#100: 01/12/2025 - Chi 1M → Số dư: 10M\n";
echo "  - GD#101: 15/12/2025 - Chi 2M → Số dư: 8M\n";
echo "  - GD#102: 01/01/2026 - Chi 3M → Số dư: 5M (hiện tại)\n\n";

echo "Nếu bạn thêm GD mới với ngày 10/12/2025:\n";
echo "  - GD#103: 10/12/2025 - Chi 500K → ???\n\n";

echo "Hệ thống sẽ:\n";
echo "  ✓ Tính số dư TRƯỚC = Tổng (GD#100, #101, #102) = 5M (SỐ DƯ HIỆN TẠI)\n";
echo "  ✓ Số dư SAU = 5M - 500K = 4.5M\n";
echo "  ✓ Lưu vào DB: from_balance_after = 4.5M\n\n";

echo "❗ NHƯNG:\n";
echo "  - GD#101 và #102 vẫn giữ nguyên số dư cũ (8M và 5M)\n";
echo "  - Không tự động recalculate các GD sau ngày 10/12\n";
echo "  - Dẫn đến số dư KHÔNG CHÍNH XÁC theo timeline!\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ GIẢI PHÁP ĐỀ XUẤT:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "1️⃣ GIẢI PHÁP 1: NGĂN NHẬP GIAO DỊCH QUÁ KHỨ\n";
echo "   - Thêm validation: date >= ngày giao dịch cuối cùng\n";
echo "   - Đơn giản, không ảnh hưởng logic hiện tại\n";
echo "   - Nhược điểm: Không linh hoạt\n\n";

echo "2️⃣ GIẢI PHÁP 2: RECALCULATE TẤT CẢ SAU KHI THÊM\n";
echo "   - Khi thêm GD quá khứ → Recalculate toàn bộ GD sau ngày đó\n";
echo "   - Chính xác 100%\n";
echo "   - Nhược điểm: Chậm nếu có nhiều GD\n\n";

echo "3️⃣ GIẢI PHÁP 3: SẮP XẾP THEO DATE THAY VÌ ID\n";
echo "   - Đổi logic calculateBalance(): ORDER BY date, id thay vì chỉ id\n";
echo "   - Sử dụng date + id để xác định thứ tự\n";
echo "   - Phức tạp hơn nhưng chính xác\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 KẾT LUẬN:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "✓ HỆ THỐNG HIỆN TẠI:\n";
echo "  - Tính số dư dựa trên ID (không phải date)\n";
echo "  - Khi thêm GD quá khứ: Sử dụng số dư HIỆN TẠI\n";
echo "  - KHÔNG tự động cập nhật các GD sau đó\n";
echo "  - Có thể dẫn đến sai lệch số dư\n\n";

echo "⚠️  KHUYẾN NGHỊ:\n";
echo "  - NÊN CHẶN không cho nhập GD với ngày < giao dịch cuối cùng\n";
echo "  - HOẶC tự động recalculate tất cả GD sau khi thêm\n";
echo "  - Hoặc hiển thị cảnh báo rõ ràng cho user\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
