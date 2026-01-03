<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;

echo "=================================================================\n";
echo "KIỂM TRA LOGIC TÀI KHOẢN - GIAO DỊCH CHI CỦA XE CÓ CHỦ\n";
echo "=================================================================\n\n";

// Kiểm tra 2 giao dịch
$codes = ['GD20251226-0815', 'GD20251126-0811'];

foreach ($codes as $code) {
    $tx = Transaction::where('code', $code)->first();
    
    if (!$tx) {
        echo "⚠️  Không tìm thấy giao dịch: {$code}\n\n";
        continue;
    }
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📋 GIAO DỊCH: {$tx->code}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "  Type:         {$tx->type}\n";
    echo "  Category:     " . ($tx->category ?? 'NULL') . "\n";
    echo "  Amount:       " . number_format($tx->amount, 0, ',', '.') . "đ\n";
    echo "  Date:         {$tx->date}\n";
    
    if ($tx->vehicle_id) {
        $vehicle = $tx->vehicle;
        echo "  Vehicle:      {$vehicle->license_plate} (ID: {$vehicle->id})\n";
        echo "  Có chủ xe:    " . ($vehicle->hasOwner() ? 'CÓ ✓' : 'KHÔNG') . "\n";
    } else {
        echo "  Vehicle:      NULL (Giao dịch công ty)\n";
    }
    
    echo "\n  📊 ACCOUNT TRACKING:\n";
    echo "  From Account: " . ($tx->from_account ?? 'NULL') . "\n";
    echo "  To Account:   " . ($tx->to_account ?? 'NULL') . "\n";
    
    if ($tx->from_account) {
        echo "  Display Flow: {$tx->account_flow_display}\n";
    }
    
    echo "\n";
}

// Kiểm tra xe 49B08879
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🚗 XE 49B08879 - TỔNG QUAN\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

if (!$vehicle) {
    echo "❌ Không tìm thấy xe 49B08879\n";
    exit;
}

echo "Xe:        {$vehicle->license_plate} (ID: {$vehicle->id})\n";
echo "Có chủ xe: " . ($vehicle->hasOwner() ? 'CÓ ✓' : 'KHÔNG') . "\n";

if ($vehicle->hasOwner()) {
    echo "Chủ xe:    {$vehicle->owner->full_name}\n";
}

echo "\n📊 THỐNG KÊ TÀI CHÍNH:\n";

$totalRevenue = $vehicle->transactions()->revenue()->sum('amount');
$totalExpense = $vehicle->transactions()->expense()->sum('amount');
$totalFundDeposit = $vehicle->transactions()->fundDeposit()->sum('amount');
$totalBorrowed = $vehicle->transactions()->borrowFromCompany()->sum('amount');
$totalReturned = $vehicle->transactions()->returnToCompany()->sum('amount');

echo "  Tổng thu:        " . number_format($totalRevenue, 0, ',', '.') . "đ\n";
echo "  Nộp quỹ:         " . number_format($totalFundDeposit, 0, ',', '.') . "đ\n";
echo "  Vay công ty:     " . number_format($totalBorrowed, 0, ',', '.') . "đ\n";
echo "  Tổng chi:        " . number_format($totalExpense, 0, ',', '.') . "đ\n";
echo "  Trả công ty:     " . number_format($totalReturned, 0, ',', '.') . "đ\n";
echo "  ────────────────────────────────\n";

$profit = $totalRevenue + $totalFundDeposit + $totalBorrowed - $totalExpense - $totalReturned;
echo "  LỢI NHUẬN:       " . number_format($profit, 0, ',', '.') . "đ\n\n";

// Kiểm tra thống kê công ty
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🏢 THỐNG KÊ CÔNG TY (/transactions)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Lấy logic từ TransactionController
$companyRevenue = Transaction::whereNull('vehicle_id')
    ->where('type', 'thu')
    ->where(function($q) {
        $q->where('category', '!=', 'vay_từ_công_ty')
          ->orWhereNull('category');
    })
    ->sum('amount');

