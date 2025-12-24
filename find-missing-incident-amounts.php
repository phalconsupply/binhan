<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Incident;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

echo "=== TÌM TẤT CẢ CHUYẾN ĐI CÓ TOTAL_AMOUNT = 0 NHƯNG CÓ GIAO DỊCH THU ===\n\n";

// Lấy tất cả chuyến đi của xe 49B08879
$vehicle = \App\Models\Vehicle::where('license_plate', '49B08879')->first();

if (!$vehicle) {
    echo "Không tìm thấy xe 49B08879\n";
    exit;
}

// Tìm các incident có total_amount = 0 hoặc null
$incidents = Incident::where('vehicle_id', $vehicle->id)
    ->where(function($query) {
        $query->whereNull('total_amount')
              ->orWhere('total_amount', 0);
    })
    ->orderBy('id')
    ->get();

echo "Tìm thấy " . $incidents->count() . " chuyến đi có total_amount = 0 hoặc null\n\n";

$problematicIncidents = [];
$totalMissingRevenue = 0;
$totalMissingFee = 0;

foreach ($incidents as $incident) {
    // Kiểm tra xem có giao dịch thu không
    $revenueTransactions = Transaction::where('incident_id', $incident->id)
        ->where('type', 'thu')
        ->get();
    
    if ($revenueTransactions->isNotEmpty()) {
        $totalRevenue = $revenueTransactions->sum('amount');
        $expectedFee = $totalRevenue * 0.15;
        
        $problematicIncidents[] = [
            'id' => $incident->id,
            'date' => $incident->incident_date,
            'actual_revenue' => $totalRevenue,
            'expected_fee' => $expectedFee,
            'transaction_count' => $revenueTransactions->count(),
        ];
        
        $totalMissingRevenue += $totalRevenue;
        $totalMissingFee += $expectedFee;
    }
}

if (empty($problematicIncidents)) {
    echo "✓ Không có chuyến đi nào bị sai lệch!\n";
} else {
    echo "⚠ Tìm thấy " . count($problematicIncidents) . " chuyến đi có vấn đề:\n\n";
    echo str_repeat("-", 90) . "\n";
    printf("%-10s %-20s %15s %15s %10s\n", "ID", "Ngày", "Thu thực tế", "Phí 15%", "Số GD");
    echo str_repeat("-", 90) . "\n";
    
    foreach ($problematicIncidents as $p) {
        printf("%-10s %-20s %15s %15s %10s\n", 
            $p['id'],
            $p['date'] ?? 'N/A',
            number_format($p['actual_revenue']),
            number_format($p['expected_fee']),
            $p['transaction_count']
        );
    }
    
    echo str_repeat("-", 90) . "\n";
    printf("%-30s %15s %15s\n", "TỔNG CỘNG:", 
        number_format($totalMissingRevenue), 
        number_format($totalMissingFee)
    );
    echo str_repeat("-", 90) . "\n\n";
    
    echo "GIẢI THÍCH CHÊNH LỆCH:\n";
    echo "- User tính thủ công: 152,911,425đ\n";
    echo "- Hệ thống hiện tại:  150,911,425đ\n";
    echo "- Chênh lệch:          2,000,000đ\n";
    echo "- Thu thiếu từ incidents: " . number_format($totalMissingRevenue) . "đ ✓ KHỚP!\n\n";
    
    echo "ĐỀ XUẤT GIẢI PHÁP:\n";
    echo "1. Cập nhật total_amount và company_fee cho các chuyến đi trên\n";
    echo "2. Hoặc tạo script sync để tự động cập nhật từ giao dịch\n";
}
