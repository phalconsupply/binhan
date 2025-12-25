<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;

echo "=== TẤT CẢ CÁC LOẠI TYPE TRONG HỆ THỐNG ===\n\n";

$types = Transaction::select('type', \DB::raw('COUNT(*) as count'), \DB::raw('SUM(amount) as total'))
    ->groupBy('type')
    ->orderBy('type')
    ->get();

foreach ($types as $type) {
    echo sprintf(
        "%-20s | %5d transactions | Total: %s VND\n",
        $type->type,
        $type->count,
        number_format($type->total)
    );
}

echo "\n=== MÔ TẢ CÁC LOẠI TYPE ===\n\n";
echo "thu          : Giao dịch thu (revenue)\n";
echo "chi          : Giao dịch chi (expense)\n";
echo "nop_quy      : Nộp quỹ (fund deposit)\n";
echo "vay_cong_ty  : Vay từ công ty (borrow from company)\n";
echo "tra_cong_ty  : Trả nợ công ty (return to company)\n";
echo "du_kien_chi  : Dự kiến chi (planned expense)\n";
