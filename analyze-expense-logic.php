<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;

echo "🔍 Kiểm tra tổng chi của công ty\n\n";

// Tất cả giao dịch CHI
$allExpense = Transaction::where('type', 'chi')->sum('amount');
echo "📊 Tổng CHI (tất cả): " . number_format($allExpense) . "đ\n";

// Chi có incident_id (chi trong chuyến đi)
$expenseWithIncident = Transaction::where('type', 'chi')
    ->whereNotNull('incident_id')
    ->sum('amount');
echo "📊 Chi có incident_id (trong chuyến): " . number_format($expenseWithIncident) . "đ\n";

// Chi không có incident_id (chi trực tiếp)
$expenseWithoutIncident = Transaction::where('type', 'chi')
    ->whereNull('incident_id')
    ->sum('amount');
echo "📊 Chi không có incident_id (trực tiếp): " . number_format($expenseWithoutIncident) . "đ\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Chi của xe có chủ
$expenseOwnerVehicle = Transaction::where('type', 'chi')
    ->whereHas('vehicle', function($q) {
        $q->whereHas('owner');
    })
    ->sum('amount');
echo "📊 Chi của xe CÓ CHỦ: " . number_format($expenseOwnerVehicle) . "đ\n";

// Chi của xe không chủ hoặc không có vehicle_id
$expenseNonOwnerOrNull = Transaction::where('type', 'chi')
    ->where(function($q) {
        $q->whereNull('vehicle_id')
          ->orWhereHas('vehicle', function($vq) {
              $vq->whereDoesntHave('owner');
          });
    })
    ->sum('amount');
echo "📊 Chi của xe KHÔNG CHỦ hoặc không có vehicle_id: " . number_format($expenseNonOwnerOrNull) . "đ\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "❓ Câu hỏi: Công ty có cần trừ CHI của xe CÓ CHỦ không?\n";
echo "   - Nếu CÓ: Chi của xe có chủ được trừ từ doanh thu chuyến đi\n";
echo "   - Nếu KHÔNG: Chỉ tính chi trực tiếp công ty (không incident_id)\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Tất cả giao dịch THU
$allRevenue = Transaction::where('type', 'thu')->sum('amount');
echo "📊 Tổng THU (tất cả): " . number_format($allRevenue) . "đ\n";

// Tất cả dự kiến chi
$allPlannedExpense = Transaction::where('type', 'du_kien_chi')->sum('amount');
echo "📊 Tổng DỰ KIẾN CHI (tất cả): " . number_format($allPlannedExpense) . "đ\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "💡 Các phương án tính lợi nhuận:\n\n";

// Phương án 1: Tổng thu - Tất cả chi - Dự kiến chi
$profit1 = $allRevenue - $allExpense - $allPlannedExpense;
echo "1️⃣ Tổng thu - TẤT CẢ chi - Dự kiến chi\n";
echo "   = " . number_format($allRevenue) . " - " . number_format($allExpense) . " - " . number_format($allPlannedExpense) . "\n";
echo "   = " . number_format($profit1) . "đ\n\n";

// Phương án 2: Tổng thu - Chi không incident - Dự kiến chi không incident
$plannedExpenseWithoutIncident = Transaction::where('type', 'du_kien_chi')
    ->whereNull('incident_id')
    ->sum('amount');

$profit2 = $allRevenue - $expenseWithoutIncident - $plannedExpenseWithoutIncident;
echo "2️⃣ Tổng thu - Chi không incident - Dự kiến chi không incident\n";
echo "   = " . number_format($allRevenue) . " - " . number_format($expenseWithoutIncident) . " - " . number_format($plannedExpenseWithoutIncident) . "\n";
echo "   = " . number_format($profit2) . "đ\n\n";

// Phương án 3: Tổng thu - Chi xe không chủ/null - Dự kiến chi không incident
$profit3 = $allRevenue - $expenseNonOwnerOrNull - $plannedExpenseWithoutIncident;
echo "3️⃣ Tổng thu - Chi xe không chủ/null - Dự kiến chi không incident\n";
echo "   = " . number_format($allRevenue) . " - " . number_format($expenseNonOwnerOrNull) . " - " . number_format($plannedExpenseWithoutIncident) . "\n";
echo "   = " . number_format($profit3) . "đ\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🤔 Logic hiện tại đang dùng gì?\n";
echo "   Đã sửa thành: Thu trực tiếp công ty - Chi trực tiếp - Dự kiến chi + Lợi nhuận incidents\n";
echo "   = 81,074,347đ\n";
