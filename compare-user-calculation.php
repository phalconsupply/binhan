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

echo "=== KIỂM TRA CHI TIẾT CÁC KHOẢN THU ===\n\n";

// Thu
$thuTransactions = $vehicle->transactions()->where('type', 'thu')->orderBy('date')->get();
echo "1. GIAO DỊCH THU:\n";
echo str_repeat("-", 100) . "\n";
printf("%-15s %-12s %-50s %15s\n", "Mã GD", "Ngày", "Ghi chú", "Số tiền");
echo str_repeat("-", 100) . "\n";
$totalThu = 0;
foreach ($thuTransactions as $tx) {
    printf("%-15s %-12s %-50s %15s\n", 
        $tx->code,
        $tx->date,
        substr($tx->note ?? '', 0, 50),
        number_format($tx->amount)
    );
    $totalThu += $tx->amount;
}
echo str_repeat("-", 100) . "\n";
echo "TỔNG THU: " . number_format($totalThu) . " đ\n\n";

// Nộp quỹ
$nopQuy = $vehicle->transactions()->where('type', 'nop_quy')->get();
echo "2. GIAO DỊCH NỘP QUỸ:\n";
echo str_repeat("-", 100) . "\n";
$totalNopQuy = 0;
foreach ($nopQuy as $tx) {
    printf("%-15s %-12s %-50s %15s\n", 
        $tx->code,
        $tx->date,
        substr($tx->note ?? '', 0, 50),
        number_format($tx->amount)
    );
    $totalNopQuy += $tx->amount;
}
echo str_repeat("-", 100) . "\n";
echo "TỔNG NỘP QUỸ: " . number_format($totalNopQuy) . " đ\n\n";

// Vay công ty
$vayTransactions = $vehicle->transactions()->where('type', 'vay_cong_ty')->orderBy('date')->get();
echo "3. GIAO DỊCH VAY CÔNG TY:\n";
echo str_repeat("-", 100) . "\n";
$totalVay = 0;
foreach ($vayTransactions as $tx) {
    printf("%-15s %-12s %-50s %15s\n", 
        $tx->code,
        $tx->date,
        substr($tx->note ?? '', 0, 50),
        number_format($tx->amount)
    );
    $totalVay += $tx->amount;
}
echo str_repeat("-", 100) . "\n";
echo "TỔNG VAY: " . number_format($totalVay) . " đ\n\n";

echo "=== KIỂM TRA CHI TIẾT CÁC KHOẢN CHI ===\n\n";

// Chi
$chiTransactions = $vehicle->transactions()->where('type', 'chi')->orderBy('date')->get();
echo "4. GIAO DỊCH CHI:\n";
echo str_repeat("-", 100) . "\n";
$totalChi = 0;
foreach ($chiTransactions as $tx) {
    $totalChi += $tx->amount;
}
echo "Có " . $chiTransactions->count() . " giao dịch chi\n";
echo "TỔNG CHI: " . number_format($totalChi) . " đ\n\n";

// Trả nợ
$traNoTransactions = $vehicle->transactions()->where('type', 'tra_cong_ty')->orderBy('date')->get();
echo "5. GIAO DỊCH TRẢ NỢ:\n";
echo str_repeat("-", 100) . "\n";
$totalTraNo = 0;
foreach ($traNoTransactions as $tx) {
    printf("%-15s %-12s %-50s %15s\n", 
        $tx->code,
        $tx->date,
        substr($tx->note ?? '', 0, 50),
        number_format($tx->amount)
    );
    $totalTraNo += $tx->amount;
}
echo str_repeat("-", 100) . "\n";
echo "TỔNG TRẢ NỢ: " . number_format($totalTraNo) . " đ\n\n";

echo "=== SO SÁNH ===\n\n";
echo "A. Tổng thu theo hệ thống:\n";
echo "   - Thu:                  " . number_format($totalThu) . " đ\n";
echo "   - Nộp quỹ:              " . number_format($totalNopQuy) . " đ\n";
echo "   - Vay công ty:          " . number_format($totalVay) . " đ\n";
echo "   - Cộng:                 " . number_format($totalThu + $totalNopQuy + $totalVay) . " đ\n\n";

echo "B. Tổng chi theo hệ thống:\n";
echo "   - Chi:                  " . number_format($totalChi) . " đ\n";
echo "   - Trả nợ:               " . number_format($totalTraNo) . " đ\n";
echo "   - Cộng:                 " . number_format($totalChi + $totalTraNo) . " đ\n\n";

echo "C. Còn lại:\n";
echo "   - (A - B):              " . number_format(($totalThu + $totalNopQuy + $totalVay) - ($totalChi + $totalTraNo)) . " đ\n\n";

echo "D. User tính được:\n";
echo "   - Tổng thu:             152,911,425 đ\n";
echo "   - Tổng chi:             123,553,100 đ\n";
echo "   - Còn lại:              29,358,325 đ\n\n";

echo "E. Chênh lệch:\n";
echo "   - Thu (hệ thống vs user):    " . number_format(($totalThu + $totalNopQuy + $totalVay) - 152911425) . " đ\n";
echo "   - Chi (hệ thống vs user):    " . number_format(($totalChi + $totalTraNo) - 123553100) . " đ\n";
