<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vehicle;

echo "=================================================================\n";
echo "TEST HIEN THI CANH BAO NO - DUNG LOGIC CONTROLLER\n";
echo "=================================================================\n\n";

$vehicle = Vehicle::with('owner')->find(4);

if (!$vehicle) {
    echo "Khong tim thay xe\n";
    exit;
}

echo "Xe: {$vehicle->license_plate}\n";
echo "Chu xe: " . ($vehicle->owner ? $vehicle->owner->full_name : 'Khong co') . "\n\n";

// Get filter parameters (giống trong controller)
$type = null;
$startDate = null;
$endDate = null;

// Build transactions query with filters (giống trong controller)
$statsQuery = $vehicle->transactions();

if ($type) {
    $statsQuery->where('type', $type);
}

if ($startDate) {
    $statsQuery->whereDate('date', '>=', $startDate);
}

if ($endDate) {
    $statsQuery->whereDate('date', '<=', $endDate);
}

$stats = [
    'total_incidents' => $vehicle->incidents()->count(),
    'this_month_incidents' => $vehicle->incidents()->thisMonth()->count(),
    'total_revenue' => (clone $statsQuery)->revenue()->sum('amount'),
    'total_expense' => (clone $statsQuery)->expense()->sum('amount'),
    'total_planned_expense' => (clone $statsQuery)->plannedExpense()->sum('amount'),
    'total_fund_deposit' => (clone $statsQuery)->fundDeposit()->sum('amount'),
    'month_revenue' => $vehicle->transactions()->revenue()->thisMonth()->sum('amount'),
    'month_expense' => $vehicle->transactions()->expense()->thisMonth()->sum('amount'),
    'month_planned_expense' => $vehicle->transactions()->plannedExpense()->thisMonth()->sum('amount'),
    'month_fund_deposit' => $vehicle->transactions()->fundDeposit()->thisMonth()->sum('amount'),
];

// For vehicles with owner
$stats['has_owner'] = $vehicle->hasOwner();

echo "STATS BAN DAU:\n";
echo "  has_owner: " . ($stats['has_owner'] ? 'true' : 'false') . "\n";
echo "  total_revenue: " . number_format($stats['total_revenue'], 0, ',', '.') . "d\n";
echo "  total_expense: " . number_format($stats['total_expense'], 0, ',', '.') . "d\n";
echo "  total_fund_deposit: " . number_format($stats['total_fund_deposit'], 0, ',', '.') . "d\n\n";

if ($stats['has_owner']) {
    // Get maintenance costs
    $totalOwnerMaintenance = (clone $statsQuery)->expense()->where('category', 'bảo_trì_xe_chủ_riêng')->sum('amount');
    $monthOwnerMaintenance = $vehicle->transactions()->expense()->thisMonth()->where('category', 'bảo_trì_xe_chủ_riêng')->sum('amount');
    
    // BƯỚC 1: Tổng thu cho xe có chủ = giao dịch thu + nộp quỹ
    $stats['total_revenue_display'] = $stats['total_revenue'] + $stats['total_fund_deposit'];
    $stats['month_revenue_display'] = $stats['month_revenue'] + $stats['month_fund_deposit'];
    
    // Calculate borrowed amount from company
    echo "TINH TOAN BORROWED:\n";
    $totalBorrowed = (clone $statsQuery)->borrowFromCompany()->sum('amount');
    echo "  totalBorrowed: " . number_format($totalBorrowed, 0, ',', '.') . "d\n";
    
    $totalReturned = (clone $statsQuery)->returnToCompany()->sum('amount');
    echo "  totalReturned: " . number_format($totalReturned, 0, ',', '.') . "d\n";
    
    $stats['total_borrowed'] = $totalBorrowed - $totalReturned;
    echo "  stats['total_borrowed']: " . number_format($stats['total_borrowed'], 0, ',', '.') . "d\n\n";
    
    $monthBorrowed = $vehicle->transactions()->borrowFromCompany()->thisMonth()->sum('amount');
    $monthReturned = $vehicle->transactions()->returnToCompany()->thisMonth()->sum('amount');
    $stats['month_borrowed'] = $monthBorrowed - $monthReturned;
    
    // BƯỚC 3: Lợi nhuận = Tổng thu - Tổng chi
    $stats['total_profit_after_fee'] = $stats['total_revenue_display'] - $stats['total_expense'];
    $stats['month_profit_after_fee'] = $stats['month_revenue_display'] - $stats['month_expense'];
    
    // Track owner maintenance separately
    $stats['total_owner_maintenance'] = $totalOwnerMaintenance;
    $stats['month_owner_maintenance'] = $monthOwnerMaintenance;
}

echo "KIEM TRA DIEU KIEN HIEN THI:\n";
echo "  stats['has_owner']: " . ($stats['has_owner'] ? 'true' : 'false') . "\n";
echo "  isset(stats['total_borrowed']): " . (isset($stats['total_borrowed']) ? 'true' : 'false') . "\n";
echo "  stats['total_borrowed']: " . (isset($stats['total_borrowed']) ? number_format($stats['total_borrowed'], 0, ',', '.') . "d" : 'NOT SET') . "\n";
echo "  stats['total_borrowed'] > 0: " . (isset($stats['total_borrowed']) && $stats['total_borrowed'] > 0 ? 'true' : 'false') . "\n\n";

echo "KET LUAN:\n";
if ($stats['has_owner'] && isset($stats['total_borrowed']) && $stats['total_borrowed'] > 0) {
    echo "  => SE HIEN THI CANH BAO NO!\n";
    echo "  => Dang vay: " . number_format($stats['total_borrowed'], 0, ',', '.') . "d\n";
    
    if ($stats['total_profit_after_fee'] > 0) {
        echo "  => CO NUT TRA NO (so du duong)\n";
    } else {
        echo "  => KHONG CO NUT TRA NO (so du = 0 hoac am)\n";
    }
} else {
    echo "  => KHONG HIEN THI CANH BAO NO\n";
    if (!$stats['has_owner']) {
        echo "     Ly do: Khong co chu xe\n";
    } else if (!isset($stats['total_borrowed'])) {
        echo "     Ly do: Bien total_borrowed khong duoc set\n";
    } else if ($stats['total_borrowed'] <= 0) {
        echo "     Ly do: total_borrowed <= 0\n";
    }
}

echo "\n=================================================================\n";
