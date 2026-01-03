<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;

$vehicle = Vehicle::where('license_plate', '49B08879')->first();

echo "=================================================================\n";
echo "TỔNG CHI XE 49B08879 THEO DANH MỤC\n";
echo "=================================================================\n\n";

echo "🚗 XE: {$vehicle->license_plate} (ID: {$vehicle->id})\n\n";

// Lấy tất cả giao dịch chi
$expenses = $vehicle->transactions()->expense()->orderBy('date')->get();

// Phân loại theo category
$maintenance = 0;           // Bảo trì
$perTrip = 0;              // Chi theo chuyến
$managementFee = 0;        // Phí quản lý 15%
$other = 0;                // Chi khác

$maintenanceList = [];
$perTripList = [];
$managementFeeList = [];
$otherList = [];

foreach ($expenses as $tx) {
    $category = $tx->category;
    
    if ($category === 'bảo_trì' || $category === 'bao_tri') {
        $maintenance += $tx->amount;
        $maintenanceList[] = $tx;
    } elseif ($category === 'chi_theo_chuyến' || $category === 'chi_theo_chuyen') {
        $perTrip += $tx->amount;
        $perTripList[] = $tx;
    } elseif ($category === 'phí_quản_lý' || $category === 'phi_quan_ly') {
        $managementFee += $tx->amount;
        $managementFeeList[] = $tx;
    } else {
        $other += $tx->amount;
        $otherList[] = $tx;
    }
}

$totalExpense = $maintenance + $perTrip + $managementFee + $other;

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📊 TỔNG HỢP CHI THEO DANH MỤC\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "1. Bảo trì:              " . number_format($maintenance, 0, ',', '.') . "đ (" . count($maintenanceList) . " GD)\n";
echo "2. Chi theo chuyến:      " . number_format($perTrip, 0, ',', '.') . "đ (" . count($perTripList) . " GD)\n";
echo "3. Phí quản lý 15%:      " . number_format($managementFee, 0, ',', '.') . "đ (" . count($managementFeeList) . " GD)\n";
echo "4. Chi khác:             " . number_format($other, 0, ',', '.') . "đ (" . count($otherList) . " GD)\n";
echo "──────────────────────────────────────────\n";
echo "TỔNG CHI:                " . number_format($totalExpense, 0, ',', '.') . "đ (" . count($expenses) . " GD)\n\n";

// Hiển thị tỷ lệ phần trăm
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📈 TỶ LỆ PHẦN TRĂM\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

if ($totalExpense > 0) {
    echo "Bảo trì:            " . number_format($maintenance / $totalExpense * 100, 1) . "%\n";
    echo "Chi theo chuyến:    " . number_format($perTrip / $totalExpense * 100, 1) . "%\n";
    echo "Phí quản lý 15%:    " . number_format($managementFee / $totalExpense * 100, 1) . "%\n";
    echo "Chi khác:           " . number_format($other / $totalExpense * 100, 1) . "%\n\n";
}

// Chi tiết từng danh mục
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 CHI TIẾT BẢO TRÌ (" . count($maintenanceList) . " giao dịch)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

if (count($maintenanceList) > 0) {
    foreach ($maintenanceList as $tx) {
        echo sprintf("%-20s | %-12s | %15s | %s\n",
            $tx->code,
            $tx->date->format('d/m/Y'),
            number_format($tx->amount, 0, ',', '.') . 'đ',
            substr($tx->note ?? '', 0, 30)
        );
    }
} else {
    echo "Không có giao dịch bảo trì\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 CHI TIẾT CHI THEO CHUYẾN (" . count($perTripList) . " giao dịch)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

if (count($perTripList) > 0) {
    foreach ($perTripList as $tx) {
        echo sprintf("%-20s | %-12s | %15s | %s\n",
            $tx->code,
            $tx->date->format('d/m/Y'),
            number_format($tx->amount, 0, ',', '.') . 'đ',
            substr($tx->note ?? '', 0, 30)
        );
    }
} else {
    echo "Không có giao dịch chi theo chuyến\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 CHI TIẾT PHÍ QUẢN LÝ 15% (" . count($managementFeeList) . " giao dịch)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

if (count($managementFeeList) > 0) {
    foreach ($managementFeeList as $tx) {
        echo sprintf("%-20s | %-12s | %15s | %s\n",
            $tx->code,
            $tx->date->format('d/m/Y'),
            number_format($tx->amount, 0, ',', '.') . 'đ',
            substr($tx->note ?? '', 0, 30)
        );
    }
} else {
    echo "Không có giao dịch phí quản lý 15%\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "📋 CHI TIẾT CHI KHÁC (" . count($otherList) . " giao dịch)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

if (count($otherList) > 0) {
    // Nhóm theo category
    $categoryGroups = [];
    foreach ($otherList as $tx) {
        $cat = $tx->category ?? 'không_có_category';
        if (!isset($categoryGroups[$cat])) {
            $categoryGroups[$cat] = [];
        }
        $categoryGroups[$cat][] = $tx;
    }
    
    foreach ($categoryGroups as $cat => $txList) {
        $catTotal = array_sum(array_map(fn($t) => $t->amount, $txList));
        echo "\n▶ Category: {$cat} (" . count($txList) . " GD, " . number_format($catTotal, 0, ',', '.') . "đ)\n";
        foreach ($txList as $tx) {
            echo sprintf("  %-20s | %-12s | %15s | %s\n",
                $tx->code,
                $tx->date->format('d/m/Y'),
                number_format($tx->amount, 0, ',', '.') . 'đ',
                substr($tx->note ?? '', 0, 30)
            );
        }
    }
} else {
    echo "Không có giao dịch chi khác\n";
}

// Kiểm tra tổng chi
echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✓ KIỂM TRA TỔNG CHI\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$totalFromScope = $vehicle->transactions()->expense()->sum('amount');
echo "Tổng chi (theo scope):        " . number_format($totalFromScope, 0, ',', '.') . "đ\n";
echo "Tổng chi (theo phân loại):    " . number_format($totalExpense, 0, ',', '.') . "đ\n";

if (abs($totalFromScope - $totalExpense) < 0.01) {
    echo "✅ Khớp!\n";
} else {
    echo "❌ Không khớp! Chênh lệch: " . number_format(abs($totalFromScope - $totalExpense), 0, ',', '.') . "đ\n";
}

echo "\n";
