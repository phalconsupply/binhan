<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;

echo "=================================================================\n";
echo "SO SÁNH SỐ DƯ: 28.674.575đ vs 35.789.575đ\n";
echo "=================================================================\n\n";

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

echo "🚗 XE: {$vehicle->license_plate} (ID: {$vehicle->id})\n\n";

// Tính số dư hiện tại
$currentBalance = 35789575;
$expectedBalance = 28674575;
$difference = $currentBalance - $expectedBalance;

echo "Số dư hiện tại:  " . number_format($currentBalance, 0, ',', '.') . "đ\n";
echo "Số dư mong đợi:  " . number_format($expectedBalance, 0, ',', '.') . "đ\n";
echo "Chênh lệch:      " . number_format($difference, 0, ',', '.') . "đ\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 TẤT CẢ GIAO DỊCH CỦA XE (bao gồm deleted)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$allTx = Transaction::withTrashed()
    ->where('vehicle_id', $vehicle->id)
    ->orderBy('date')
    ->orderBy('id')
    ->get();

$totalRevenue = 0;
$totalExpense = 0;
$totalFundDeposit = 0;
$runningBalance = 0;

echo sprintf("%-20s | %-12s | %-10s | %15s | %15s | %s\n", 
    "Code", "Date", "Type", "Amount", "Balance", "Status");
echo str_repeat("─", 120) . "\n";

foreach ($allTx as $tx) {
    $status = $tx->trashed() ? '🗑️ DELETED' : '✅ ACTIVE';
    
    if (!$tx->trashed()) {
        if ($tx->type === 'thu') {
            $totalRevenue += $tx->amount;
            $runningBalance += $tx->amount;
        } elseif ($tx->type === 'chi') {
            $totalExpense += $tx->amount;
            $runningBalance -= $tx->amount;
        } elseif ($tx->type === 'nop_quy') {
            $totalFundDeposit += $tx->amount;
            $runningBalance -= $tx->amount;
        }
    }
    
    echo sprintf("%-20s | %-12s | %-10s | %15s | %15s | %s\n",
        $tx->code,
        $tx->date->format('d/m/Y'),
        $tx->type,
        number_format($tx->amount, 0, ',', '.') . 'đ',
        $tx->trashed() ? '-' : number_format($runningBalance, 0, ',', '.') . 'đ',
        $status
    );
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 TỔNG HỢP (CHỈ GIAO DỊCH ACTIVE)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "Thu:         " . number_format($totalRevenue, 0, ',', '.') . "đ\n";
echo "Chi:         " . number_format($totalExpense, 0, ',', '.') . "đ\n";
echo "Nộp quỹ:     " . number_format($totalFundDeposit, 0, ',', '.') . "đ\n";
echo "──────────────────────────────────\n";
echo "SỐ DƯ:       " . number_format($runningBalance, 0, ',', '.') . "đ\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔍 PHÂN TÍCH CHÊNH LỆCH 7.115.000đ\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Tìm giao dịch có giá trị ~7.115.000đ
$similarTx = Transaction::withTrashed()
    ->where('vehicle_id', $vehicle->id)
    ->where('amount', '>=', 7000000)
    ->where('amount', '<=', 7200000)
    ->get();

if ($similarTx->count() > 0) {
    echo "Tìm thấy giao dịch có giá trị tương tự 7.115.000đ:\n\n";
    foreach ($similarTx as $tx) {
        $status = $tx->trashed() ? '🗑️ ĐÃ XÓA' : '✅ ĐANG TỒN TẠI';
        echo "• {$tx->code} - {$status}\n";
        echo "  Type:   {$tx->type}\n";
        echo "  Amount: " . number_format($tx->amount, 0, ',', '.') . "đ\n";
        echo "  Date:   {$tx->date->format('d/m/Y')}\n\n";
    }
} else {
    echo "Không tìm thấy giao dịch nào có giá trị ~7.115.000đ\n\n";
}

// Kiểm tra xem có giao dịch nào bị restore không
$deletedTx = Transaction::onlyTrashed()
    ->where('vehicle_id', $vehicle->id)
    ->get();

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🗑️  DANH SÁCH GIAO DỊCH ĐÃ XÓA\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$totalDeletedExpense = 0;
$totalDeletedRevenue = 0;

foreach ($deletedTx as $tx) {
    echo "• {$tx->code} | {$tx->type} | " . number_format($tx->amount, 0, ',', '.') . "đ | Xóa: {$tx->deleted_at->format('d/m/Y H:i')}\n";
    
    if ($tx->type === 'chi') {
        $totalDeletedExpense += $tx->amount;
    } elseif ($tx->type === 'thu') {
        $totalDeletedRevenue += $tx->amount;
    }
}

echo "\nTổng chi đã xóa:  " . number_format($totalDeletedExpense, 0, ',', '.') . "đ\n";
echo "Tổng thu đã xóa:  " . number_format($totalDeletedRevenue, 0, ',', '.') . "đ\n";

echo "\n";
