<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;
use Carbon\Carbon;

echo "KIEM TRA GROUP 'GIAO DICH KHAC'\n";
echo str_repeat('=', 100) . "\n\n";

// Lấy transactions không có incident_id, không có vehicle_maintenance_id, không phải nop_quy, dividend
// Filter giống logic controller: dùng note thay vì category
$allOtherCandidates = Transaction::query()
    ->whereNull('incident_id')
    ->whereNull('vehicle_maintenance_id')
    ->where('type', '!=', 'nop_quy')
    ->orderBy('date', 'desc')
    ->limit(50)
    ->get();

$otherTransactions = $allOtherCandidates->reject(function($t) {
    return str_contains($t->note ?? '', 'Chia cổ tức');
});

echo "TONG SO GIAO DICH KHAC: " . $otherTransactions->count() . "\n";
echo str_repeat('-', 100) . "\n\n";

// Phân loại theo type
$thuTransactions = $otherTransactions->where('type', 'thu');
$chiTransactions = $otherTransactions->where('type', 'chi');
$duKienChiTransactions = $otherTransactions->where('type', 'du_kien_chi');

echo "GIAO DICH THU (type = 'thu'):\n";
$totalThu = 0;
foreach ($thuTransactions as $t) {
    echo sprintf("  ID %-4s | %s | +%s | Vehicle: %s | %s\n", 
        $t->id,
        Carbon::parse($t->date)->format('d/m/Y'),
        str_pad(number_format($t->amount, 0, ',', '.'), 15, ' ', STR_PAD_LEFT),
        $t->vehicle_id ? $t->vehicle->license_plate : 'N/A',
        substr($t->note ?? 'N/A', 0, 40)
    );
    $totalThu += $t->amount;
}
echo "=> TONG THU: " . number_format($totalThu, 0, ',', '.') . "d\n\n";

echo "GIAO DICH CHI (type = 'chi'):\n";
$totalChi = 0;
foreach ($chiTransactions as $t) {
    echo sprintf("  ID %-4s | %s | -%s | Vehicle: %s | %s\n", 
        $t->id,
        Carbon::parse($t->date)->format('d/m/Y'),
        str_pad(number_format($t->amount, 0, ',', '.'), 15, ' ', STR_PAD_LEFT),
        $t->vehicle_id ? $t->vehicle->license_plate : 'N/A',
        substr($t->note ?? 'N/A', 0, 40)
    );
    $totalChi += $t->amount;
}
echo "=> TONG CHI: " . number_format($totalChi, 0, ',', '.') . "d\n\n";

echo "GIAO DICH DU KIEN CHI (type = 'du_kien_chi'):\n";
$totalDuKien = 0;
foreach ($duKienChiTransactions as $t) {
    echo sprintf("  ID %-4s | %s | -%s | Vehicle: %s | %s\n", 
        $t->id,
        Carbon::parse($t->date)->format('d/m/Y'),
        str_pad(number_format($t->amount, 0, ',', '.'), 15, ' ', STR_PAD_LEFT),
        $t->vehicle_id ? $t->vehicle->license_plate : 'N/A',
        substr($t->note ?? 'N/A', 0, 40)
    );
    $totalDuKien += $t->amount;
}
echo "=> TONG DU KIEN CHI: " . number_format($totalDuKien, 0, ',', '.') . "d\n\n";

echo str_repeat('=', 100) . "\n";
echo "TONG KET GROUP 'GIAO DICH KHAC':\n";
echo "  total_revenue (thu):         " . number_format($totalThu, 0, ',', '.') . "d\n";
echo "  total_expense (chi):         " . number_format($totalChi, 0, ',', '.') . "d\n";
echo "  total_planned_expense:       " . number_format($totalDuKien, 0, ',', '.') . "d\n";
echo "  net_amount (thu - chi - dk): " . number_format($totalThu - $totalChi - $totalDuKien, 0, ',', '.') . "d\n";

// Kiểm tra theo logic controller
$controllerTotalRevenue = $otherTransactions->where('type', 'thu')->sum('amount');
$controllerTotalExpense = $otherTransactions->where('type', 'chi')->sum('amount');
$controllerTotalPlanned = $otherTransactions->where('type', 'du_kien_chi')->sum('amount');

echo "\nSO SANH VOI LOGIC CONTROLLER:\n";
echo "  Controller total_revenue: " . number_format($controllerTotalRevenue, 0, ',', '.') . "d";
echo ($controllerTotalRevenue == $totalThu ? " ✓ KHOP\n" : " ✗ SAI\n");

echo "  Controller total_expense: " . number_format($controllerTotalExpense, 0, ',', '.') . "d";
echo ($controllerTotalExpense == $totalChi ? " ✓ KHOP\n" : " ✗ SAI\n");

echo "  Controller total_planned: " . number_format($controllerTotalPlanned, 0, ',', '.') . "d";
echo ($controllerTotalPlanned == $totalDuKien ? " ✓ KHOP\n" : " ✗ SAI\n");
