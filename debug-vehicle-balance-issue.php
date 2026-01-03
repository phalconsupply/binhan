<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;

echo "=================================================================\n";
echo "KIỂM TRA LỖI SỐ DƯ SAU KHI CHI TIỀN TỪ XE CÓ CHỦ\n";
echo "=================================================================\n\n";

// Kiểm tra các giao dịch cụ thể
$codes = ['GD20260101-0911', 'GD20251226-0910'];

foreach ($codes as $code) {
    $tx = Transaction::withTrashed()->where('code', $code)->first();
    
    if (!$tx) {
        echo "⚠️  Không tìm thấy giao dịch: {$code}\n\n";
        continue;
    }
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📋 GIAO DỊCH: {$tx->code}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "  ID:           {$tx->id}\n";
    echo "  Type:         {$tx->type}\n";
    echo "  Category:     " . ($tx->category ?? 'NULL') . "\n";
    echo "  Amount:       " . number_format($tx->amount, 0, ',', '.') . "đ\n";
    echo "  Date:         {$tx->date}\n";
    echo "  Deleted:      " . ($tx->trashed() ? 'CÓ (deleted_at: ' . $tx->deleted_at . ')' : 'KHÔNG') . "\n";
    
    if ($tx->vehicle_id) {
        $vehicle = $tx->vehicle;
        echo "  Vehicle:      {$vehicle->license_plate} (ID: {$vehicle->id})\n";
        echo "  Có chủ xe:    " . ($vehicle->hasOwner() ? 'CÓ ✓' : 'KHÔNG') . "\n";
    } else {
        echo "  Vehicle:      NULL (Giao dịch công ty)\n";
    }
    
    echo "\n  📊 ACCOUNT TRACKING:\n";
    echo "  From Account:        " . ($tx->from_account ?? 'NULL') . "\n";
    echo "  To Account:          " . ($tx->to_account ?? 'NULL') . "\n";
    echo "  From Balance Before: " . ($tx->from_balance_before !== null ? number_format($tx->from_balance_before, 0, ',', '.') . 'đ' : 'NULL') . "\n";
    echo "  From Balance After:  " . ($tx->from_balance_after !== null ? number_format($tx->from_balance_after, 0, ',', '.') . 'đ' : 'NULL') . "\n";
    echo "  To Balance Before:   " . ($tx->to_balance_before !== null ? number_format($tx->to_balance_before, 0, ',', '.') . 'đ' : 'NULL') . "\n";
    echo "  To Balance After:    " . ($tx->to_balance_after !== null ? number_format($tx->to_balance_after, 0, ',', '.') . 'đ' : 'NULL') . "\n";
    
    if ($tx->from_account) {
        echo "  Display Flow:        {$tx->account_flow_display}\n";
    }
    
    echo "\n";
}

// Tìm giao dịch đã xóa gần đây
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🗑️  GIAO DỊCH ĐÃ XÓA GẦN ĐÂY (7 ngày)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$deletedTransactions = Transaction::onlyTrashed()
    ->where('deleted_at', '>=', now()->subDays(7))
    ->whereNotNull('vehicle_id')
    ->orderBy('deleted_at', 'desc')
    ->get();

if ($deletedTransactions->count() > 0) {
    foreach ($deletedTransactions as $tx) {
        echo sprintf(
            "%s | %s | Type: %s | %s | Vehicle: %s | Deleted: %s\n",
            $tx->code,
            $tx->date->format('d/m/Y H:i'),
            str_pad($tx->type, 10),
            str_pad(number_format($tx->amount, 0, ',', '.') . 'đ', 15, ' ', STR_PAD_LEFT),
            $tx->vehicle ? $tx->vehicle->license_plate : 'N/A',
            $tx->deleted_at->format('d/m/Y H:i')
        );
    }
} else {
    echo "Không có giao dịch nào bị xóa trong 7 ngày qua.\n";
}

echo "\n";

