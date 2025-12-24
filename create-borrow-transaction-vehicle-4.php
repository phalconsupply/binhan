<?php
/**
 * Script tạo giao dịch vay công ty cho xe 49B08879
 * Ghi nhận việc công ty đã ứng tiền trước cho chi phí bảo trì
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vehicle;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

echo "=================================================================\n";
echo "TẠO GIAO DỊCH VAY CÔNG TY CHO XE 49B08879\n";
echo "=================================================================\n\n";

$vehicleId = 4;
$vehicle = Vehicle::find($vehicleId);

if (!$vehicle) {
    echo "❌ Không tìm thấy xe với ID = {$vehicleId}\n";
    exit;
}

echo "Xe: {$vehicle->license_plate} (ID: {$vehicle->id})\n";
echo "Chủ xe: {$vehicle->owner->full_name}\n\n";

// Tính số tiền cần vay
$totalRevenue = Transaction::where('vehicle_id', $vehicleId)->where('type', 'thu')->sum('amount');
$totalExpense = Transaction::where('vehicle_id', $vehicleId)->where('type', 'chi')->sum('amount');
$deficit = $totalExpense - $totalRevenue;

echo "📊 TÌNH HÌNH TÀI CHÍNH:\n";
echo "  Tổng thu:             " . number_format($totalRevenue, 0, ',', '.') . "đ\n";
echo "  Tổng chi:             " . number_format($totalExpense, 0, ',', '.') . "đ\n";
echo "  ────────────────────────────────\n";
echo "  Thiếu hụt:           " . number_format($deficit, 0, ',', '.') . "đ\n\n";

if ($deficit <= 0) {
    echo "✅ Không có thiếu hụt, không cần vay.\n";
    exit;
}

// Kiểm tra xem đã có giao dịch vay chưa
$existingBorrow = Transaction::where('vehicle_id', $vehicleId)
    ->where('type', 'vay_cong_ty')
    ->exists();

if ($existingBorrow) {
    echo "⚠️  Đã có giao dịch vay từ trước. Bạn có muốn tạo thêm? (YES/NO): ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    if ($line !== 'YES') {
        echo "\n❌ Đã hủy thao tác.\n";
        exit;
    }
}

echo "💡 SẼ TẠO GIAO DỊCH:\n";
echo "  Type:     vay_cong_ty\n";
echo "  Amount:   " . number_format($deficit, 0, ',', '.') . "đ\n";
echo "  Category: vay_tạm_ứng\n";
echo "  Note:     Vay công ty để chi trả bảo trì xe\n";
echo "  Date:     " . date('Y-m-d H:i:s') . "\n\n";

echo "⚠️  Xác nhận tạo giao dịch vay? Nhập 'YES': ";
$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));

if ($line !== 'YES') {
    echo "\n❌ Đã hủy thao tác.\n";
    exit;
}

try {
    DB::beginTransaction();

    // Tạo giao dịch vay
    $transaction = Transaction::create([
        'vehicle_id' => $vehicleId,
        'type' => 'vay_cong_ty',
        'amount' => $deficit,
        'category' => 'vay_tạm_ứng',
        'note' => 'Vay công ty để chi trả bảo trì xe',
        'date' => now(),
        'recorded_by' => 1, // Admin user
        'method' => 'bank',
    ]);

    DB::commit();

    echo "\n✅ ĐÃ TẠO GIAO DỊCH VAY THÀNH CÔNG!\n\n";
    echo "📋 CHI TIẾT:\n";
    echo "  ID giao dịch:  {$transaction->id}\n";
    echo "  Loại:          {$transaction->type_label}\n";
    echo "  Số tiền:       " . number_format($transaction->amount, 0, ',', '.') . "đ\n";
    echo "  Ngày:          {$transaction->date->format('d/m/Y H:i')}\n";
    echo "  Ghi chú:       {$transaction->note}\n\n";

    // Kiểm tra lại số dư
    echo "📊 SAU KHI TẠO GIAO DỊCH VAY:\n";
    $newRevenue = Transaction::where('vehicle_id', $vehicleId)->where('type', 'thu')->sum('amount');
    $newBorrowed = Transaction::where('vehicle_id', $vehicleId)->where('type', 'vay_cong_ty')->sum('amount');
    $newExpense = Transaction::where('vehicle_id', $vehicleId)->where('type', 'chi')->sum('amount');
    $newReturned = Transaction::where('vehicle_id', $vehicleId)->where('type', 'tra_cong_ty')->sum('amount');
    
    $newBalance = $newRevenue + $newBorrowed - $newExpense - $newReturned;
    $currentDebt = $newBorrowed - $newReturned;

    echo "  Tổng thu:              " . number_format($newRevenue, 0, ',', '.') . "đ\n";
    echo "  Vay công ty:           " . number_format($newBorrowed, 0, ',', '.') . "đ\n";
    echo "  Tổng chi:              " . number_format($newExpense, 0, ',', '.') . "đ\n";
    echo "  Đã trả công ty:        " . number_format($newReturned, 0, ',', '.') . "đ\n";
    echo "  ────────────────────────────────\n";
    echo "  Số dư hiện tại:        " . number_format($newBalance, 0, ',', '.') . "đ\n";
    echo "  Đang nợ công ty:       " . number_format($currentDebt, 0, ',', '.') . "đ\n\n";

    echo "=================================================================\n";
    echo "✅ HOÀN THÀNH!\n";
    echo "=================================================================\n";
    echo "Giao dịch vay đã được tạo.\n";
    echo "Chủ xe giờ có thể dùng nút 'Trả nợ' để trả lại công ty.\n";
    echo "Chi tiết xe: /vehicles/{$vehicleId}\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ LỖI: {$e->getMessage()}\n";
}
