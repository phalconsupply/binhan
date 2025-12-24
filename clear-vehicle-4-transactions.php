<?php
/**
 * Script xóa tất cả giao dịch của xe 49B08879, chỉ giữ lại giao dịch bảo trì
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vehicle;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

echo "=================================================================\n";
echo "XÓA GIAO DỊCH XE 49B08879 (ID = 4)\n";
echo "=================================================================\n\n";

$vehicleId = 4;
$vehicle = Vehicle::find($vehicleId);

if (!$vehicle) {
    echo "❌ Không tìm thấy xe với ID = {$vehicleId}\n";
    exit;
}

echo "Xe: {$vehicle->license_plate} (ID: {$vehicle->id})\n";
if ($vehicle->owner) {
    echo "Chủ xe: {$vehicle->owner->full_name}\n";
}
echo "\n";

// Đếm số giao dịch
$totalTransactions = Transaction::where('vehicle_id', $vehicleId)->count();
$maintenanceTransactions = Transaction::where('vehicle_id', $vehicleId)
    ->whereNotNull('vehicle_maintenance_id')
    ->count();
$otherTransactions = $totalTransactions - $maintenanceTransactions;

echo "📊 THỐNG KÊ GIAO DỊCH:\n";
echo "  Tổng số giao dịch:        {$totalTransactions}\n";
echo "  Giao dịch bảo trì:        {$maintenanceTransactions} (giữ lại)\n";
echo "  Giao dịch khác:           {$otherTransactions} (sẽ xóa)\n\n";

if ($otherTransactions == 0) {
    echo "✅ Không có giao dịch nào cần xóa.\n";
    exit;
}

// Hiển thị chi tiết giao dịch sẽ xóa
echo "📋 CHI TIẾT GIAO DỊCH SẼ XÓA:\n";
$transactionsToDelete = Transaction::where('vehicle_id', $vehicleId)
    ->whereNull('vehicle_maintenance_id')
    ->orderBy('date', 'desc')
    ->get();

$typeCounts = [];
foreach ($transactionsToDelete as $trans) {
    $typeCounts[$trans->type] = ($typeCounts[$trans->type] ?? 0) + 1;
}

foreach ($typeCounts as $type => $count) {
    $label = [
        'thu' => 'Thu',
        'chi' => 'Chi',
        'du_kien_chi' => 'Dự kiến chi',
        'nop_quy' => 'Nộp quỹ',
        'vay_cong_ty' => 'Vay công ty',
        'tra_cong_ty' => 'Trả công ty',
    ][$type] ?? $type;
    echo "  - {$label}: {$count} giao dịch\n";
}

echo "\n";

// Backup dữ liệu trước khi xóa
echo "💾 BACKUP DỮ LIỆU...\n";
$backupFile = __DIR__ . '/backup_transactions_vehicle_4_' . date('YmdHis') . '.json';
$backupData = $transactionsToDelete->toArray();
file_put_contents($backupFile, json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "  ✓ Đã backup vào: {$backupFile}\n\n";

// Xác nhận
echo "⚠️  CẢNH BÁO: Bạn có chắc chắn muốn xóa {$otherTransactions} giao dịch?\n";
echo "   Nhập 'YES' để xác nhận: ";

$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));

if ($line !== 'YES') {
    echo "\n❌ Đã hủy thao tác.\n";
    exit;
}

echo "\n🗑️  ĐANG XÓA GIAO DỊCH...\n";

try {
    DB::beginTransaction();
    
    $deleted = Transaction::where('vehicle_id', $vehicleId)
        ->whereNull('vehicle_maintenance_id')
        ->delete();
    
    DB::commit();
    
    echo "  ✓ Đã xóa thành công {$deleted} giao dịch\n\n";
    
    // Kiểm tra lại
    $remaining = Transaction::where('vehicle_id', $vehicleId)->count();
    $maintenanceRemaining = Transaction::where('vehicle_id', $vehicleId)
        ->whereNotNull('vehicle_maintenance_id')
        ->count();
    
    echo "📊 SAU KHI XÓA:\n";
    echo "  Tổng giao dịch còn lại:   {$remaining}\n";
    echo "  Giao dịch bảo trì:        {$maintenanceRemaining}\n\n";
    
    echo "=================================================================\n";
    echo "✅ HOÀN THÀNH!\n";
    echo "=================================================================\n";
    echo "Backup file: {$backupFile}\n";
    echo "Xe {$vehicle->license_plate} giờ chỉ còn {$remaining} giao dịch bảo trì.\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ LỖI: {$e->getMessage()}\n";
    echo "Dữ liệu đã được backup tại: {$backupFile}\n";
}
