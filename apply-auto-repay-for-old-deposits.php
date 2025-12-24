<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\{Transaction, Vehicle};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "APPLY AUTO-REPAY LOGIC CHO CAC GIAO DICH NOP QUY CU\n";
echo str_repeat('=', 100) . "\n\n";

DB::beginTransaction();

try {
    // Lấy tất cả giao dịch nộp quỹ, sắp xếp theo ngày tăng dần
    $fundDeposits = Transaction::where('type', 'nop_quy')
        ->whereNotNull('vehicle_id')
        ->orderBy('date')
        ->orderBy('id')
        ->get();

    echo "Tong so giao dich nop quy: " . $fundDeposits->count() . "\n\n";

    $processedCount = 0;
    $skippedCount = 0;
    $totalRepaid = 0;

    foreach ($fundDeposits as $fundDeposit) {
        $vehicle = $fundDeposit->vehicle;
        
        if (!$vehicle || !$vehicle->hasOwner()) {
            $skippedCount++;
            continue;
        }

        // Tính nợ TẠI THỜI ĐIỂM nộp quỹ (trước ngày nộp quỹ)
        $totalBorrowed = Transaction::where('vehicle_id', $vehicle->id)
            ->where('type', 'vay_cong_ty')
            ->where('date', '<=', $fundDeposit->date)
            ->sum('amount');

        $totalRepaid = Transaction::where('vehicle_id', $vehicle->id)
            ->where('type', 'tra_cong_ty')
            ->where('date', '<', $fundDeposit->date) // Trước ngày nộp quỹ (không bao gồm cùng ngày)
            ->where('id', '<', $fundDeposit->id) // Và có ID nhỏ hơn để đảm bảo thứ tự
            ->sum('amount');

        $currentDebt = $totalBorrowed - $totalRepaid;

        if ($currentDebt <= 0) {
            echo sprintf(
                "SKIP: GD #%-4s | %s | Xe %s | Nop quy: %s | Khong co no\n",
                $fundDeposit->id,
                Carbon::parse($fundDeposit->date)->format('d/m/Y'),
                $vehicle->license_plate,
                number_format($fundDeposit->amount, 0, ',', '.')
            );
            $skippedCount++;
            continue;
        }

        $depositAmount = $fundDeposit->amount;
        $repayAmount = min($currentDebt, $depositAmount);

        // Kiểm tra xem đã có giao dịch trả nợ tự động cho lần nộp quỹ này chưa
        $existingRepay = Transaction::where('vehicle_id', $vehicle->id)
            ->where('type', 'tra_cong_ty')
            ->where('date', $fundDeposit->date)
            ->where('note', 'like', "%GD #{$fundDeposit->id}%")
            ->first();

        if ($existingRepay) {
            echo sprintf(
                "EXIST: GD #%-4s | %s | Xe %s | Da co giao dich tra no #%s\n",
                $fundDeposit->id,
                Carbon::parse($fundDeposit->date)->format('d/m/Y'),
                $vehicle->license_plate,
                $existingRepay->id
            );
            $skippedCount++;
            continue;
        }

        // Tạo giao dịch trả nợ (trừ tiền chủ xe)
        $repayTransaction = Transaction::create([
            'vehicle_id' => $vehicle->id,
            'type' => 'tra_cong_ty',
            'amount' => $repayAmount,
            'date' => $fundDeposit->date,
            'method' => $fundDeposit->method,
            'note' => "Tự động trả nợ từ nộp quỹ (GD #{$fundDeposit->id})",
            'recorded_by' => $fundDeposit->recorded_by,
        ]);

        // Tạo giao dịch thu vào công ty (không có vehicle_id)
        $companyRevenueTransaction = Transaction::create([
            'vehicle_id' => null,
            'type' => 'thu',
            'amount' => $repayAmount,
            'date' => $fundDeposit->date,
            'method' => $fundDeposit->method,
            'note' => "Thu từ xe {$vehicle->license_plate} trả nợ (GD #{$repayTransaction->id})",
            'recorded_by' => $fundDeposit->recorded_by,
        ]);

        echo sprintf(
            "CREATE: GD #%-4s | %s | Xe %s | Nop quy: %s | No: %s | Tra: %s -> GD #%s (xe) + GD #%s (cty)\n",
            $fundDeposit->id,
            Carbon::parse($fundDeposit->date)->format('d/m/Y'),
            $vehicle->license_plate,
            str_pad(number_format($depositAmount, 0, ',', '.'), 12, ' ', STR_PAD_LEFT),
            str_pad(number_format($currentDebt, 0, ',', '.'), 12, ' ', STR_PAD_LEFT),
            str_pad(number_format($repayAmount, 0, ',', '.'), 12, ' ', STR_PAD_LEFT),
            $repayTransaction->id,
            $companyRevenueTransaction->id
        );

        $processedCount++;
        $totalRepaid += $repayAmount;
    }

    echo "\n" . str_repeat('=', 100) . "\n";
    echo "KET QUA:\n";
    echo "  Tong so giao dich nop quy:        " . $fundDeposits->count() . "\n";
    echo "  Da tao giao dich tra no moi:      " . $processedCount . "\n";
    echo "  Bo qua (khong co no hoac da co):  " . $skippedCount . "\n";
    echo "  Tong so tien da tra:              " . number_format($totalRepaid, 0, ',', '.') . "d\n";
    
    echo "\n";
    
    // Auto commit without confirmation
    DB::commit();
    echo "✓ DA COMMIT! Cac giao dich tra no da duoc tao thanh cong.\n";

} catch (Exception $e) {
    DB::rollBack();
    echo "\n✗ LOI: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
