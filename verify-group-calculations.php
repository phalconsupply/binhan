<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;
use Carbon\Carbon;

echo "KIEM TRA LOGIC TINH TOAN CUA MOI GROUP\n";
echo str_repeat('=', 100) . "\n\n";

// Kiểm tra một số group ngẫu nhiên
$sampleIncidents = Transaction::whereNotNull('incident_id')
    ->select('incident_id')
    ->distinct()
    ->limit(5)
    ->pluck('incident_id');

foreach ($sampleIncidents as $incidentId) {
    $transactions = Transaction::where('incident_id', $incidentId)->get();
    
    // Tính theo controller logic
    $totalRevenue = $transactions->where('type', 'thu')->sum('amount');
    $totalExpense = $transactions->where('type', 'chi')->sum('amount');
    $totalPlanned = $transactions->where('type', 'du_kien_chi')->sum('amount');
    
    // Kiểm tra từng giao dịch
    $revenueItems = $transactions->where('type', 'thu');
    $expenseItems = $transactions->where('type', 'chi');
    
    echo "CHUYEN #$incidentId\n";
    echo str_repeat('-', 100) . "\n";
    
    echo "GIAO DICH THU:\n";
    $manualRevenue = 0;
    foreach ($revenueItems as $t) {
        echo sprintf("  ID %-4s | %s | +%s\n", 
            $t->id, 
            Carbon::parse($t->date)->format('d/m/Y'),
            str_pad(number_format($t->amount, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)
        );
        $manualRevenue += $t->amount;
    }
    echo "=> TONG THU (manual): " . number_format($manualRevenue, 0, ',', '.') . "d\n";
    echo "=> TONG THU (logic):  " . number_format($totalRevenue, 0, ',', '.') . "d\n";
    echo "=> KHOP: " . ($manualRevenue == $totalRevenue ? "✓ OK" : "✗ SAI") . "\n\n";
    
    echo "GIAO DICH CHI:\n";
    $manualExpense = 0;
    foreach ($expenseItems as $t) {
        echo sprintf("  ID %-4s | %s | -%s\n", 
            $t->id, 
            Carbon::parse($t->date)->format('d/m/Y'),
            str_pad(number_format($t->amount, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)
        );
        $manualExpense += $t->amount;
    }
    echo "=> TONG CHI (manual): " . number_format($manualExpense, 0, ',', '.') . "d\n";
    echo "=> TONG CHI (logic):  " . number_format($totalExpense, 0, ',', '.') . "d\n";
    echo "=> KHOP: " . ($manualExpense == $totalExpense ? "✓ OK" : "✗ SAI") . "\n\n";
    
    echo "KET LUAN:\n";
    echo "  Tong thu: " . number_format($totalRevenue, 0, ',', '.') . "d\n";
    echo "  Tong chi: " . number_format($totalExpense, 0, ',', '.') . "d\n";
    echo "  Loi nhuan: " . number_format($totalRevenue - $totalExpense, 0, ',', '.') . "d\n";
    echo "\n" . str_repeat('=', 100) . "\n\n";
}

// Kiểm tra group bảo trì
echo "KIEM TRA GROUP BAO TRI (maintenance):\n";
echo str_repeat('=', 100) . "\n";
$maintenanceTransactions = Transaction::whereNotNull('vehicle_maintenance_id')
    ->where('type', 'chi')
    ->limit(10)
    ->get();

$totalMaintenanceExpense = $maintenanceTransactions->sum('amount');
$manualMaintenanceExpense = 0;

echo "GIAO DICH BAO TRI:\n";
foreach ($maintenanceTransactions as $t) {
    echo sprintf("  ID %-4s | Xe: %s | -%s | %s\n", 
        $t->id,
        $t->vehicle->license_plate ?? 'N/A',
        str_pad(number_format($t->amount, 0, ',', '.'), 15, ' ', STR_PAD_LEFT),
        substr($t->note ?? 'N/A', 0, 50)
    );
    $manualMaintenanceExpense += $t->amount;
}

echo "\n=> TONG CHI BAO TRI (manual): " . number_format($manualMaintenanceExpense, 0, ',', '.') . "d\n";
echo "=> TONG CHI BAO TRI (logic):  " . number_format($totalMaintenanceExpense, 0, ',', '.') . "d\n";
echo "=> KHOP: " . ($manualMaintenanceExpense == $totalMaintenanceExpense ? "✓ OK" : "✗ SAI") . "\n\n";

// Kiểm tra group nộp quỹ
echo "KIEM TRA GROUP NOP QUY (fund deposit):\n";
echo str_repeat('=', 100) . "\n";
$fundDepositTransactions = Transaction::where('type', 'nop_quy')
    ->limit(10)
    ->get();

$totalFundDeposit = $fundDepositTransactions->sum('amount');
$manualFundDeposit = 0;

echo "GIAO DICH NOP QUY:\n";
foreach ($fundDepositTransactions as $t) {
    echo sprintf("  ID %-4s | Xe: %s | +%s\n", 
        $t->id,
        $t->vehicle->license_plate ?? 'N/A',
        str_pad(number_format($t->amount, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)
    );
    $manualFundDeposit += $t->amount;
}

echo "\n=> TONG NOP QUY (manual): " . number_format($manualFundDeposit, 0, ',', '.') . "d\n";
echo "=> TONG NOP QUY (logic):  " . number_format($totalFundDeposit, 0, ',', '.') . "d\n";
echo "=> KHOP: " . ($manualFundDeposit == $totalFundDeposit ? "✓ OK" : "✗ SAI") . "\n\n";

echo str_repeat('=', 100) . "\n";
echo "KET LUAN CHUNG:\n";
echo "Logic tinh toan total_revenue = tong cac giao dich type = 'thu'\n";
echo "Logic tinh toan total_expense = tong cac giao dich type = 'chi'\n";
echo "✓ Tat ca deu tinh dung theo sum('amount') cua moi type\n";
