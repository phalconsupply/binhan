<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

echo "=================================================================\n";
echo "KIỂM TRA TOTAL_EXPENSE_DISPLAY CỦA XE 49B08879\n";
echo "=================================================================\n\n";

echo "🚗 XE: {$vehicle->license_plate} (ID: {$vehicle->id})\n";
echo "Có chủ: " . ($vehicle->hasOwner() ? "CÓ" : "KHÔNG") . "\n\n";

// Tính từng thành phần
$totalExpense = $vehicle->transactions()->expense()->sum('amount');
$totalPlannedExpense = $vehicle->transactions()->plannedExpense()->sum('amount');
$totalReturned = $vehicle->transactions()->returnToCompany()->sum('amount');

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 CÁC THÀNH PHẦN TỔNG CHI\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "1. totalExpense (chi):              " . number_format($totalExpense, 0, ',', '.') . "đ\n";
echo "2. totalPlannedExpense (dự kiến):   " . number_format($totalPlannedExpense, 0, ',', '.') . "đ\n";
echo "3. totalReturned (trả công ty):     " . number_format($totalReturned, 0, ',', '.') . "đ\n";

// Tính phí quản lý 15%
$totalRevenue = $vehicle->transactions()->revenue()->sum('amount');
$totalBorrowed = $vehicle->transactions()->borrowFromCompany()->sum('amount');
$totalFundDeposit = $vehicle->transactions()->fundDeposit()->sum('amount');

// Phí 15% tính trên lợi nhuận THỰC (thu - chi - nộp quỹ)
$realProfit = $totalRevenue - $totalExpense - $totalFundDeposit;
$companyFee = max(0, $realProfit * 0.15);

echo "4. companyFee (phí 15%):            " . number_format($companyFee, 0, ',', '.') . "đ\n";
echo "\n   Tính từ lợi nhuận thực:\n";
echo "   Thu:         " . number_format($totalRevenue, 0, ',', '.') . "đ\n";
echo "   Chi:         " . number_format($totalExpense, 0, ',', '.') . "đ\n";
echo "   Nộp quỹ:     " . number_format($totalFundDeposit, 0, ',', '.') . "đ\n";
echo "   ────────────────────────────\n";
echo "   Lợi nhuận:   " . number_format($realProfit, 0, ',', '.') . "đ\n";
echo "   Phí 15%:     " . number_format($companyFee, 0, ',', '.') . "đ\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📈 TỔNG CHI HIỂN THỊ (total_expense_display)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$total_expense_display = $totalExpense + $totalPlannedExpense + $totalReturned + $companyFee;

echo "total_expense_display = totalExpense + totalPlannedExpense + totalReturned + companyFee\n";
echo "                      = " . number_format($totalExpense, 0, ',', '.') . " + ";
echo number_format($totalPlannedExpense, 0, ',', '.') . " + ";
echo number_format($totalReturned, 0, ',', '.') . " + ";
echo number_format($companyFee, 0, ',', '.') . "\n";
echo "                      = " . number_format($total_expense_display, 0, ',', '.') . "đ\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✓ SO SÁNH VỚI UI\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$uiValue = 105573425;
echo "Giá trị UI hiển thị:     " . number_format($uiValue, 0, ',', '.') . "đ\n";
echo "Giá trị tính được:       " . number_format($total_expense_display, 0, ',', '.') . "đ\n";

$diff = $uiValue - $total_expense_display;
echo "Chênh lệch:              " . number_format($diff, 0, ',', '.') . "đ\n\n";

if (abs($diff) < 0.01) {
    echo "✅ Khớp!\n";
} else {
    echo "❌ Không khớp! Cần kiểm tra thêm.\n\n";
    
    // Có thể do phí 15% tính theo cách khác
    echo "💡 Thử tính phí 15% theo cách khác:\n\n";
    
    // Cách 2: Phí 15% tính trên (thu - chi) không trừ nộp quỹ
    $profit2 = $totalRevenue - $totalExpense;
    $companyFee2 = max(0, $profit2 * 0.15);
    $total_expense_display2 = $totalExpense + $totalPlannedExpense + $totalReturned + $companyFee2;
    
    echo "   Cách 2: Phí 15% trên (thu - chi) KHÔNG trừ nộp quỹ\n";
    echo "   Lợi nhuận:   " . number_format($profit2, 0, ',', '.') . "đ\n";
    echo "   Phí 15%:     " . number_format($companyFee2, 0, ',', '.') . "đ\n";
    echo "   Tổng chi:    " . number_format($total_expense_display2, 0, ',', '.') . "đ\n";
    echo "   Chênh lệch:  " . number_format($uiValue - $total_expense_display2, 0, ',', '.') . "đ\n\n";
}

echo "\n";
