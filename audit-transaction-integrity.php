<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Incident;
use App\Models\Transaction;

echo "=== Comprehensive System Audit: Transaction Integrity ===\n\n";

$issues = [
    'double_wages' => [],
    'mismatched_wages' => [],
    'orphaned_transactions' => [],
    'missing_transactions' => [],
];

// 1. Check for double wage transactions
echo "1. Checking for double wage transactions...\n";
echo "--------------------------------------------\n";

$incidents = Incident::with(['transactions', 'staff'])->get();

foreach ($incidents as $incident) {
    foreach ($incident->staff as $staff) {
        $expectedWage = $staff->pivot->wage_amount ?? 0;
        
        if ($expectedWage <= 0) {
            continue;
        }
        
        $wageTransactions = $incident->transactions
            ->where('staff_id', $staff->id)
            ->where('type', 'chi');
        
        if ($wageTransactions->count() > 1) {
            $issues['double_wages'][] = [
                'incident_id' => $incident->id,
                'staff_id' => $staff->id,
                'staff_name' => $staff->full_name,
                'transaction_count' => $wageTransactions->count(),
                'total_amount' => $wageTransactions->sum('amount'),
                'expected_amount' => $expectedWage,
            ];
        }
        
        // Check if sum matches expected
        $totalWage = $wageTransactions->sum('amount');
        if ($totalWage != $expectedWage && $totalWage > 0) {
            $issues['mismatched_wages'][] = [
                'incident_id' => $incident->id,
                'staff_id' => $staff->id,
                'staff_name' => $staff->full_name,
                'expected' => $expectedWage,
                'actual' => $totalWage,
                'difference' => $totalWage - $expectedWage,
            ];
        }
    }
    
    // Check for transactions without staff assignment
    $orphanedWages = $incident->transactions
        ->where('type', 'chi')
        ->filter(function($t) {
            return $t->staff_id != null && 
                   $t->note && (
                       strpos($t->note, 'Tiền công') !== false || 
                       strpos($t->note, 'lái xe') !== false || 
                       strpos($t->note, 'NVYT') !== false ||
                       strpos($t->note, 'y tế') !== false
                   );
        });
    
    foreach ($orphanedWages as $trans) {
        $staffAssigned = $incident->staff->where('id', $trans->staff_id)->first();
        if (!$staffAssigned) {
            $issues['orphaned_transactions'][] = [
                'incident_id' => $incident->id,
                'transaction_id' => $trans->id,
                'staff_id' => $trans->staff_id,
                'amount' => $trans->amount,
                'note' => $trans->note,
            ];
        }
    }
}

// 2. Check for staff assignments without transactions
echo "\n2. Checking for staff assignments without wage transactions...\n";
echo "---------------------------------------------------------------\n";

foreach ($incidents as $incident) {
    foreach ($incident->staff as $staff) {
        $expectedWage = $staff->pivot->wage_amount ?? 0;
        
        if ($expectedWage <= 0) {
            continue;
        }
        
        $wageTransactions = $incident->transactions
            ->where('staff_id', $staff->id)
            ->where('type', 'chi');
        
        if ($wageTransactions->count() == 0) {
            $issues['missing_transactions'][] = [
                'incident_id' => $incident->id,
                'staff_id' => $staff->id,
                'staff_name' => $staff->full_name,
                'role' => $staff->pivot->role,
                'expected_wage' => $expectedWage,
            ];
        }
    }
}

// Print results
echo "\n\n=== AUDIT RESULTS ===\n\n";

echo "1. Double Wage Transactions: " . count($issues['double_wages']) . "\n";
if (count($issues['double_wages']) > 0) {
    foreach ($issues['double_wages'] as $issue) {
        echo "   - Incident #{$issue['incident_id']}: {$issue['staff_name']} has {$issue['transaction_count']} transactions";
        echo " (Total: " . number_format($issue['total_amount'], 0, ',', '.') . "đ, Expected: " . number_format($issue['expected_amount'], 0, ',', '.') . "đ)\n";
    }
} else {
    echo "   ✓ No double transactions found\n";
}

echo "\n2. Mismatched Wages: " . count($issues['mismatched_wages']) . "\n";
if (count($issues['mismatched_wages']) > 0) {
    foreach ($issues['mismatched_wages'] as $issue) {
        echo "   - Incident #{$issue['incident_id']}: {$issue['staff_name']}";
        echo " - Expected: " . number_format($issue['expected'], 0, ',', '.') . "đ";
        echo ", Actual: " . number_format($issue['actual'], 0, ',', '.') . "đ";
        echo ", Diff: " . number_format($issue['difference'], 0, ',', '.') . "đ\n";
    }
} else {
    echo "   ✓ All wages match\n";
}

echo "\n3. Orphaned Transactions (transaction without staff assignment): " . count($issues['orphaned_transactions']) . "\n";
if (count($issues['orphaned_transactions']) > 0) {
    foreach ($issues['orphaned_transactions'] as $issue) {
        echo "   - Incident #{$issue['incident_id']}: Transaction #{$issue['transaction_id']}";
        echo " for Staff ID {$issue['staff_id']} - " . number_format($issue['amount'], 0, ',', '.') . "đ";
        echo " - {$issue['note']}\n";
    }
} else {
    echo "   ✓ No orphaned transactions\n";
}

echo "\n4. Missing Transactions (staff assigned but no wage transaction): " . count($issues['missing_transactions']) . "\n";
if (count($issues['missing_transactions']) > 0) {
    foreach ($issues['missing_transactions'] as $issue) {
        echo "   - Incident #{$issue['incident_id']}: {$issue['staff_name']} ({$issue['role']})";
        echo " - Expected wage: " . number_format($issue['expected_wage'], 0, ',', '.') . "đ";
        echo " - NO TRANSACTION FOUND!\n";
    }
} else {
    echo "   ✓ All staff assignments have transactions\n";
}

// Summary
echo "\n\n=== SUMMARY ===\n";
$totalIssues = count($issues['double_wages']) + count($issues['mismatched_wages']) + 
               count($issues['orphaned_transactions']) + count($issues['missing_transactions']);

if ($totalIssues > 0) {
    echo "⚠️  Total issues found: {$totalIssues}\n";
    echo "Action required: Review and fix the issues above\n";
} else {
    echo "✓ System is healthy! No issues found.\n";
}

echo "\n";
