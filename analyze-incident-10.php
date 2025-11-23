<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Incident;
use App\Models\Transaction;

$incident = Incident::with(['transactions', 'staff'])->find(10);

if (!$incident) {
    echo "Incident #10 not found\n";
    exit;
}

echo "=== Incident #10 Analysis ===\n\n";
echo "Date: {$incident->date->format('d/m/Y')}\n";
echo "Vehicle: " . ($incident->vehicle ? $incident->vehicle->license_plate : 'N/A') . "\n";
echo "Patient: " . ($incident->patient ? $incident->patient->name : 'N/A') . "\n\n";

echo "--- Staff Assignments (incident_staff table) ---\n";
foreach ($incident->staff as $staff) {
    echo "Staff: {$staff->full_name} (ID: {$staff->id}) - Role: {$staff->pivot->role}\n";
    echo "  Wage Amount: " . number_format($staff->pivot->wage_amount ?? 0, 0, ',', '.') . "đ\n";
    if ($staff->pivot->wage_details) {
        echo "  Details: {$staff->pivot->wage_details}\n";
    }
    echo "\n";
}

echo "\n--- Transactions (transactions table) ---\n";
echo "Total: {$incident->transactions->count()}\n\n";

$groupedByType = $incident->transactions->groupBy('type');

foreach ($groupedByType as $type => $trans) {
    echo strtoupper($type) . " ({$trans->count()} transactions):\n";
    foreach ($trans as $t) {
        echo "  ID {$t->id}: " . number_format($t->amount, 0, ',', '.') . "đ";
        if ($t->staff_id) echo " [Staff: {$t->staff_id}]";
        if ($t->transaction_category) echo " [Category: {$t->transaction_category}]";
        echo " - {$t->note}\n";
    }
    echo "\n";
}

echo "\n--- Issue Detection: Double Wage Transactions ---\n";

// Find wage transactions
$wageTransactions = $incident->transactions->filter(function($t) {
    return $t->staff_id != null && $t->note && (
        strpos($t->note, 'Tiền công') !== false || 
        strpos($t->note, 'lái xe') !== false || 
        strpos($t->note, 'NVYT') !== false ||
        strpos($t->note, 'Công') !== false
    );
});

echo "Wage transactions count: {$wageTransactions->count()}\n\n";

$wageByStaff = $wageTransactions->groupBy('staff_id');

foreach ($wageByStaff as $staffId => $wages) {
    $staffMember = \App\Models\Staff::find($staffId);
    $staffName = $staffMember ? $staffMember->full_name : "Unknown";
    
    echo "Staff ID {$staffId} ({$staffName}): {$wages->count()} wage transaction(s)\n";
    
    foreach ($wages as $w) {
        echo "  - Transaction ID {$w->id}: " . number_format($w->amount, 0, ',', '.') . "đ";
        echo " - Created: {$w->created_at->format('d/m/Y H:i:s')}";
        echo " - Note: {$w->note}\n";
    }
    
    if ($wages->count() > 1) {
        echo "  ⚠️ WARNING: DOUBLE TRANSACTION DETECTED!\n";
        echo "  Total duplicated: " . number_format($wages->sum('amount'), 0, ',', '.') . "đ\n";
        
        // Check if they have same amounts
        $amounts = $wages->pluck('amount')->unique();
        if ($amounts->count() == 1) {
            echo "  Same amounts detected - likely duplicated by system\n";
        }
    }
    echo "\n";
}

echo "\n--- Root Cause Analysis ---\n";

// Check incident_staff records
$staffRecords = DB::table('incident_staff')
    ->where('incident_id', 10)
    ->get();

echo "incident_staff records: {$staffRecords->count()}\n";
foreach ($staffRecords as $record) {
    echo "  Staff ID {$record->staff_id}: Role={$record->role}, Wage={$record->wage_amount}đ\n";
}

echo "\n--- Comparison: incident_staff vs transactions ---\n";

foreach ($incident->staff as $staff) {
    $wageFromPivot = $staff->pivot->wage_amount ?? 0;
    $wageFromTransactions = $incident->transactions
        ->where('staff_id', $staff->id)
        ->where('type', 'chi')
        ->sum('amount');
    
    echo "Staff: {$staff->full_name} (ID: {$staff->id})\n";
    echo "  incident_staff.wage_amount: " . number_format($wageFromPivot, 0, ',', '.') . "đ\n";
    echo "  Sum of transactions: " . number_format($wageFromTransactions, 0, ',', '.') . "đ\n";
    
    if ($wageFromTransactions > $wageFromPivot && $wageFromPivot > 0) {
        $ratio = $wageFromTransactions / $wageFromPivot;
        echo "  ⚠️ MISMATCH: Transactions are " . round($ratio, 2) . "x larger!\n";
        if (abs($ratio - 2.0) < 0.1) {
            echo "  💡 LIKELY CAUSE: Double creation (created twice)\n";
        }
    } elseif ($wageFromTransactions == $wageFromPivot) {
        echo "  ✓ MATCH: Correct\n";
    }
    echo "\n";
}

echo "\n";
