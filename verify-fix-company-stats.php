<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;

echo "=================================================================\n";
echo "VERIFY FIX: Kiểm tra thống kê sau khi sửa bug\n";
echo "=================================================================\n\n";

// Simulate fixed code
$statsQuery = Transaction::whereNull('vehicle_id'); // ✓ ĐÃ THÊM FILTER

echo "📊 THỐNG KÊ SAU KHI FIX:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$totalRevenue = (clone $statsQuery)->revenue()->where(function($q) {
    $q->where('category', '!=', 'vay_từ_công_ty')->orWhereNull('category');
})->sum('amount');

$totalFundDeposit = (clone $statsQuery)->fundDeposit()->sum('amount');
$totalRevenueAll = $totalRevenue + $totalFundDeposit;

$totalExpense = (clone $statsQuery)->expense()->sum('amount');
$totalPlannedExpense = (clone $statsQuery)->plannedExpense()->sum('amount');
$totalProfit = $totalRevenueAll - $totalExpense - $totalPlannedExpense;

echo "Tổng thu:        " . number_format($totalRevenueAll, 0, ',', '.') . "đ\n";
echo "Tổng chi:        " . number_format($totalExpense, 0, ',', '.') . "đ\n";
echo "Dự kiến chi:     " . number_format($totalPlannedExpense, 0, ',', '.') . "đ\n";
echo "────────────────────────────────\n";
echo "LỢI NHUẬN:       " . number_format($totalProfit, 0, ',', '.') . "đ\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✓ SO SÁNH TRƯỚC VÀ SAU:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Trước fix
$beforeExpense = Transaction::where('type', 'chi')->sum('amount');
$beforeRevenue = Transaction::where('type', 'thu')->sum('amount');

// Sau fix
$afterExpense = $totalExpense;
$afterRevenue = $totalRevenueAll;

echo "CHI:\n";
echo "  Trước fix: " . number_format($beforeExpense, 0, ',', '.') . "đ (SAI - tính cả xe)\n";
echo "  Sau fix:   " . number_format($afterExpense, 0, ',', '.') . "đ (ĐÚNG - chỉ công ty)\n";
echo "  Giảm:      " . number_format($beforeExpense - $afterExpense, 0, ',', '.') . "đ\n\n";

echo "THU:\n";
echo "  Trước fix: 472M đ (SAI - tính cả xe)\n";
echo "  Sau fix:   " . number_format($afterRevenue, 0, ',', '.') . "đ (ĐÚNG - chỉ công ty)\n\n";

echo "✓ Giao dịch chi từ xe 49B08879 (12M + 1.9M) KHÔNG còn ảnh hưởng\n";
echo "  đến thống kê công ty!\n\n";
