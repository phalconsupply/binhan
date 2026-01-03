<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Incident;
use App\Models\Transaction;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

echo "╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║  TẠO GIAO DỊCH PHÍ 15% CHO CÁC CHUYẾN ĐI CŨ                         ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

echo "⚠️  Script này sẽ tạo giao dịch phí 15% cho TẤT CẢ các chuyến đi\n";
echo "   của XE CÓ CHỦ mà chưa có giao dịch phí 15%.\n\n";

echo "Nhấn ENTER để tiếp tục hoặc CTRL+C để hủy...\n";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

echo "\n🔄 Đang xử lý...\n\n";

DB::beginTransaction();

try {
    // Lấy tất cả xe có chủ
    $ownerVehicles = Vehicle::all()->filter(function($vehicle) {
        return $vehicle->hasOwner();
    });
    
    $totalCreated = 0;
    $totalSkipped = 0;
    $totalIncidents = 0;
    
    foreach ($ownerVehicles as $vehicle) {
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "🚗 XE: {$vehicle->license_plate} (ID: {$vehicle->id})\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        // Lấy tất cả incidents của xe
        $incidents = Incident::where('vehicle_id', $vehicle->id)->get();
        $totalIncidents += $incidents->count();
        
        foreach ($incidents as $incident) {
            // Kiểm tra xem đã có giao dịch phí 15% cho incident này chưa
            $existingFee = Transaction::where('incident_id', $incident->id)
                ->where('type', 'chi')
                ->where('category', 'phí_công_ty_15%')
                ->first();
            
            if ($existingFee) {
                $totalSkipped++;
                continue;
            }
            
            // Tính revenue của incident (không bao gồm vay)
            $incidentRevenue = Transaction::where('incident_id', $incident->id)
                ->where('type', 'thu')
                ->where(function($q) {
                    $q->where('category', '!=', 'vay_từ_công_ty')->orWhereNull('category');
                })
                ->sum('amount');
            
            // Tính expense của incident (không bao gồm bảo trì xe chủ riêng và phí 15%)
            $incidentExpense = Transaction::where('incident_id', $incident->id)
                ->where('type', 'chi')
                ->where(function($q) {
                    $q->whereNull('category')
                      ->orWhere('category', '!=', 'bảo_trì_xe_chủ_riêng');
                })
                ->sum('amount');
            
            // Tính profit và phí 15%
            $incidentProfit = $incidentRevenue - $incidentExpense;
            
            if ($incidentProfit > 0) {
                $companyFee = $incidentProfit * 0.15;
                
                Transaction::create([
                    'incident_id' => $incident->id,
                    'vehicle_id' => $vehicle->id,
                    'type' => 'chi',
                    'category' => 'phí_công_ty_15%',
                    'amount' => $companyFee,
                    'method' => 'bank',
                    'recorded_by' => 1, // Admin
                    'date' => $incident->date,
                    'note' => 'Phí công ty 15% - Chuyến đi #' . $incident->id . ' (tạo tự động)',
                ]);
                
                $totalCreated++;
                
                echo sprintf(
                    "   ✅ Incident #%d (%s): Thu=%s, Chi=%s, Lợi=%s → Phí=%s\n",
                    $incident->id,
                    $incident->date->format('d/m/Y'),
                    number_format($incidentRevenue, 0, ',', '.'),
                    number_format($incidentExpense, 0, ',', '.'),
                    number_format($incidentProfit, 0, ',', '.'),
                    number_format($companyFee, 0, ',', '.')
                );
            } else {
                $totalSkipped++;
                echo sprintf(
                    "   ⏭️  Incident #%d: Lợi nhuận <= 0 (%s), bỏ qua\n",
                    $incident->id,
                    number_format($incidentProfit, 0, ',', '.')
                );
            }
        }
        
        echo "\n";
    }
    
    DB::commit();
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📊 TỔNG KẾT:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "✅ Đã tạo:     {$totalCreated} giao dịch phí 15%\n";
    echo "⏭️  Bỏ qua:    {$totalSkipped} incident (đã có phí hoặc lỗ)\n";
    echo "📋 Tổng:       {$totalIncidents} incidents\n\n";
    
    echo "🎉 HOÀN TẤT!\n\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    
    echo "❌ LỖI: " . $e->getMessage() . "\n\n";
    echo $e->getTraceAsString() . "\n\n";
}
