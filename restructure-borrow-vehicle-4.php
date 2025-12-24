<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\{Vehicle, Transaction};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

try {
    $vehicle = Vehicle::find(4);
    echo "Xe: {$vehicle->license_plate}\n\n";
    
    // Bước 1: Xóa giao dịch vay tổng hiện tại
    echo "BUOC 1: XOA GIAO DICH VAY TONG\n";
    echo str_repeat('=', 80) . "\n";
    $existingBorrows = Transaction::where('vehicle_id', 4)
        ->where('type', 'vay_cong_ty')
        ->get();
    
    foreach ($existingBorrows as $borrow) {
        echo "Xoa giao dich ID {$borrow->id}: " . number_format($borrow->amount) . "d\n";
        $borrow->delete();
    }
    
    echo "\n";
    
    // Bước 2: Duyệt từng giao dịch chi, nếu số dư âm thì tạo giao dịch vay
    echo "BUOC 2: TAO GIAO DICH VAY CHO TUNG LAN CHI\n";
    echo str_repeat('=', 80) . "\n";
    
    $balance = 0;
    $chiTransactions = Transaction::where('vehicle_id', 4)
        ->where('type', 'chi')
        ->orderBy('date')
        ->orderBy('id')
        ->get();
    
    foreach ($chiTransactions as $chi) {
        // Trừ tiền chi
        $balance -= $chi->amount;
        
        echo sprintf(
            "ID %-4s | %s | Chi: -%s | So du: %s",
            $chi->id,
            Carbon::parse($chi->date)->format('d/m/Y'),
            str_pad(number_format($chi->amount, 0, ',', '.'), 13, ' ', STR_PAD_LEFT),
            str_pad(number_format($balance, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)
        );
        
        // Nếu số dư âm, tạo giao dịch vay
        if ($balance < 0) {
            $borrowAmount = abs($balance);
            
            $borrowTransaction = Transaction::create([
                'vehicle_id' => 4,
                'type' => 'vay_cong_ty',
                'amount' => $borrowAmount,
                'date' => $chi->date, // Cùng ngày với giao dịch chi
                'method' => 'bank',
                'note' => "Vay công ty để chi: " . ($chi->note ?? 'Bảo trì'),
                'staff_id' => $chi->staff_id ?? null,
                'recorded_by' => auth()->id() ?? 1, // User hiện tại hoặc admin
            ]);
            
            $balance = 0; // Sau khi vay thì số dư về 0
            
            echo " => TAO VAY ID {$borrowTransaction->id}: +{$borrowAmount}d\n";
        } else {
            echo "\n";
        }
    }
    
    echo "\n" . str_repeat('=', 80) . "\n";
    echo "BUOC 3: KIEM TRA LAI SO DU CUOI CUNG\n";
    echo str_repeat('=', 80) . "\n";
    
    $finalBalance = 0;
    $allTransactions = Transaction::where('vehicle_id', 4)
        ->whereIn('type', ['thu', 'chi', 'vay_cong_ty'])
        ->orderBy('date')
        ->orderBy('id')
        ->get();
    
    foreach ($allTransactions as $t) {
        if ($t->type == 'thu' || $t->type == 'vay_cong_ty') {
            $finalBalance += $t->amount;
            $sign = '+';
        } else {
            $finalBalance -= $t->amount;
            $sign = '-';
        }
        
        echo sprintf(
            "ID %-4s | %s | %s | %s%s | So du: %s\n",
            $t->id,
            Carbon::parse($t->date)->format('d/m/Y'),
            str_pad($t->type, 12),
            $sign,
            str_pad(number_format($t->amount, 0, ',', '.'), 13, ' ', STR_PAD_LEFT),
            str_pad(number_format($finalBalance, 0, ',', '.'), 15, ' ', STR_PAD_LEFT)
        );
    }
    
    echo "\n" . str_repeat('=', 80) . "\n";
    echo "SO DU CUOI CUNG: " . number_format($finalBalance, 0, ',', '.') . "d\n\n";
    
    $totalBorrowed = Transaction::where('vehicle_id', 4)
        ->where('type', 'vay_cong_ty')
        ->sum('amount');
    
    echo "TONG SO TIEN DA VAY: " . number_format($totalBorrowed, 0, ',', '.') . "d\n";
    echo "SO GIAO DICH VAY: " . Transaction::where('vehicle_id', 4)->where('type', 'vay_cong_ty')->count() . " giao dich\n";
    
    DB::commit();
    echo "\n✓ HOAN TAT! Da dieu chinh thanh cong.\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "\n✗ LOI: " . $e->getMessage() . "\n";
}
