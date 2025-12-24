<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vehicle;
use App\Models\Transaction;

$vehicle = Vehicle::find(4);
echo "Xe: {$vehicle->license_plate}\n";
echo "\n";

echo "Tong giao dich: " . Transaction::where('vehicle_id', 4)->count() . "\n";
echo "\n";

echo "Chi tiet giao dich:\n";
$transactions = Transaction::where('vehicle_id', 4)
    ->orderBy('date', 'desc')
    ->get();

foreach ($transactions->groupBy('type') as $type => $group) {
    $sum = $group->sum('amount');
    echo "  {$type}: {$group->count()} giao dich, tong: " . number_format($sum, 0, ',', '.') . "d\n";
}

echo "\n";
echo "Tinh toan:\n";
$thu = Transaction::where('vehicle_id', 4)->where('type', 'thu')->sum('amount');
$chi = Transaction::where('vehicle_id', 4)->where('type', 'chi')->sum('amount');
$vay = Transaction::where('vehicle_id', 4)->where('type', 'vay_cong_ty')->sum('amount');
$tra = Transaction::where('vehicle_id', 4)->where('type', 'tra_cong_ty')->sum('amount');

echo "  Thu: " . number_format($thu, 0, ',', '.') . "d\n";
echo "  Chi: " . number_format($chi, 0, ',', '.') . "d\n";
echo "  Vay cong ty: " . number_format($vay, 0, ',', '.') . "d\n";
echo "  Tra cong ty: " . number_format($tra, 0, ',', '.') . "d\n";
echo "\n";

$soDu = $thu + $vay - $chi - $tra;
$dangVay = $vay - $tra;

echo "So du: " . number_format($soDu, 0, ',', '.') . "d\n";
echo "Dang vay: " . number_format($dangVay, 0, ',', '.') . "d\n";
echo "\n";

// Giải thích logic "Chi từ công ty"
echo "=================================================================\n";
echo "LOGIC 'CHI TU CONG TY' HIEN TAI (SAI):\n";
echo "=================================================================\n";
$chiTuCongTy_Old = max(0, $chi - $thu);
echo "Chi tu cong ty (cu) = max(0, Chi - Thu)\n";
echo "                    = max(0, {$chi} - {$thu})\n";
echo "                    = " . number_format($chiTuCongTy_Old, 0, ',', '.') . "d\n";
echo "\n";
echo "LOGIC MOI (DUNG):\n";
echo "Chi tu cong ty = Dang vay cong ty\n";
echo "               = Vay - Tra\n";
echo "               = {$vay} - {$tra}\n";
echo "               = " . number_format($dangVay, 0, ',', '.') . "d\n";
echo "=================================================================\n";
