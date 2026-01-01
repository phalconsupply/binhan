<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Services\DoubleEntryService;
use Illuminate\Support\Facades\DB;

class GenerateJournalEntries extends Command
{
    protected $signature = 'transactions:generate-journal-entries {--force}';
    protected $description = 'Generate double-entry journal entries for all transactions';

    public function handle()
    {
        $this->info('🔍 Analyzing transactions...');
        
        // Check existing entries
        $existingCount = TransactionLine::count();
        
        if ($existingCount > 0 && !$this->option('force')) {
            $this->warn("Found {$existingCount} existing journal entries.");
            if (!$this->confirm('Do you want to delete and regenerate all entries?')) {
                $this->info('Cancelled.');
                return Command::SUCCESS;
            }
        }
        
        $this->newLine();
        $this->info('📊 Starting journal entry generation...');
        
        DB::beginTransaction();
        
        try {
            // Clear existing entries if force or confirmed
            if ($existingCount > 0) {
                $this->info('Deleting existing entries...');
                TransactionLine::truncate();
            }
            
            // Get all transactions
            $transactions = Transaction::whereNotNull('from_account_id')
                ->whereNotNull('to_account_id')
                ->orderBy('id')
                ->get();
            
            $this->info("Found {$transactions->count()} transactions to process");
            $this->newLine();
            
            $bar = $this->output->createProgressBar($transactions->count());
            $bar->start();
            
            $doubleEntryService = new DoubleEntryService();
            $processed = 0;
            $errors = [];
            
            foreach ($transactions as $transaction) {
                try {
                    $doubleEntryService->createJournalEntries($transaction);
                    $processed++;
                } catch (\Exception $e) {
                    $errors[] = [
                        'transaction_id' => $transaction->id,
                        'error' => $e->getMessage(),
                    ];
                }
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine(2);
            
            if (!empty($errors)) {
                $this->error('❌ Encountered errors:');
                $this->table(['Transaction ID', 'Error'], $errors);
                $this->newLine();
            }
            
            // Verify balance
            $this->info('🔍 Verifying double-entry balance...');
            
            $totalDebit = TransactionLine::sum('debit');
            $totalCredit = TransactionLine::sum('credit');
            $balanced = abs($totalDebit - $totalCredit) < 0.01;
            
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Transactions processed', $processed],
                    ['Journal entries created', TransactionLine::count()],
                    ['Total Debit', number_format($totalDebit, 0, ',', '.') . 'đ'],
                    ['Total Credit', number_format($totalCredit, 0, ',', '.') . 'đ'],
                    ['Balanced', $balanced ? '✅ YES' : '❌ NO'],
                ]
            );
            
            if (!$balanced) {
                throw new \Exception("Journal entries not balanced! Debit: {$totalDebit}, Credit: {$totalCredit}");
            }
            
            DB::commit();
            
            $this->newLine();
            $this->info('✅ Successfully generated journal entries for all transactions!');
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
