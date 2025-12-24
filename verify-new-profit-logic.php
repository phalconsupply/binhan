<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Vehicle;

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

if (!$vehicle) {
    echo "Không tìm thấy xe 49B08879\n";
    exit;
}

echo "=== XÁC NHẬN LOGIC MỚI CHO XE 49B08879 ===\n\n";

$totalRevenue = $vehicle->transactions()->revenue()->sum('amount');
$totalFundDeposit = $vehicle->transactions()->fundDeposit()->sum('amount');
$totalExpense = $vehicle->transactions()->expense()->sum('amount');
$totalReturned = $vehicle->transactions()->returnToCompany()->sum('amount');
$totalBorrowed = $vehicle->transactions()->borrowFromCompany()->sum('amount');

echo "1. GIAO DỊCH TÀI CHÍNH:\n";
echo str_repeat("-", 60) . "\n";
printf("   Thu từ chuyến đi (thu):           %15s đ\n", number_format($totalRevenue));
printf("   Nộp quỹ (nop_quy):                %15s đ\n", number_format($totalFundDeposit));
printf("   Chi phí hoạt động (chi):          %15s đ\n", number_format($totalExpense));
printf("   Vay công ty (vay_cong_ty):        %15s đ\n", number_format($totalBorrowed));
printf("   Trả nợ công ty (tra_cong_ty):     %15s đ\n", number_format($totalReturned));
echo str_repeat("-", 60) . "\n\n";

echo "2. TÍNH TOÁN LỢI NHUẬN:\n";
echo str_repeat("-", 60) . "\n";
$totalRevenueDisplay = $totalRevenue + $totalFundDeposit + $totalBorrowed; // BAO GỒM VAY
$companyFee = $totalRevenue * 0.15; // Phí 15% cho công ty
$totalExpenseDisplay = $totalExpense + $totalReturned + $companyFee; // BAO GỒM PHÍ 15%
$profit = $totalRevenueDisplay - $totalExpenseDisplay; // Tiền vào - Tiền ra
$debtRemaining = $totalBorrowed - $totalReturned;

printf("   Tổng thu (tiền vào):              %15s đ\n", number_format($totalRevenueDisplay));
printf("   (= thu + nộp quỹ + vay = %s + %s + %s)\n\n", 
    number_format($totalRevenue), 
    number_format($totalFundDeposit), 
    number_format($totalBorrowed)
);

printf("   Tổng chi (tiền ra):               %15s đ\n", number_format($totalExpenseDisplay));
printf("   (= chi + trả nợ + phí 15%%)\n");
printf("   (= %s + %s + %s)\n\n", 
    number_format($totalExpense), 
    number_format($totalReturned),
    number_format($companyFee)
);

printf("   Lợi nhuận (tiền vào - tiền ra):  %15s đ\n", number_format($profit));

printf("\n   Nợ còn lại:                       %15s đ\n", number_format($debtRemaining));
echo str_repeat("-", 60) . "\n\n";

echo "3. GIẢI THÍCH:\n";
echo str_repeat("-", 60) . "\n";
echo "   LOGIC: Tất cả tiền vào - Tất cả tiền ra\n";
echo "   - Tiền vào: Thu từ chuyến + Nộp quỹ + Vay công ty\n";
echo "   - Tiền ra: Chi phí + Trả nợ + Phí 15% cho công ty\n";
echo "   - Lợi nhuận = Tiền vào - Tiền ra\n\n";
echo "   Trạng thái nợ:\n";
echo "   - Đã vay: " . number_format($totalBorrowed) . "đ\n";
echo "   - Đã trả: " . number_format($totalReturned) . "đ\n";
echo "   - Nợ còn lại: " . number_format($debtRemaining) . "đ\n\n";
echo "   Phí công ty:\n";
echo "   - Thu từ chuyến: " . number_format($totalRevenue) . "đ\n";
echo "   - Phí 15%: " . number_format($companyFee) . "đ\n";
echo str_repeat("-", 60) . "\n";