$companyExpense = Transaction::whereNull('vehicle_id')
    ->where('type', 'chi')
    ->sum('amount');

$companyPlannedExpense = Transaction::whereNull('vehicle_id')
    ->where('type', 'du_kien_chi')
    ->sum('amount');

echo "Tổng thu công ty:        " . number_format($companyRevenue, 0, ',', '.') . "đ\n";
echo "Tổng chi công ty:        " . number_format($companyExpense, 0, ',', '.') . "đ\n";
echo "Dự kiến chi:             " . number_format($companyPlannedExpense, 0, ',', '.') . "đ\n";
echo "────────────────────────────────\n";

$companyProfit = $companyRevenue - $companyExpense - $companyPlannedExpense;
echo "LỢI NHUẬN CÔNG TY:       " . number_format($companyProfit, 0, ',', '.') . "đ\n\n";

// Kiểm tra có giao dịch nào của xe được tính vào công ty không
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔍 PHÂN TÍCH VẤN ĐỀ\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Kiểm tra giao dịch chi của xe 49B08879
$vehicleExpenses = Transaction::where('vehicle_id', $vehicle->id)
    ->where('type', 'chi')
    ->orderBy('date', 'desc')
    ->limit(10)
    ->get();

echo "📋 10 GIAO DỊCH CHI GẦN NHẤT CỦA XE 49B08879:\n\n";

foreach ($vehicleExpenses as $expense) {
    echo sprintf(
        "  %s | %s | %s | From: %s | To: %s\n",
        $expense->code,
        $expense->date->format('d/m/Y'),
        str_pad(number_format($expense->amount, 0, ',', '.') . 'đ', 15, ' ', STR_PAD_LEFT),
        $expense->from_account ?? 'NULL',
        $expense->to_account ?? 'NULL'
    );
}

echo "\n";

// Kiểm tra xem có giao dịch nào vehicle_id = NULL nhưng liên quan đến xe không
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "⚠️  KIỂM TRA GIAO DỊCH CÔNG TY LIÊN QUAN ĐẾN XE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Tìm giao dịch công ty có note chứa "49B08879"
$relatedCompanyTx = Transaction::whereNull('vehicle_id')
    ->where(function($q) {
        $q->where('note', 'like', '%49B08879%')
          ->orWhere('note', 'like', '%49B-08879%')
          ->orWhere('note', 'like', '%49B 08879%');
    })
    ->orderBy('date', 'desc')
    ->limit(10)
    ->get();

if ($relatedCompanyTx->count() > 0) {
    echo "Tìm thấy " . $relatedCompanyTx->count() . " giao dịch công ty liên quan:\n\n";
    
    foreach ($relatedCompanyTx as $tx) {
        echo sprintf(
            "  %s | %s | Type: %s | %s | %s\n",
            $tx->code,
            $tx->date->format('d/m/Y'),
            str_pad($tx->type, 12),
            str_pad(number_format($tx->amount, 0, ',', '.') . 'đ', 15, ' ', STR_PAD_LEFT),
            substr($tx->note ?? '', 0, 50)
        );
    }
} else {
    echo "✓ Không tìm thấy giao dịch công ty nào liên quan đến xe 49B08879\n";
}

echo "\n";
echo "=================================================================\n";
echo "KẾT LUẬN\n";
echo "=================================================================\n\n";

echo "1. Xe 49B08879 CÓ chủ xe → Giao dịch chi từ xe KHÔNG nên ảnh hưởng\n";
echo "   đến lợi nhuận công ty\n\n";

echo "2. Kiểm tra logic trong AccountBalanceService::determineAccounts():\n";
echo "   - Giao dịch chi từ xe có chủ → from_account = 'vehicle_4'\n";
echo "   - KHÔNG tạo giao dịch company_fund\n\n";

echo "3. Thống kê công ty chỉ tính giao dịch có vehicle_id = NULL\n";
echo "   → Nếu vẫn bị ảnh hưởng → Có bug trong logic\n\n";
