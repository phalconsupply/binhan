<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;

echo "=================================================================\n";
echo "KIỂM TRA SỐ DƯ XE 49B08879 CHI TIẾT\n";
echo "=================================================================\n\n";

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

if (!$vehicle) {
    echo "Không tìm thấy xe 49B08879\n";
    exit;
}

echo "🚗 XE: {$vehicle->license_plate} (ID: {$vehicle->id})\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 CÁCH 1: TÍNH THEO LOGIC CŨ (SCOPE)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$totalRevenue = $vehicle->transactions()->revenue()->sum('amount');
$totalExpense = $vehicle->transactions()->expense()->sum('amount');
$totalFundDeposit = $vehicle->transactions()->fundDeposit()->sum('amount');
$totalBorrowed = $vehicle->transactions()->borrowFromCompany()->sum('amount');
$totalReturned = $vehicle->transactions()->returnToCompany()->sum('amount');

echo "Thu:              " . number_format($totalRevenue, 0, ',', '.') . "đ\n";
echo "Chi:              " . number_format($totalExpense, 0, ',', '.') . "đ\n";
echo "Nộp quỹ:          " . number_format($totalFundDeposit, 0, ',', '.') . "đ\n";
echo "Vay công ty:      " . number_format($totalBorrowed, 0, ',', '.') . "đ\n";
echo "Trả công ty:      " . number_format($totalReturned, 0, ',', '.') . "đ\n";
echo "────────────────────────────────────────\n";

$balanceScope = $totalRevenue + $totalFundDeposit + $totalBorrowed - $totalExpense - $totalReturned;
echo "SỐ DƯ (Scope):    " . number_format($balanceScope, 0, ',', '.') . "đ\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 CÁCH 2: TÍNH THEO ACCOUNT TRACKING\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$accountName = "vehicle_{$vehicle->id}";
echo "Account: {$accountName}\n\n";

$allTransactions = Transaction::orderBy('date')->orderBy('id')->get();

$balanceTracking = 0;
$vehicleTransactionCount = 0;

foreach ($allTransactions as $tx) {
    if ($tx->from_account === $accountName) {
        $balanceTracking -= $tx->amount;
        $vehicleTransactionCount++;
    }
    if ($tx->to_account === $accountName) {
        $balanceTracking += $tx->amount;
        $vehicleTransactionCount++;
    }
}

echo "Số giao dịch liên quan: {$vehicleTransactionCount}\n";
echo "SỐ DƯ (Tracking):       " . number_format($balanceTracking, 0, ',', '.') . "đ\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔍 SO SÁNH\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$difference = abs($balanceScope - $balanceTracking);

if ($difference < 0.01) {
    echo "✅ HAI CÁCH TÍNH GIỐNG NHAU\n";
    echo "   Số dư: " . number_format($balanceScope, 0, ',', '.') . "đ\n\n";
} else {
    echo "❌ HAI CÁCH TÍNH KHÁC NHAU!\n";
    echo "   Scope:    " . number_format($balanceScope, 0, ',', '.') . "đ\n";
    echo "   Tracking: " . number_format($balanceTracking, 0, ',', '.') . "đ\n";
    echo "   Chênh lệch: " . number_format($difference, 0, ',', '.') . "đ\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 10 GIAO DỊCH GẦN NHẤT (bao gồm đã xóa)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$recentTransactions = Transaction::withTrashed()
    ->where('vehicle_id', $vehicle->id)
    ->orderBy('date', 'desc')
    ->orderBy('id', 'desc')
    ->limit(10)
    ->get();

foreach ($recentTransactions as $tx) {
    $deleted = $tx->trashed() ? '🗑️ ' : '';
    $type = str_pad($tx->type, 12);
    $amount = str_pad(number_format($tx->amount, 0, ',', '.') . 'đ', 18, ' ', STR_PAD_LEFT);
    
    echo "{$deleted}{$tx->code} | {$tx->date->format('d/m/Y')} | {$type} | {$amount}\n";
    echo "  From: {$tx->from_account} (before: " . ($tx->from_balance_before !== null ? number_format($tx->from_balance_before, 0, ',', '.') : 'NULL') . "đ, after: " . ($tx->from_balance_after !== null ? number_format($tx->from_balance_after, 0, ',', '.') : 'NULL') . "đ)\n";
    echo "  To:   {$tx->to_account} (before: " . ($tx->to_balance_before !== null ? number_format($tx->to_balance_before, 0, ',', '.') : 'NULL') . "đ, after: " . ($tx->to_balance_after !== null ? number_format($tx->to_balance_after, 0, ',', '.') : 'NULL') . "đ)\n";
    
    if ($tx->trashed()) {
        echo "  ⚠️  Deleted at: {$tx->deleted_at}\n";
    }
    echo "\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📈 KIỂM TRA BALANCE CỦA GIAO DỊCH CUỐI CÙNG\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$lastTransaction = Transaction::where('vehicle_id', $vehicle->id)
    ->orderBy('date', 'desc')
    ->orderBy('id', 'desc')
    ->first();

if ($lastTransaction) {
    echo "Giao dịch cuối: {$lastTransaction->code}\n";
    echo "Ngày: {$lastTransaction->date->format('d/m/Y')}\n";
    echo "Type: {$lastTransaction->type}\n";
    echo "Amount: " . number_format($lastTransaction->amount, 0, ',', '.') . "đ\n\n";
    
    if ($lastTransaction->type === 'chi' || $lastTransaction->type === 'tra_cong_ty') {
        echo "Số dư xe sau giao dịch (from_balance_after): ";
        echo $lastTransaction->from_balance_after !== null 
            ? number_format($lastTransaction->from_balance_after, 0, ',', '.') . "đ" 
            : "NULL";
        echo "\n";
    } else {
        echo "Số dư xe sau giao dịch (to_balance_after): ";
        echo $lastTransaction->to_balance_after !== null 
            ? number_format($lastTransaction->to_balance_after, 0, ',', '.') . "đ" 
            : "NULL";
        echo "\n";
    }
    
    echo "\nSố dư tính bằng scope: " . number_format($balanceScope, 0, ',', '.') . "đ\n";
    echo "Số dư tracking:        " . number_format($balanceTracking, 0, ',', '.') . "đ\n\n";
    
    $lastBalance = ($lastTransaction->type === 'chi' || $lastTransaction->type === 'tra_cong_ty')
        ? $lastTransaction->from_balance_after
        : $lastTransaction->to_balance_after;
        
    if ($lastBalance !== null && abs($lastBalance - $balanceScope) > 0.01) {
        echo "⚠️  SỐ DƯ TRONG GIAO DỊCH CUỐI KHÔNG KHỚP VỚI TÍNH TOÁN!\n";
        echo "   Giao dịch cuối: " . number_format($lastBalance, 0, ',', '.') . "đ\n";
        echo "   Tính toán:      " . number_format($balanceScope, 0, ',', '.') . "đ\n";
        echo "   Chênh lệch:     " . number_format(abs($lastBalance - $balanceScope), 0, ',', '.') . "đ\n";
    } else {
        echo "✅ Số dư trong giao dịch cuối khớp với tính toán\n";
    }
}

echo "\n";
