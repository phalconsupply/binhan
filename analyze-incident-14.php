<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Incident;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

$incident = Incident::with(['transactions', 'staff'])->find(14);

if (!$incident) {
    echo "Incident #14 not found\n";
    exit;
}

echo "=== Incident #14 Analysis ===\n\n";
echo "Date: {$incident->date->format('d/m/Y')}\n";
echo "Vehicle: " . ($incident->vehicle ? $incident->vehicle->license_plate : 'N/A') . "\n";
echo "Patient: " . ($incident->patient ? $incident->patient->name : 'N/A') . "\n\n";

echo "--- Staff Assignments (incident_staff pivot table) ---\n";
$staffRecords = DB::table('incident_staff')
    ->where('incident_id', 14)
    ->get();

echo "Total records: {$staffRecords->count()}\n\n";

foreach ($staffRecords as $record) {
    $staff = \App\Models\Staff::find($record->staff_id);
    if ($staff) {
        echo "Staff: {$staff->full_name} (ID: {$record->staff_id}, Code: {$staff->employee_code})\n";
        echo "  Role: {$record->role}\n";
        echo "  Wage Amount: " . number_format($record->wage_amount ?? 0, 0, ',', '.') . "đ\n";
        echo "  Wage Details: " . ($record->wage_details ?? 'NULL') . "\n";
        echo "  Created: {$record->created_at}\n";
        echo "  Updated: {$record->updated_at}\n";
        echo "\n";
    }
}

echo "\n--- Active Transactions (transactions table) ---\n";
$transactions = Transaction::where('incident_id', 14)->get();
echo "Total transactions: {$transactions->count()}\n\n";

$groupedByType = $transactions->groupBy('type');

foreach ($groupedByType as $type => $trans) {
    echo strtoupper($type) . " ({$trans->count()} transactions):\n";
    foreach ($trans as $t) {
        echo "  ID {$t->id}: " . number_format($t->amount, 0, ',', '.') . "đ";
        if ($t->staff_id) {
            $staff = \App\Models\Staff::find($t->staff_id);
            echo " [Staff: {$t->staff_id} - " . ($staff ? $staff->employee_code . ' ' . $staff->full_name : 'Unknown') . "]";
        }
        echo " - {$t->note}\n";
        echo "      Created: {$t->created_at}\n";
    }
    echo "\n";
}

echo "\n--- Wage Transactions Analysis ---\n";

foreach ($staffRecords as $record) {
    $staff = \App\Models\Staff::find($record->staff_id);
    if (!$staff) continue;
    
    $expectedWage = $record->wage_amount ?? 0;
    
    echo "Staff: {$staff->full_name} (ID: {$record->staff_id}, Code: {$staff->employee_code})\n";
    echo "  Role: {$record->role}\n";
    echo "  Expected wage (from incident_staff): " . number_format($expectedWage, 0, ',', '.') . "đ\n";
    
    // Find wage transactions
    $wageTransactions = Transaction::where('incident_id', 14)
        ->where('staff_id', $record->staff_id)
        ->where('type', 'chi')
        ->get();
    
    echo "  Wage transactions found: {$wageTransactions->count()}\n";
    
    if ($wageTransactions->count() > 0) {
        $totalFromTransactions = $wageTransactions->sum('amount');
        echo "  Total from transactions: " . number_format($totalFromTransactions, 0, ',', '.') . "đ\n";
        
        foreach ($wageTransactions as $trans) {
            echo "    - Transaction #{$trans->id}: " . number_format($trans->amount, 0, ',', '.') . "đ - {$trans->note}\n";
        }
        
        if ($totalFromTransactions != $expectedWage) {
            echo "  ⚠️  MISMATCH: Transaction total ≠ Expected wage\n";
            echo "      Difference: " . number_format($totalFromTransactions - $expectedWage, 0, ',', '.') . "đ\n";
        } else {
            echo "  ✓ MATCH: Transactions match expected wage\n";
        }
    } else {
        if ($expectedWage > 0) {
            echo "  ❌ ISSUE: No wage transactions found but expected wage is " . number_format($expectedWage, 0, ',', '.') . "đ\n";
            echo "  💡 This is the problem! incident_staff has wage but no transaction exists.\n";
        } else {
            echo "  ✓ OK: No wage expected, no transactions\n";
        }
    }
    echo "\n";
}

echo "\n--- Issue Summary ---\n";
echo "When user clicks 'Edit', the form loads data from incident_staff table.\n";
echo "If incident_staff has wage_amount but no corresponding transaction exists,\n";
echo "the form will show the wage amount (from incident_staff) even though\n";
echo "the transaction was deleted.\n\n";

echo "SOLUTION: When showing edit form, check if wage transaction exists.\n";
echo "If no transaction exists, either:\n";
echo "  1. Don't show the wage in edit form (sync with reality), OR\n";
echo "  2. Clear wage_amount in incident_staff when transaction is deleted\n";
echo "\n";
