<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;

echo "=================================================================\n";
echo "KIỂM TRA 3 GIAO DỊCH VÀ SỐ DƯ XE 49B08879\n";
echo "=================================================================\n\n";

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

if (!$vehicle) {
    echo "Không tìm thấy xe 49B08879\n";
    exit;
}

echo "🚗 XE: {$vehicle->license_plate} (ID: {$vehicle->id})\n\n";

$codes = ['GD20251126-0904', 'GD20251226-0910', 'GD20260101-0911'];

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 CHI TIẾT 3 GIAO DỊCH\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$foundTransactions = [];
foreach ($codes as $code) {
    $tx = Transaction::withTrashed()->where('code', $code)->first();
    
    if ($tx) {
        $foundTransactions[] = $tx;
        $status = $tx->trashed() ? '🗑️ ĐÃ XÓA' : '✅ ĐANG TỒN TẠI';
        
        echo "📌 {$tx->code} - {$status}\n";
        echo "   ID:           {$tx->id}\n";
        echo "   Ngày:         {$tx->date->format('d/m/Y H:i')}\n";
        echo "   Type:         {$tx->type}\n";
        echo "   Amount:       " . number_format($tx->amount, 0, ',', '.') . "đ\n";
        echo "   Vehicle:      " . ($tx->vehicle_id ? "ID {$tx->vehicle_id}" : "NULL") . "\n";
        
        if ($tx->trashed()) {
            echo "   Deleted at:   {$tx->deleted_at->format('d/m/Y H:i')}\n";
        }
        
        echo "\n   📊 Account Tracking:\n";
        echo "   From:         {$tx->from_account}\n";
        echo "   To:           {$tx->to_account}\n";
        echo "   From Before:  " . ($tx->from_balance_before !== null ? number_format($tx->from_balance_before, 0, ',', '.') . 'đ' : 'NULL') . "\n";
        echo "   From After:   " . ($tx->from_balance_after !== null ? number_format($tx->from_balance_after, 0, ',', '.') . 'đ' : 'NULL') . "\n";
        echo "   To Before:    " . ($tx->to_balance_before !== null ? number_format($tx->to_balance_before, 0, ',', '.') . 'đ' : 'NULL') . "\n";
        echo "   To After:     " . ($tx->to_balance_after !== null ? number_format($tx->to_balance_after, 0, ',', '.') . 'đ' : 'NULL') . "\n";
        echo "\n";
    } else {
        echo "❌ {$code} - KHÔNG TÌM THẤY\n\n";
    }
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔍 TÍNH SỐ DƯ HIỆN TẠI (CHỈ TÍNH GIAO DỊCH CHƯA XÓA)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Tính số dư theo scope (không bao gồm deleted)
$totalRevenue = $vehicle->transactions()->revenue()->sum('amount');
$totalExpense = $vehicle->transactions()->expense()->sum('amount');
$totalFundDeposit = $vehicle->transactions()->fundDeposit()->sum('amount');
$totalBorrowed = $vehicle->transactions()->borrowFromCompany()->sum('amount');
$totalReturned = $vehicle->transactions()->returnToCompany()->sum('amount');

$balanceCurrent = $totalRevenue + $totalFundDeposit + $totalBorrowed - $totalExpense - $totalReturned;

echo "Thu:              " . number_format($totalRevenue, 0, ',', '.') . "đ\n";
echo "Chi:              " . number_format($totalExpense, 0, ',', '.') . "đ\n";
echo "Nộp quỹ:          " . number_format($totalFundDeposit, 0, ',', '.') . "đ\n";
echo "Vay công ty:      " . number_format($totalBorrowed, 0, ',', '.') . "đ\n";
echo "Trả công ty:      " . number_format($totalReturned, 0, ',', '.') . "đ\n";
echo "────────────────────────────────────────\n";
echo "SỐ DƯ HIỆN TẠI:   " . number_format($balanceCurrent, 0, ',', '.') . "đ\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔄 GIẢ LẬP NẾU KHÔNG TẠO 3 GIAO DỊCH NÀY\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Tính số dư bao gồm cả deleted
$totalRevenueWithDeleted = $vehicle->transactions()->withTrashed()->revenue()->sum('amount');
$totalExpenseWithDeleted = $vehicle->transactions()->withTrashed()->expense()->sum('amount');
$totalFundDepositWithDeleted = $vehicle->transactions()->withTrashed()->fundDeposit()->sum('amount');
$totalBorrowedWithDeleted = $vehicle->transactions()->withTrashed()->borrowFromCompany()->sum('amount');
$totalReturnedWithDeleted = $vehicle->transactions()->withTrashed()->returnToCompany()->sum('amount');

$balanceWithDeleted = $totalRevenueWithDeleted + $totalFundDepositWithDeleted + $totalBorrowedWithDeleted 
                      - $totalExpenseWithDeleted - $totalReturnedWithDeleted;

echo "Thu (bao gồm deleted):              " . number_format($totalRevenueWithDeleted, 0, ',', '.') . "đ\n";
echo "Chi (bao gồm deleted):              " . number_format($totalExpenseWithDeleted, 0, ',', '.') . "đ\n";
echo "Nộp quỹ (bao gồm deleted):          " . number_format($totalFundDepositWithDeleted, 0, ',', '.') . "đ\n";
echo "Vay công ty (bao gồm deleted):      " . number_format($totalBorrowedWithDeleted, 0, ',', '.') . "đ\n";
echo "Trả công ty (bao gồm deleted):      " . number_format($totalReturnedWithDeleted, 0, ',', '.') . "đ\n";
echo "────────────────────────────────────────\n";
echo "SỐ DƯ (nếu tính cả deleted):        " . number_format($balanceWithDeleted, 0, ',', '.') . "đ\n\n";

// Tính tổng amount của 3 giao dịch đã xóa
$deletedAmount = 0;
$deletedRevenueAmount = 0;
$deletedExpenseAmount = 0;

foreach ($foundTransactions as $tx) {
    if ($tx->trashed()) {
        if ($tx->type === 'chi') {
            $deletedExpenseAmount += $tx->amount;
        } elseif ($tx->type === 'thu') {
            $deletedRevenueAmount += $tx->amount;
        }
    }
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 PHÂN TÍCH CHÊNH LỆCH\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "Số dư người dùng báo TRƯỚC khi tạo 3 GD:  28.674.575đ\n";
echo "Số dư người dùng báo SAU khi xóa 3 GD:    27.926.575đ\n";
echo "Chênh lệch do người dùng báo:             " . number_format(28674575 - 27926575, 0, ',', '.') . "đ\n\n";

echo "Số dư hiện tại (tính bằng scope):         " . number_format($balanceCurrent, 0, ',', '.') . "đ\n";
echo "Số dư nếu tính cả deleted:                " . number_format($balanceWithDeleted, 0, ',', '.') . "đ\n";
echo "Chênh lệch (hiện tại vs với deleted):     " . number_format(abs($balanceCurrent - $balanceWithDeleted), 0, ',', '.') . "đ\n\n";

echo "Tổng chi trong 3 GD bị xóa:               " . number_format($deletedExpenseAmount, 0, ',', '.') . "đ\n";
echo "Tổng thu trong 3 GD bị xóa:               " . number_format($deletedRevenueAmount, 0, ',', '.') . "đ\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔍 TÌM GIAO DỊCH GẦN THỜI ĐIỂM TẠO 3 GD TRÊN\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Tìm giao dịch ngay trước GD20251126-0904
$beforeFirst = Transaction::where('vehicle_id', $vehicle->id)
    ->where('date', '<=', '2025-11-26')
    ->where('id', '<', $foundTransactions[0]->id ?? 999999)
    ->orderBy('date', 'desc')
    ->orderBy('id', 'desc')
    ->first();

if ($beforeFirst) {
    echo "📌 Giao dịch TRƯỚC GD20251126-0904:\n";
    echo "   Code:         {$beforeFirst->code}\n";
    echo "   Date:         {$beforeFirst->date->format('d/m/Y')}\n";
    echo "   Type:         {$beforeFirst->type}\n";
    echo "   Amount:       " . number_format($beforeFirst->amount, 0, ',', '.') . "đ\n";
    
    if ($beforeFirst->type === 'chi' || $beforeFirst->type === 'tra_cong_ty') {
        echo "   Số dư xe sau: " . ($beforeFirst->from_balance_after !== null ? number_format($beforeFirst->from_balance_after, 0, ',', '.') . 'đ' : 'NULL') . "\n";
    } else {
        echo "   Số dư xe sau: " . ($beforeFirst->to_balance_after !== null ? number_format($beforeFirst->to_balance_after, 0, ',', '.') . 'đ' : 'NULL') . "\n";
    }
    echo "\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "💡 KẾT LUẬN\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$expectedBalance = 28674575;
$actualBalance = $balanceCurrent;
$difference = $expectedBalance - $actualBalance;

echo "Số dư mong đợi (theo người dùng):  " . number_format($expectedBalance, 0, ',', '.') . "đ\n";
echo "Số dư thực tế (tính bằng code):    " . number_format($actualBalance, 0, ',', '.') . "đ\n";
echo "Chênh lệch:                        " . number_format($difference, 0, ',', '.') . "đ\n\n";

if (abs($difference) < 0.01) {
    echo "✅ Số dư khớp!\n";
} else {
    echo "❌ Số dư KHÔNG khớp!\n";
    echo "\nNguyên nhân có thể:\n";
    echo "1. Có giao dịch khác bị xóa/restore giữa các lần kiểm tra\n";
    echo "2. Balance không được cập nhật khi xóa/restore giao dịch\n";
    echo "3. Dữ liệu ban đầu (28.674.575đ) không chính xác\n";
    echo "4. Có giao dịch khác được tạo/sửa trong khoảng thời gian này\n";
}

echo "\n";
