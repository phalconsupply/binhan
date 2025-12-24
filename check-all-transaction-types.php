<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

if (!$vehicle) {
    echo "Không tìm thấy xe 49B08879\n";
    exit;
}

echo "=== PHÂN TÍCH TẤT CẢ CÁC LOẠI GIAO DỊCH CHO XE 49B08879 ===\n\n";

// Get all transaction types with totals
$types = DB::table('transactions')
    ->where('vehicle_id', $vehicle->id)
    ->select('type', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
    ->groupBy('type')
    ->orderBy('type')
    ->get();

echo "1. TỔNG HỢP THEO LOẠI GIAO DỊCH:\n";
echo str_repeat("-", 80) . "\n";
printf("%-20s %10s %20s\n", "Loại giao dịch", "Số lượng", "Tổng tiền");
echo str_repeat("-", 80) . "\n";

$grandTotal = 0;
foreach ($types as $type) {
    printf("%-20s %10d %20s đ\n", 
        $type->type, 
        $type->count, 
        number_format($type->total)
    );
    $grandTotal += $type->total;
}
echo str_repeat("-", 80) . "\n";
printf("%-20s %10s %20s đ\n", "TỔNG", "", number_format($grandTotal));
echo str_repeat("-", 80) . "\n\n";

// Tính theo logic user muốn
echo "2. TÍNH TOÁN THEO LOGIC USER:\n";
echo str_repeat("-", 80) . "\n";

// Lấy TẤT CẢ các loại thu (có thể bao gồm thu, nop_quy, vay_cong_ty?)
$allRevenue = $vehicle->transactions()
    ->whereIn('type', ['thu', 'nop_quy'])
    ->sum('amount');

$allExpense = $vehicle->transactions()
    ->whereIn('type', ['chi', 'tra_cong_ty'])
    ->sum('amount');

echo "Tổng thu (thu + nop_quy):          " . number_format($allRevenue) . " đ\n";
echo "Tổng chi (chi + tra_cong_ty):      " . number_format($allExpense) . " đ\n";
echo "Còn lại:                            " . number_format($allRevenue - $allExpense) . " đ\n";
echo str_repeat("-", 80) . "\n\n";

// Thử thêm vay vào thu xem có khớp không
echo "3. THỬ NGHIỆM: BAO GỒM VAY CÔNG TY VÀO THU:\n";
echo str_repeat("-", 80) . "\n";

$allRevenueWithBorrow = $vehicle->transactions()
    ->whereIn('type', ['thu', 'nop_quy', 'vay_cong_ty'])
    ->sum('amount');

echo "Tổng thu (thu + nop_quy + vay):    " . number_format($allRevenueWithBorrow) . " đ\n";
echo "Tổng chi (chi + tra_cong_ty):      " . number_format($allExpense) . " đ\n";
echo "Còn lại:                            " . number_format($allRevenueWithBorrow - $allExpense) . " đ\n";
echo str_repeat("-", 80) . "\n\n";

// Thử trừ vay khỏi chi
echo "4. THỬ NGHIỆM: CHỈ TÍNH CHI (KHÔNG BAO GỒM TRẢ NỢ):\n";
echo str_repeat("-", 80) . "\n";

$onlyExpense = $vehicle->transactions()
    ->where('type', 'chi')
    ->sum('amount');

echo "Tổng thu (thu + nop_quy + vay):    " . number_format($allRevenueWithBorrow) . " đ\n";
echo "Tổng chi (CHỈ chi):                 " . number_format($onlyExpense) . " đ\n";
echo "Còn lại:                            " . number_format($allRevenueWithBorrow - $onlyExpense) . " đ\n";
echo str_repeat("-", 80) . "\n\n";

echo "USER TÍNH ĐƯỢC:\n";
echo "- Tổng thu: 152,911,425 đ\n";
echo "- Tổng chi: 123,553,100 đ\n";
echo "- Còn lại: 29,358,325 đ\n";
