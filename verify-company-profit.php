<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Vehicle;
use App\Models\Incident;

echo "🔍 Kiểm tra logic tính lợi nhuận công ty\n\n";

// Company direct revenue (without vehicle_id or vehicle has no owner)
$companyDirectRevenue = Transaction::where('type', 'thu')
    ->where(function($q) {
        $q->whereNull('vehicle_id')
          ->orWhereHas('vehicle', function($vq) {
              $vq->whereDoesntHave('owner');
          });
    })
    ->sum('amount');

echo "📊 Thu trực tiếp công ty (không có vehicle_id hoặc xe không chủ): " . number_format($companyDirectRevenue) . "đ\n";

// Company direct expense (without incident_id)
$companyDirectExpense = Transaction::where('type', 'chi')
    ->whereNull('incident_id')
    ->sum('amount');

echo "📊 Chi trực tiếp công ty (không có incident_id): " . number_format($companyDirectExpense) . "đ\n";

// Company planned expense
$companyPlannedExpense = Transaction::where('type', 'du_kien_chi')
    ->whereNull('incident_id')
    ->sum('amount');

echo "📊 Dự kiến chi công ty: " . number_format($companyPlannedExpense) . "đ\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Calculate profit from incidents
$incidentProfit = 0;
$allIncidents = Incident::with('vehicle.owner')->get();

foreach ($allIncidents as $incident) {
    $incidentRevenue = $incident->transactions()->where('type', 'thu')->sum('amount');
    $incidentExpense = $incident->transactions()->where('type', 'chi')->sum('amount');
    $incidentPlannedExpense = $incident->transactions()->where('type', 'du_kien_chi')->sum('amount');
    $incidentNet = $incidentRevenue - $incidentExpense - $incidentPlannedExpense;
    
    if ($incidentNet > 0) {
        if ($incident->vehicle && $incident->vehicle->hasOwner()) {
            $incidentProfit += $incidentNet * 0.15;
        } else {
            $incidentProfit += $incidentNet;
        }
    }
}

echo "📊 Lợi nhuận từ incidents (15% xe có chủ, 100% xe không chủ): " . number_format($incidentProfit) . "đ\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Total company profit
$totalProfit = $companyDirectRevenue - $companyDirectExpense - $companyPlannedExpense + $incidentProfit;

echo "💰 LỢI NHUẬN CÔNG TY:\n";
echo "   = Thu trực tiếp - Chi trực tiếp - Dự kiến chi + Lợi nhuận incidents\n";
echo "   = " . number_format($companyDirectRevenue) . " - " . number_format($companyDirectExpense) . " - " . number_format($companyPlannedExpense) . " + " . number_format($incidentProfit) . "\n";
echo "   = " . number_format($totalProfit) . "đ\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Check transaction #754
$transaction754 = Transaction::find(754);
if ($transaction754) {
    echo "\n✓ Giao dịch #754:\n";
    echo "  - Loại: {$transaction754->type}\n";
    echo "  - Số tiền: " . number_format($transaction754->amount) . "đ\n";
    echo "  - vehicle_id: " . ($transaction754->vehicle_id ?? 'NULL') . "\n";
    echo "  - Có được tính vào Thu trực tiếp công ty: " . ($transaction754->vehicle_id === null && $transaction754->type === 'thu' ? 'CÓ ✓' : 'KHÔNG ✗') . "\n";
}
