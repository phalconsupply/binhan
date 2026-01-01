<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\AccountBalanceService;
use Illuminate\Support\Facades\DB;

class ReconcileAccountBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:reconcile 
                            {account? : Specific account name to reconcile}
                            {--all : Reconcile all accounts}
                            {--fix : Auto-fix discrepancies by recalculating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile account balances and detect discrepancies';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Starting Account Balance Reconciliation...');
        $this->newLine();

        $discrepancies = [];
        $accountsToCheck = [];

        // Determine which accounts to check
        if ($this->option('all')) {
            $accountsToCheck = $this->getAllAccounts();
        } elseif ($this->argument('account')) {
            $accountsToCheck = [$this->argument('account')];
        } else {
            // Default: check main accounts
            $accountsToCheck = ['company_fund', 'company_reserved'];
            
            // Add all vehicle accounts
            $vehicles = \App\Models\Vehicle::all();
            foreach ($vehicles as $vehicle) {
                $accountsToCheck[] = "vehicle_{$vehicle->id}";
            }
        }

        $this->info("Checking " . count($accountsToCheck) . " accounts...");
        $this->newLine();

        $progressBar = $this->output->createProgressBar(count($accountsToCheck));
        $progressBar->start();

        foreach ($accountsToCheck as $accountName) {
            $result = $this->reconcileAccount($accountName);
            
            if (!$result['balanced']) {
                $discrepancies[] = $result;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display results
        if (empty($discrepancies)) {
            $this->info('✅ All accounts are balanced! No discrepancies found.');
            return Command::SUCCESS;
        }

        $this->error('❌ Found ' . count($discrepancies) . ' accounts with discrepancies:');
        $this->newLine();

        $tableData = [];
        foreach ($discrepancies as $disc) {
            $tableData[] = [
                $disc['account'],
                $this->formatCurrency($disc['calculated_balance']),
                $this->formatCurrency($disc['last_recorded_balance']),
                $this->formatCurrency($disc['difference']),
            ];
        }

        $this->table(
            ['Account', 'Calculated Balance', 'Last Recorded', 'Difference'],
            $tableData
        );

        // Auto-fix if requested
        if ($this->option('fix')) {
            $this->newLine();
            if ($this->confirm('Do you want to recalculate and fix these discrepancies?')) {
                $this->info('Recalculating balances...');
                
                foreach ($discrepancies as $disc) {
                    $this->recalculateAccountTransactions($disc['account']);
                }

                $this->info('✅ Recalculation completed!');
                return Command::SUCCESS;
            }
        }

        return Command::FAILURE;
    }

    /**
     * Reconcile a single account
     */
    protected function reconcileAccount(string $accountName): array
    {
        // Find account by code (convert old name to new code)
        $accountCode = $this->convertAccountNameToCode($accountName);
        $account = Account::where('code', $accountCode)->first();
        
        if (!$account) {
            return [
                'account' => $accountName,
                'calculated_balance' => 0,
                'last_recorded_balance' => 0,
                'difference' => 0,
                'balanced' => true,
            ];
        }

        // Calculate balance from all transactions
        $debits = Transaction::where('to_account_id', $account->id)->sum('amount');
        $credits = Transaction::where('from_account_id', $account->id)->sum('amount');
        $calculatedBalance = $debits - $credits;

        // Get the last recorded balance from transactions
        $lastTransaction = Transaction::where(function($q) use ($account) {
            $q->where('from_account_id', $account->id)
              ->orWhere('to_account_id', $account->id);
        })
        ->orderBy('id', 'desc')
        ->first();

        $lastRecordedBalance = null;
        if ($lastTransaction) {
            if ($lastTransaction->from_account_id === $account->id) {
                $lastRecordedBalance = $lastTransaction->from_balance_after;
            } elseif ($lastTransaction->to_account_id === $account->id) {
                $lastRecordedBalance = $lastTransaction->to_balance_after;
            }
        }

        $difference = 0;
        $balanced = true;

        if ($lastRecordedBalance !== null) {
            $difference = abs($calculatedBalance - $lastRecordedBalance);
            $balanced = $difference < 0.01; // Allow 1 cent tolerance for floating point
        }

        return [
            'account' => $accountName,
            'calculated_balance' => $calculatedBalance,
            'last_recorded_balance' => $lastRecordedBalance ?? 0,
            'difference' => $difference,
            'balanced' => $balanced,
        ];
    }

    /**
     * Convert old account name to new account code
     */
    protected function convertAccountNameToCode(string $accountName): string
    {
        $map = [
            'customer' => 'SYS-CUSTOMER',
            'income' => 'SYS-INCOME',
            'external' => 'SYS-EXTERNAL',
            'partner' => 'SYS-PARTNER',
            'company_fund' => 'COMP-FUND',
            'company_reserved' => 'COMP-RESERVED',
        ];

        if (isset($map[$accountName])) {
            return $map[$accountName];
        }

        if (preg_match('/^vehicle_(\d+)$/', $accountName, $matches)) {
            return "VEH-{$matches[1]}";
        }

        if (preg_match('/^staff_(\d+)$/', $accountName, $matches)) {
            return "STAFF-{$matches[1]}";
        }

        return strtoupper($accountName);
    }

    /**
     * Get all unique account names from transactions
     */
    protected function getAllAccounts(): array
    {
        $fromAccounts = Transaction::whereNotNull('from_account')
            ->distinct()
            ->pluck('from_account')
            ->toArray();

        $toAccounts = Transaction::whereNotNull('to_account')
            ->distinct()
            ->pluck('to_account')
            ->toArray();

        return array_unique(array_merge($fromAccounts, $toAccounts));
    }

    /**
     * Recalculate all transactions for an account
     */
    protected function recalculateAccountTransactions(string $accountName): void
    {
        DB::transaction(function () use ($accountName) {
            // Find account by code
            $accountCode = $this->convertAccountNameToCode($accountName);
            $account = Account::where('code', $accountCode)->first();
            
            if (!$account) {
                return;
            }

            // Get all transactions for this account ordered by ID
            $transactions = Transaction::where(function($q) use ($account) {
                $q->where('from_account_id', $account->id)
                  ->orWhere('to_account_id', $account->id);
            })
            ->orderBy('id')
            ->get();

            foreach ($transactions as $transaction) {
                // Skip validation khi recalculate để cho phép số dư âm trong lịch sử
                AccountBalanceService::updateTransactionBalances($transaction, true);
            }
            
            // Update Account.balance to match last transaction
            if ($transactions->isNotEmpty()) {
                $lastTx = $transactions->last();
                $newBalance = $lastTx->from_account_id === $account->id 
                    ? $lastTx->from_balance_after 
                    : $lastTx->to_balance_after;
                
                $account->update(['balance' => $newBalance]);
            }
        });
    }

    /**
     * Format currency for display
     */
    protected function formatCurrency(float $amount): string
    {
        return number_format($amount, 0, ',', '.') . 'đ';
    }
}
