<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class MigrateTransactionsToAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:migrate-transactions
                            {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate string-based account names to foreign keys';

    protected $accountMap = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info('🔄 Starting transaction migration to accounts table...');
        $this->newLine();

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Build account mapping
        $this->buildAccountMap();

        // Get all transactions
        $transactions = Transaction::whereNotNull('from_account')
            ->orWhereNotNull('to_account')
            ->get();

        $this->info("Found {$transactions->count()} transactions to migrate");
        $this->newLine();

        $progressBar = $this->output->createProgressBar($transactions->count());
        $progressBar->start();

        $migrated = 0;
        $failed = 0;
        $errors = [];

        foreach ($transactions as $transaction) {
            try {
                if (!$dryRun) {
                    DB::transaction(function () use ($transaction) {
                        $this->migrateTransaction($transaction);
                    });
                } else {
                    $this->migrateTransaction($transaction, true);
                }
                $migrated++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage(),
                ];
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display results
        $this->info("✅ Successfully migrated: {$migrated}");
        if ($failed > 0) {
            $this->error("❌ Failed: {$failed}");
            $this->newLine();
            $this->table(['Transaction ID', 'Error'], $errors);
        }

        if (!$dryRun) {
            $this->info('Migration completed!');
            $this->newLine();
            $this->warn('⚠️  Next steps:');
            $this->line('1. Verify the migration: php artisan accounts:reconcile --all');
            $this->line('2. Test the application thoroughly');
            $this->line('3. If everything works, you can drop old string columns in a future migration');
        } else {
            $this->info('Dry run completed. Run without --dry-run to apply changes.');
        }

        return Command::SUCCESS;
    }

    /**
     * Build mapping from string account names to Account IDs
     */
    protected function buildAccountMap(): void
    {
        $this->info('Building account mapping...');

        $accounts = Account::all();

        foreach ($accounts as $account) {
            // Map by category + reference
            if ($account->reference_id) {
                $key = strtolower("{$account->category}_{$account->reference_id}");
                $this->accountMap[$key] = $account->id;
            }

            // Map by category only
            $this->accountMap[strtolower($account->category)] = $account->id;

            // Special mappings for backward compatibility
            if ($account->category === 'company_fund') {
                $this->accountMap['company_fund'] = $account->id;
            }
            if ($account->category === 'company_reserved') {
                $this->accountMap['company_reserved'] = $account->id;
            }
            if ($account->category === 'customer') {
                $this->accountMap['customer'] = $account->id;
            }
            if ($account->category === 'income') {
                $this->accountMap['income'] = $account->id;
            }
            if ($account->category === 'external') {
                $this->accountMap['external'] = $account->id;
            }
            if ($account->category === 'partner') {
                $this->accountMap['partner'] = $account->id;
            }
        }

        $this->info('Account mapping built: ' . count($this->accountMap) . ' entries');
        $this->newLine();
    }

    /**
     * Migrate a single transaction
     */
    protected function migrateTransaction(Transaction $transaction, bool $dryRun = false): void
    {
        $fromAccountId = null;
        $toAccountId = null;

        // Map from_account
        if ($transaction->from_account) {
            $fromAccountId = $this->resolveAccountId($transaction->from_account);
            if (!$fromAccountId) {
                throw new \Exception("Could not resolve from_account: {$transaction->from_account}");
            }
        }

        // Map to_account
        if ($transaction->to_account) {
            $toAccountId = $this->resolveAccountId($transaction->to_account);
            if (!$toAccountId) {
                throw new \Exception("Could not resolve to_account: {$transaction->to_account}");
            }
        }

        if (!$dryRun) {
            $transaction->updateQuietly([
                'from_account_id' => $fromAccountId,
                'to_account_id' => $toAccountId,
            ]);
        }
    }

    /**
     * Resolve account string to Account ID
     */
    protected function resolveAccountId(string $accountString): ?int
    {
        // Direct match
        $key = strtolower($accountString);
        if (isset($this->accountMap[$key])) {
            return $this->accountMap[$key];
        }

        // Parse vehicle_123 format
        if (preg_match('/^vehicle_(\d+)$/i', $accountString, $matches)) {
            $vehicleId = $matches[1];
            $key = "vehicle_{$vehicleId}";
            return $this->accountMap[$key] ?? null;
        }

        // Parse staff_123 format
        if (preg_match('/^staff_(\d+)$/i', $accountString, $matches)) {
            $staffId = $matches[1];
            $key = "staff_{$staffId}";
            return $this->accountMap[$key] ?? null;
        }

        return null;
    }
}