// Kiểm tra xe cụ thể nếu có trong các giao dịch trên
$firstTx = Transaction::withTrashed()->where('code', 'GD20260101-0911')->first();
if ($firstTx && $firstTx->vehicle_id) {
    $vehicle = $firstTx->vehicle;
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🚗 XE: {$vehicle->license_plate} - PHÂN TÍCH SỐ DƯ\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    // Tính số dư theo 2 cách
    
    // Cách 1: Theo logic cũ (không dùng account tracking)
    $totalRevenue = $vehicle->transactions()->revenue()->sum('amount');
    $totalExpense = $vehicle->transactions()->expense()->sum('amount');
    $totalFundDeposit = $vehicle->transactions()->fundDeposit()->sum('amount');
    $totalBorrowed = $vehicle->transactions()->borrowFromCompany()->sum('amount');
    $totalReturned = $vehicle->transactions()->returnToCompany()->sum('amount');
    
    $balanceOldLogic = $totalRevenue + $totalFundDeposit + $totalBorrowed - $totalExpense - $totalReturned;
    
    echo "📊 CÁCH 1: Tính theo logic cũ (scope)\n";
    echo "  Thu:         " . number_format($totalRevenue, 0, ',', '.') . "đ\n";
    echo "  Nộp quỹ:     " . number_format($totalFundDeposit, 0, ',', '.') . "đ\n";
    echo "  Vay công ty: " . number_format($totalBorrowed, 0, ',', '.') . "đ\n";
    echo "  Chi:         " . number_format($totalExpense, 0, ',', '.') . "đ\n";
    echo "  Trả công ty: " . number_format($totalReturned, 0, ',', '.') . "đ\n";
    echo "  ────────────────────────────────\n";
    echo "  SỐ DƯ:       " . number_format($balanceOldLogic, 0, ',', '.') . "đ\n\n";
    
    // Cách 2: Theo account tracking mới
    $accountName = "vehicle_{$vehicle->id}";
    
    $allTransactions = Transaction::orderBy('date')->orderBy('id')->get();
    
    $balanceFromAccounting = 0;
    foreach ($allTransactions as $tx) {
        if ($tx->from_account === $accountName) {
            $balanceFromAccounting -= $tx->amount;
        }
        if ($tx->to_account === $accountName) {
            $balanceFromAccounting += $tx->amount;
        }
    }
    
    echo "📊 CÁCH 2: Tính theo account tracking\n";
    echo "  Account: {$accountName}\n";
    echo "  SỐ DƯ:   " . number_format($balanceFromAccounting, 0, ',', '.') . "đ\n\n";
    
    // So sánh
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🔍 SO SÁNH VÀ PHÁT HIỆN VẤN ĐỀ\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    if (abs($balanceOldLogic - $balanceFromAccounting) < 0.01) {
        echo "✓ HAI CÁCH TÍNH CHO KẾT QUẢ GIỐNG NHAU\n";
        echo "  → Không có vấn đề về logic tính số dư\n\n";
    } else {
        echo "✗ HAI CÁCH TÍNH CHO KẾT QUẢ KHÁC NHAU!\n";
        echo "  Chênh lệch: " . number_format(abs($balanceOldLogic - $balanceFromAccounting), 0, ',', '.') . "đ\n\n";
        echo "  VẤN ĐỀ CÓ THỂ DO:\n";
        echo "  1. Giao dịch đã xóa chưa được revert trong account tracking\n";
        echo "  2. Logic determineAccounts() không đúng với một số loại giao dịch\n";
        echo "  3. Giao dịch cũ chưa có account tracking\n\n";
    }
    
    // Kiểm tra các giao dịch gần đây của xe này
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📋 10 GIAO DỊCH GẦN NHẤT (bao gồm đã xóa)\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    $recentTransactions = Transaction::withTrashed()
        ->where('vehicle_id', $vehicle->id)
        ->orderBy('date', 'desc')
        ->orderBy('id', 'desc')
        ->limit(10)
        ->get();
    
    foreach ($recentTransactions as $tx) {
        $deleted = $tx->trashed() ? '🗑️' : '  ';
        echo sprintf(
            "%s %s | %s | %s | %s | From: %-15s | Balance After: %s\n",
            $deleted,
            $tx->code,
            $tx->date->format('d/m/Y'),
            str_pad($tx->type, 10),
            str_pad(number_format($tx->amount, 0, ',', '.') . 'đ', 15, ' ', STR_PAD_LEFT),
            $tx->from_account ?? 'NULL',
            $tx->from_balance_after !== null ? number_format($tx->from_balance_after, 0, ',', '.') . 'đ' : 'NULL'
        );
    }
    
    echo "\n";
}

echo "=================================================================\n";
echo "KIỂM TRA HỆ THỐNG ACCOUNT TRACKING\n";
echo "=================================================================\n\n";

// Kiểm tra xem có giao dịch nào có account tracking hay chưa
$withAccountTracking = Transaction::whereNotNull('from_account')->count();
$withoutAccountTracking = Transaction::whereNull('from_account')->count();
$total = $withAccountTracking + $withoutAccountTracking;

echo "Tổng số giao dịch:               " . number_format($total) . "\n";
echo "Có account tracking:             " . number_format($withAccountTracking) . " (" . round($withAccountTracking/$total*100, 1) . "%)\n";
echo "Chưa có account tracking:        " . number_format($withoutAccountTracking) . " (" . round($withoutAccountTracking/$total*100, 1) . "%)\n\n";

if ($withoutAccountTracking > 0) {
    echo "⚠️  CÓ GIAO DỊCH CHƯA CÓ ACCOUNT TRACKING\n";
    echo "Điều này có thể gây sai lệch trong tính toán số dư.\n\n";
    
    // Kiểm tra giao dịch cũ nhất có account tracking
    $oldestWithTracking = Transaction::whereNotNull('from_account')->orderBy('date')->first();
    $oldestWithoutTracking = Transaction::whereNull('from_account')->orderBy('date')->first();
    
    if ($oldestWithTracking) {
        echo "Giao dịch cũ nhất CÓ tracking:   {$oldestWithTracking->code} ({$oldestWithTracking->date->format('d/m/Y')})\n";
    }
    if ($oldestWithoutTracking) {
        echo "Giao dịch cũ nhất CHƯA tracking: {$oldestWithoutTracking->code} ({$oldestWithoutTracking->date->format('d/m/Y')})\n";
    }
}

echo "\n";
