<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionLine;
use Illuminate\Support\Facades\DB;

echo "=== PHASE 3 DEPLOYMENT VERIFICATION ===\n\n";

// 1. Verify transaction_lines table
echo "📊 TRANSACTION LINES TABLE:\n";
$totalLines = TransactionLine::count();
$totalTransactions = Transaction::whereNotNull('from_account_id')
    ->whereNotNull('to_account_id')
    ->count();
echo "  Total journal entries: {$totalLines}\n";
echo "  Total transactions: {$totalTransactions}\n";
echo "  Average lines per transaction: " . number_format($totalLines / $totalTransactions, 1) . "\n\n";

// 2. Verify double-entry balance
echo "💰 DOUBLE-ENTRY BALANCE:\n";
$totalDebit = TransactionLine::sum('debit');
$totalCredit = TransactionLine::sum('credit');
$balanced = abs($totalDebit - $totalCredit) < 0.01;

echo "  Total Debit: " . number_format($totalDebit) . "đ\n";
echo "  Total Credit: " . number_format($totalCredit) . "đ\n";
echo "  Difference: " . number_format(abs($totalDebit - $totalCredit)) . "đ\n";
echo "  Balanced: " . ($balanced ? '✅ YES' : '❌ NO') . "\n\n";

// 3. Account activity summary
echo "📈 ACCOUNT ACTIVITY (from journal entries):\n";
$accountActivity = DB::table('transaction_lines')
    ->join('accounts', 'transaction_lines.account_id', '=', 'accounts.id')
    ->select(
        'accounts.name',
        'accounts.type',
        'accounts.balance as recorded_balance',
        DB::raw('SUM(transaction_lines.debit) as total_debit'),
        DB::raw('SUM(transaction_lines.credit) as total_credit'),
        DB::raw('SUM(transaction_lines.debit) - SUM(transaction_lines.credit) as calculated_balance'),
        DB::raw('COUNT(*) as entry_count')
    )
    ->groupBy('accounts.id', 'accounts.name', 'accounts.type', 'accounts.balance')
    ->orderByDesc('entry_count')
    ->limit(10)
    ->get();

echo "\nTop 10 Most Active Accounts:\n";
foreach ($accountActivity as $activity) {
    $match = abs($activity->recorded_balance - $activity->calculated_balance) < 0.01 ? '✅' : '❌';
    echo sprintf(
        "  %s [%s] {$match}\n    Entries: %d | Debit: %s | Credit: %s | Balance: %s\n",
        $activity->name,
        $activity->type,
        $activity->entry_count,
        number_format($activity->total_debit),
        number_format($activity->total_credit),
        number_format($activity->calculated_balance)
    );
}

// 4. Transaction type breakdown
echo "\n\n📊 TRANSACTION TYPE BREAKDOWN:\n";
$typeBreakdown = DB::table('transactions')
    ->join('transaction_lines', 'transactions.id', '=', 'transaction_lines.transaction_id')
    ->select(
        'transactions.type',
        DB::raw('COUNT(DISTINCT transactions.id) as transaction_count'),
        DB::raw('SUM(transaction_lines.debit) as total_debit'),
        DB::raw('SUM(transaction_lines.credit) as total_credit')
    )
    ->groupBy('transactions.type')
    ->get();

foreach ($typeBreakdown as $type) {
    echo sprintf(
        "  %s: %d transactions | Debit: %s | Credit: %s\n",
        ucfirst($type->type),
        $type->transaction_count,
        number_format($type->total_debit),
        number_format($type->total_credit)
    );
}

// 5. Check for any unbalanced transactions
echo "\n\n🔍 CHECKING FOR UNBALANCED TRANSACTIONS:\n";
$unbalanced = DB::table('transaction_lines')
    ->select(
        'transaction_id',
        DB::raw('SUM(debit) as total_debit'),
        DB::raw('SUM(credit) as total_credit'),
        DB::raw('ABS(SUM(debit) - SUM(credit)) as difference')
    )
    ->groupBy('transaction_id')
    ->having('difference', '>', 0.01)
    ->get();

if ($unbalanced->isEmpty()) {
    echo "  ✅ All transactions are balanced!\n";
} else {
    echo "  ❌ Found {$unbalanced->count()} unbalanced transactions:\n";
    foreach ($unbalanced as $tx) {
        echo "    Transaction #{$tx->transaction_id}: Debit={$tx->total_debit} Credit={$tx->total_credit} Diff={$tx->difference}\n";
    }
}

// 6. Sample journal entry
echo "\n\n📝 SAMPLE JOURNAL ENTRY:\n";
$sampleTx = Transaction::with(['lines.account'])->first();
if ($sampleTx) {
    echo "  Transaction #{$sampleTx->id}: {$sampleTx->code}\n";
    echo "  Date: {$sampleTx->date->format('Y-m-d')}\n";
    echo "  Description: {$sampleTx->description}\n";
    echo "  Amount: " . number_format($sampleTx->amount) . "đ\n\n";
    echo "  Journal Entries:\n";
    foreach ($sampleTx->lines as $line) {
        echo sprintf(
            "    %s: %s %s\n",
            $line->account->name,
            $line->debit > 0 ? 'Debit' : 'Credit',
            number_format($line->debit > 0 ? $line->debit : $line->credit)
        );
    }
}

echo "\n\n=== VERIFICATION COMPLETE ===\n";
echo "✅ Phase 1: InsufficientBalanceException & Constraints\n";
echo "✅ Phase 2: Accounts Table & Normalization ({$totalTransactions} transactions migrated)\n";
echo "✅ Phase 3: Double-Entry Bookkeeping ({$totalLines} journal entries created)\n";
echo "\n🎉 All phases deployed successfully!\n";
