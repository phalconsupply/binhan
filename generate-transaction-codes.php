<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

echo "🔄 Bắt đầu tạo mã giao dịch cho các giao dịch cũ...\n\n";

DB::beginTransaction();

try {
    // Get all transactions without code
    $transactions = Transaction::whereNull('code')
        ->orWhere('code', '')
        ->orderBy('id')
        ->get();

    echo "📊 Tìm thấy " . $transactions->count() . " giao dịch chưa có mã\n\n";

    $updated = 0;

    foreach ($transactions as $transaction) {
        // Generate code: GD{YYYYMMDD}-{ID}
        $date = $transaction->date ? $transaction->date->format('Ymd') : now()->format('Ymd');
        $code = "GD{$date}-" . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        
        // Update without triggering events
        DB::table('transactions')
            ->where('id', $transaction->id)
            ->update(['code' => $code]);
        
        $updated++;
        
        if ($updated % 100 == 0) {
            echo "✓ Đã cập nhật {$updated} giao dịch...\n";
        }
    }

    DB::commit();

    echo "\n✅ Hoàn tất! Đã tạo mã cho {$updated} giao dịch\n";
    echo "📋 Ví dụ: " . Transaction::whereNotNull('code')->first()->code . "\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}
