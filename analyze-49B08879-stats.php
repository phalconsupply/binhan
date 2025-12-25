<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Vehicle;
use App\Http\Controllers\VehicleController;

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

if (!$vehicle) {
    echo "Vehicle not found\n";
    exit;
}

echo "=== PHÂN TÍCH CHI TIẾT XE {$vehicle->license_plate} ===\n\n";

// Thu
$totalRevenue = $vehicle->transactions()->revenue()->sum('amount');
$totalBorrowed = $vehicle->transactions()->borrowFromCompany()->sum('amount');
$totalFundDeposit = $vehicle->transactions()->fundDeposit()->sum('amount');

echo "THU:\n";
echo "  - Thu chuyến đi: " . number_format($totalRevenue) . "\n";
echo "  - Vay công ty: " . number_format($totalBorrowed) . "\n";
echo "  - Nộp quỹ: " . number_format($totalFundDeposit) . "\n";
echo "  => TỔNG THU: " . number_format($totalRevenue + $totalBorrowed + $totalFundDeposit) . "\n\n";

// Chi - Tách riêng bảo trì và chuyến đi
$chiChuyenDi = $vehicle->transactions()
    ->where('type', 'chi')
    ->where(function($q) {
        $q->whereNull('vehicle_maintenance_id')
          ->orWhere(function($q2) {
              $q2->whereNull('category')
                 ->orWhere('category', '!=', 'bảo_trì_xe_chủ_riêng');
          });
    })
    ->sum('amount');

$chiBaoTri = $vehicle->transactions()
    ->where('type', 'chi')
    ->where(function($q) {
        $q->whereNotNull('vehicle_maintenance_id')
          ->orWhere('category', 'bảo_trì_xe_chủ_riêng');
    })
    ->sum('amount');

$totalExpense = $vehicle->transactions()->expense()->sum('amount');
$totalPlannedExpense = $vehicle->transactions()->plannedExpense()->sum('amount');
$totalReturned = $vehicle->transactions()->returnToCompany()->sum('amount');

echo "CHI:\n";
echo "  - Chi chuyến đi: " . number_format($chiChuyenDi) . "\n";
echo "  - Chi bảo trì: " . number_format($chiBaoTri) . "\n";
echo "  - Dự kiến chi: " . number_format($totalPlannedExpense) . "\n";
echo "  - Trả nợ công ty: " . number_format($totalReturned) . "\n";
echo "  => TỔNG CHI (chưa phí): " . number_format($chiChuyenDi + $chiBaoTri + $totalPlannedExpense + $totalReturned) . "\n\n";

// Tính phí 15% CHỈ trên lợi nhuận chuyến đi
$loiNhuanChuyenDi = $totalRevenue - $chiChuyenDi;
$phi15 = ($loiNhuanChuyenDi > 0) ? $loiNhuanChuyenDi * 0.15 : 0;

echo "PHÍ 15%:\n";
echo "  - Lợi nhuận chuyến đi: " . number_format($loiNhuanChuyenDi) . " (Thu: " . number_format($totalRevenue) . " - Chi CD: " . number_format($chiChuyenDi) . ")\n";
echo "  - Phí 15%: " . number_format($phi15) . "\n\n";

$tongChi = $chiChuyenDi + $chiBaoTri + $totalPlannedExpense + $totalReturned + $phi15;
$loiNhuan = ($totalRevenue + $totalBorrowed + $totalFundDeposit) - $tongChi;

echo "TỔNG KẾT:\n";
echo "  - Tổng thu: " . number_format($totalRevenue + $totalBorrowed + $totalFundDeposit) . "\n";
echo "  - Tổng chi: " . number_format($tongChi) . " (CD: " . number_format($chiChuyenDi) . " + BT: " . number_format($chiBaoTri) . " + Phí: " . number_format($phi15) . ")\n";
echo "  => LỢI NHUẬN: " . number_format($loiNhuan) . "\n\n";

echo "SO SÁNH VỚI TÍNH TOÁN USER:\n";
echo "  User - Chi chuyến đi: 53.860.000\n";
echo "  User - Chi bảo trì: 36.630.425\n";
echo "  User - Phí 15%: 6.741.000\n";
echo "  User - Tổng chi: 97.231.425\n";
echo "  User - Lợi nhuận: 21.568.575\n";

// Test calculateVehicleStats
echo "\n=== KẾT QUẢ TỪ calculateVehicleStats() ===\n";
$stats = VehicleController::calculateVehicleStats($vehicle);
echo "  - Total revenue display: " . number_format($stats['total_revenue_display']) . "\n";
echo "  - Total expense display: " . number_format($stats['total_expense_display']) . "\n";
echo "  - Total company fee: " . number_format($stats['total_company_fee']) . "\n";
echo "  - Total profit after fee: " . number_format($stats['total_profit_after_fee']) . "\n";
