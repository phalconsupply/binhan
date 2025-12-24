<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vehicle;

echo "CAC XE CO CHU XE:\n";
echo "=================================================================\n";

$vehicles = Vehicle::whereHas('owner')->get();

foreach ($vehicles as $v) {
    $thu = $v->transactions()->where('type', 'thu')->sum('amount');
    $chi = $v->transactions()->where('type', 'chi')->sum('amount');
    $vay = $v->transactions()->where('type', 'vay_cong_ty')->sum('amount');
    $tra = $v->transactions()->where('type', 'tra_cong_ty')->sum('amount');
    $soDu = $thu + $vay - $chi - $tra;
    $dangVay = $vay - $tra;
    
    echo "{$v->id}: {$v->license_plate}\n";
    echo "  Thu: " . number_format($thu, 0, ',', '.') . "d\n";
    echo "  Chi: " . number_format($chi, 0, ',', '.') . "d\n";
    echo "  Vay: " . number_format($vay, 0, ',', '.') . "d\n";
    echo "  Tra: " . number_format($tra, 0, ',', '.') . "d\n";
    echo "  So du: " . number_format($soDu, 0, ',', '.') . "d\n";
    echo "  Dang vay: " . number_format($dangVay, 0, ',', '.') . "d\n";
    
    if ($dangVay > 0) {
        if ($soDu > 0) {
            echo "  => HIEN THI: Canh bao NO + Nut TRA NO\n";
        } else {
            echo "  => HIEN THI: Canh bao NO (khong co nut vi so du <= 0)\n";
        }
    } else {
        echo "  => KHONG hien thi canh bao no\n";
    }
    echo "\n";
}
