<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use Carbon\Carbon;

echo str_repeat('=', 120) . "\n";
echo "KIEM TRA SO DU QUY CONG TY (company_fund)\n";
echo str_repeat('=', 120) . "\n\n";

// Lấy tất cả giao dịch liên quan đến quỹ công ty
$transactions = Transaction::where(function($q) {
    $q->where('from_account', 'company_fund')
      ->orWhere('to_account', 'company_fund');
})
->orderBy('date')
->orderBy('id')
->get();

echo "Tổng số giao dịch liên quan: " . $transactions->count() . "\n\n";

$balance = 0;
$inCount = 0;
$outCount = 0;
$totalIn = 0;
$totalOut = 0;

echo str_repeat('-', 120) . "\n";
printf("%-12s | %-10s | %-15s | %-10s | %15s | %15s | %-40s\n",
    "Mã GD", "Ngày", "Loại", "Chiều", "Số tiền", "Số dư", "Ghi chú");
echo str_repeat('-', 120) . "\n";

foreach ($transactions as $tx) {
    $before = $balance;
    
    if ($tx->to_account === 'company_fund') {
        // Tiền VÀO quỹ công ty
        $balance += $tx->amount;
        $sign = '+';
        $direction = 'VÀO';
        $inCount++;
        $totalIn += $tx->amount;
    } else {
        // Tiền RA khỏi quỹ công ty
        $balance -= $tx->amount;
        $sign = '-';
        $direction = 'RA';
        $outCount++;
        $totalOut += $tx->amount;
    }
    
    printf("%-12s | %-10s | %-15s | %-10s | %s%14s | %15s | %-40s\n",
        $tx->code ?? 'N/A',
        Carbon::parse($tx->date)->format('d/m/Y'),
        $tx->type,
        $direction,
        $sign,
        number_format($tx->amount, 0, ',', '.'),
        number_format($balance, 0, ',', '.'),
        substr($tx->note ?? '', 0, 40)
    );
}

echo str_repeat('-', 120) . "\n";
echo "\nTONG KET:\n";
echo str_repeat('=', 120) . "\n";
echo "Giao dịch VÀO:  " . $inCount . " giao dịch  | Tổng: +" . number_format($totalIn, 0, ',', '.') . " đ\n";
echo "Giao dịch RA:   " . $outCount . " giao dịch  | Tổng: -" . number_format($totalOut, 0, ',', '.') . " đ\n";
echo str_repeat('-', 120) . "\n";
echo "SO DU QUY CONG TY: " . number_format($balance, 0, ',', '.') . " đ\n";
echo str_repeat('=', 120) . "\n\n";

// Chi tiết theo loại giao dịch
echo "CHI TIET THEO LOAI GIAO DICH:\n";
echo str_repeat('-', 120) . "\n";

$summary = [
    'nop_quy' => ['in' => 0, 'out' => 0, 'count' => 0],
    'tra_cong_ty' => ['in' => 0, 'out' => 0, 'count' => 0],
    'vay_cong_ty' => ['in' => 0, 'out' => 0, 'count' => 0],
    'du_kien_chi' => ['in' => 0, 'out' => 0, 'count' => 0],
    'chi' => ['in' => 0, 'out' => 0, 'count' => 0],
    'thu' => ['in' => 0, 'out' => 0, 'count' => 0],
];

foreach ($transactions as $tx) {
    $type = $tx->type;
    if (!isset($summary[$type])) {
        $summary[$type] = ['in' => 0, 'out' => 0, 'count' => 0];
    }
    
    $summary[$type]['count']++;
    
    if ($tx->to_account === 'company_fund') {
        $summary[$type]['in'] += $tx->amount;
    } else {
        $summary[$type]['out'] += $tx->amount;
    }
}

foreach ($summary as $type => $data) {
    if ($data['count'] > 0) {
        $net = $data['in'] - $data['out'];
        printf("%-15s | %2d GD | Vào: +%14s | Ra: -%14s | Net: %s%14s\n",
            $type,
            $data['count'],
            number_format($data['in'], 0, ',', '.'),
            number_format($data['out'], 0, ',', '.'),
            $net >= 0 ? '+' : '',
            number_format($net, 0, ',', '.')
        );
    }
}

echo str_repeat('=', 120) . "\n";
