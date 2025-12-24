<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

echo "🔄 Khôi phục giao dịch cho xe 49B08879 từ backup\n\n";

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

if (!$vehicle) {
    echo "❌ Không tìm thấy xe 49B08879\n";
    exit(1);
}

echo "✓ Tìm thấy xe ID: {$vehicle->id}\n\n";

// Read backup file
$backupFile = __DIR__ . '/backup_transactions_vehicle_4_20251224125937.json';

if (!file_exists($backupFile)) {
    echo "❌ Không tìm thấy file backup: {$backupFile}\n";
    exit(1);
}

$backupData = json_decode(file_get_contents($backupFile), true);

if (!$backupData) {
    echo "❌ Không thể đọc file backup\n";
    exit(1);
}

echo "✓ Đọc file backup: " . count($backupData) . " giao dịch\n\n";

// Filter only incident transactions (có incident_id)
$incidentTransactions = array_filter($backupData, function($trans) {
    return !empty($trans['incident_id']);
});

echo "📊 Tìm thấy " . count($incidentTransactions) . " giao dịch có incident_id\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Group by incident
$groupedByIncident = [];
foreach ($incidentTransactions as $trans) {
    $incidentId = $trans['incident_id'];
    if (!isset($groupedByIncident[$incidentId])) {
        $groupedByIncident[$incidentId] = [];
    }
    $groupedByIncident[$incidentId][] = $trans;
}

echo "📋 Giao dịch theo chuyến:\n";
foreach ($groupedByIncident as $incidentId => $transactions) {
    $revenueTransactions = array_filter($transactions, fn($t) => $t['type'] === 'thu');
    $expenseTransactions = array_filter($transactions, fn($t) => $t['type'] === 'chi');
    
    $revenue = 0;
    foreach ($revenueTransactions as $t) {
        $revenue += floatval($t['amount']);
    }
    
    $expense = 0;
    foreach ($expenseTransactions as $t) {
        $expense += floatval($t['amount']);
    }
    
    echo "  Chuyến #{$incidentId}: " . count($transactions) . " giao dịch (Thu: " . number_format($revenue) . "đ, Chi: " . number_format($expense) . "đ)\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "⚠️  LƯU Ý:\n";
echo "   - Script này chỉ hiển thị dữ liệu, CHƯA THỰC HIỆN khôi phục\n";
echo "   - Giao dịch bảo trì (không có incident_id) sẽ KHÔNG được khôi phục\n";
echo "   - Chỉ khôi phục giao dịch của các chuyến đi\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "💡 Để khôi phục, bạn cần:\n";
echo "   1. Xác nhận các chuyến đi cần khôi phục\n";
echo "   2. Chạy script với tham số --execute để thực hiện\n";
echo "\nBạn có muốn tiếp tục khôi phục không? (yes/no): ";

$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));

if (strtolower($line) !== 'yes') {
    echo "\n❌ Hủy khôi phục\n";
    exit(0);
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔄 Bắt đầu khôi phục...\n\n";

DB::beginTransaction();

try {
    $restored = 0;
    $skipped = 0;
    
    foreach ($incidentTransactions as $trans) {
        // Check if transaction already exists
        $exists = Transaction::where('incident_id', $trans['incident_id'])
            ->where('type', $trans['type'])
            ->where('amount', $trans['amount'])
            ->where('date', $trans['date'])
            ->exists();
        
        if ($exists) {
            $skipped++;
            continue;
        }
        
        // Create transaction (without code, it will auto-generate)
        Transaction::create([
            'incident_id' => $trans['incident_id'],
            'staff_id' => $trans['staff_id'],
            'vehicle_id' => $trans['vehicle_id'],
            'vehicle_maintenance_id' => $trans['vehicle_maintenance_id'],
            'type' => $trans['type'],
            'category' => $trans['category'],
            'transaction_category' => $trans['transaction_category'],
            'amount' => $trans['amount'],
            'method' => $trans['method'],
            'payment_method' => $trans['payment_method'],
            'note' => $trans['note'],
            'recorded_by' => $trans['recorded_by'],
            'date' => $trans['date'],
        ]);
        
        $restored++;
        
        if ($restored % 10 == 0) {
            echo "✓ Đã khôi phục {$restored} giao dịch...\n";
        }
    }
    
    DB::commit();
    
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ Hoàn tất khôi phục!\n";
    echo "   - Đã khôi phục: {$restored} giao dịch\n";
    echo "   - Đã bỏ qua (trùng): {$skipped} giao dịch\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}
