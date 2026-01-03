<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use Carbon\Carbon;

echo "╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║  KIỂM TRA FILTER THÁNG - THỐNG KÊ CÔNG TY                           ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

echo "📊 THỐNG KÊ THEO THÁNG (12 tháng gần nhất):\n\n";

$monthlyStats = [];
$totalRevenue = 0;
$totalExpense = 0;
$totalPlanned = 0;

for ($i = 0; $i < 12; $i++) {
    $month = Carbon::now()->subMonths($i);
    $year = $month->year;
    $monthNum = $month->month;
    $label = $month->format('m/Y');
    
    // Tổng thu
    $revenue = Transaction::revenue()
        ->whereYear('date', $year)
        ->whereMonth('date', $monthNum)
        ->where(function($q) {
            $q->where('category', '!=', 'vay_từ_công_ty')->orWhereNull('category');
        })->sum('amount');
        
    $fundDeposit = Transaction::fundDeposit()
        ->whereYear('date', $year)
        ->whereMonth('date', $monthNum)
        ->sum('amount');
        
    $monthRevenue = $revenue + $fundDeposit;
    
    // Tổng chi
    $monthExpense = Transaction::expense()
        ->whereYear('date', $year)
        ->whereMonth('date', $monthNum)
        ->sum('amount');
    
    // Dự kiến chi
    $monthPlanned = Transaction::plannedExpense()
        ->whereYear('date', $year)
        ->whereMonth('date', $monthNum)
        ->sum('amount');
    
    // Lợi nhuận
    $monthProfit = $monthRevenue - $monthExpense - $monthPlanned;
    
    $monthlyStats[] = [
        'label' => $label,
        'revenue' => $monthRevenue,
        'expense' => $monthExpense,
        'planned' => $monthPlanned,
        'profit' => $monthProfit,
    ];
    
    $totalRevenue += $monthRevenue;
    $totalExpense += $monthExpense;
    $totalPlanned += $monthPlanned;
}

// Hiển thị bảng
echo "┌──────────┬──────────────────┬──────────────────┬──────────────────┬──────────────────┐\n";
echo "│  Tháng   │   Tổng thu       │   Tổng chi       │   Dự kiến chi    │   Lợi nhuận      │\n";
echo "├──────────┼──────────────────┼──────────────────┼──────────────────┼──────────────────┤\n";

foreach (array_reverse($monthlyStats) as $stat) {
    printf(
        "│ %-8s │ %14s đ │ %14s đ │ %14s đ │ %14s đ │\n",
        $stat['label'],
        number_format($stat['revenue'], 0, ',', '.'),
        number_format($stat['expense'], 0, ',', '.'),
        number_format($stat['planned'], 0, ',', '.'),
        number_format($stat['profit'], 0, ',', '.')
    );
}

echo "├──────────┼──────────────────┼──────────────────┼──────────────────┼──────────────────┤\n";
printf(
    "│ %-8s │ %14s đ │ %14s đ │ %14s đ │ %14s đ │\n",
    "TỔNG",
    number_format($totalRevenue, 0, ',', '.'),
    number_format($totalExpense, 0, ',', '.'),
    number_format($totalPlanned, 0, ',', '.'),
    number_format($totalRevenue - $totalExpense - $totalPlanned, 0, ',', '.')
);
echo "└──────────┴──────────────────┴──────────────────┴──────────────────┴──────────────────┘\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "📈 TOP 5 THÁNG LỢI NHUẬN CAO NHẤT:\n";
usort($monthlyStats, function($a, $b) {
    return $b['profit'] <=> $a['profit'];
});

foreach (array_slice($monthlyStats, 0, 5) as $idx => $stat) {
    $icon = $stat['profit'] >= 0 ? '✅' : '⚠️';
    echo "  " . ($idx + 1) . ". {$icon} {$stat['label']}: " . number_format($stat['profit'], 0, ',', '.') . "đ\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "📉 TOP 5 THÁNG CHI TIÊU NHIỀU NHẤT:\n";
usort($monthlyStats, function($a, $b) {
    return $b['expense'] <=> $a['expense'];
});

foreach (array_slice($monthlyStats, 0, 5) as $idx => $stat) {
    echo "  " . ($idx + 1) . ". {$stat['label']}: " . number_format($stat['expense'], 0, ',', '.') . "đ\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "💡 HƯỚNG DẪN SỬ DỤNG FILTER:\n";
echo "  1. Mở /transactions trên trình duyệt\n";
echo "  2. Tìm dropdown 'Xem theo tháng' ở góc phải\n";
echo "  3. Chọn:\n";
echo "     • 'Tất cả thời gian' - Xem tổng hợp toàn bộ\n";
echo "     • 'Tháng này' - Chỉ xem tháng hiện tại\n";
echo "     • Chọn 1 tháng cụ thể (VD: 12/2025)\n";
echo "     • Giữ Ctrl + Click để chọn nhiều tháng → Hiển thị dạng bảng\n";
echo "  4. Click 'Cập nhật' để xem kết quả\n\n";

echo "✅ TÍNH NĂNG MỚI:\n";
echo "  • Filter theo tháng: Chọn 1 hoặc nhiều tháng cùng lúc\n";
echo "  • Hiển thị card khi chọn 1 tháng\n";
echo "  • Hiển thị bảng khi chọn nhiều tháng\n";
echo "  • Tự động tính tổng cộng khi chọn nhiều tháng\n";
echo "  • Giữ nguyên các filter khác (xe, loại giao dịch, ...)\n";
