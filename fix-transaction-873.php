<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

echo "🔄 Tạo giao dịch thu bổ sung cho #873\n\n";

$repayTransaction = Transaction::find(873);

if (!$repayTransaction) {
    echo "❌ Không tìm thấy giao dịch #873\n";
    exit(1);
}

echo "✓ Tìm thấy giao dịch trả nợ:\n";
echo "  ID: #{$repayTransaction->id}\n";
echo "  Mã: {$repayTransaction->code}\n";
echo "  Loại: {$repayTransaction->type}\n";
echo "  Số tiền: " . number_format($repayTransaction->amount) . "đ\n";
echo "  Xe: {$repayTransaction->vehicle->license_plate}\n\n";

// Check if revenue transaction already exists
$existingRevenue = Transaction::where('type', 'thu')
    ->whereNull('vehicle_id')
    ->where('note', 'LIKE', '%GD #873%')
    ->first();

if ($existingRevenue) {
    echo "⚠️ Đã có giao dịch thu tương ứng:\n";
    echo "  ID: #{$existingRevenue->id}\n";
    echo "  Mã: {$existingRevenue->code}\n";
    exit(0);
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔄 Tạo giao dịch thu cho công ty...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

DB::beginTransaction();

try {
    $revenueTransaction = Transaction::create([
        'vehicle_id' => null,
        'type' => 'thu',
        'amount' => $repayTransaction->amount,
        'category' => null,
        'note' => 'Thu từ xe ' . $repayTransaction->vehicle->license_plate . ' trả nợ (GD #' . $repayTransaction->id . ')',
        'date' => $repayTransaction->date,
        'recorded_by' => $repayTransaction->recorded_by,
        'method' => $repayTransaction->method,
    ]);
    
    DB::commit();
    
    echo "✅ Đã tạo giao dịch thu thành công!\n";
    echo "  ID: #{$revenueTransaction->id}\n";
    echo "  Mã: {$revenueTransaction->code}\n";
    echo "  Số tiền: " . number_format($revenueTransaction->amount) . "đ\n\n";
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📊 Lợi nhuận công ty đã tăng: +" . number_format($revenueTransaction->amount) . "đ\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}
