<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Services\TransactionLifecycleService;

class ReverseTransaction extends Command
{
    protected $signature = 'transaction:reverse 
                            {code : Transaction code to reverse}
                            {reason : Reason for reversal}
                            {--preview : Preview without making changes}';
    
    protected $description = 'Reverse a transaction by creating an opposing transaction';

    public function handle()
    {
        $code = $this->argument('code');
        $reason = $this->argument('reason');
        $preview = $this->option('preview');
        
        $this->info("🔍 Finding transaction {$code}...");
        
        $transaction = Transaction::where('code', $code)->first();
        
        if (!$transaction) {
            $this->error("❌ Transaction {$code} not found!");
            return Command::FAILURE;
        }
        
        // Display transaction info
        $this->newLine();
        $this->info("📊 Transaction Details:");
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $transaction->id],
                ['Code', $transaction->code],
                ['Type', strtoupper($transaction->type)],
                ['Amount', number_format($transaction->amount) . 'đ'],
                ['Date', $transaction->date->format('d/m/Y')],
                ['From', $transaction->fromAccount->name],
                ['To', $transaction->toAccount->name],
                ['Status', $transaction->lifecycle_status ?? 'N/A'],
                ['Locked', $transaction->is_locked ? 'Yes' : 'No'],
            ]
        );
        
        // Check if can be reversed
        if ($transaction->lifecycle_status === 'reversed') {
            $this->error("❌ This transaction has already been reversed!");
            $reversalTx = Transaction::find($transaction->reversed_by_transaction_id);
            if ($reversalTx) {
                $this->warn("Reversal transaction: {$reversalTx->code}");
            }
            return Command::FAILURE;
        }
        
        if ($transaction->is_locked) {
            $this->error("❌ This transaction is locked and cannot be reversed!");
            return Command::FAILURE;
        }
        
        if ($preview) {
            $this->newLine();
            $this->warn("👁️  PREVIEW MODE - No changes will be made");
            $this->newLine();
            
            $this->info("📝 Reversal Transaction Preview:");
            $this->table(
                ['Field', 'Original', 'Reversal'],
                [
                    ['Type', $transaction->type, $transaction->type === 'thu' ? 'chi' : 'thu'],
                    ['From Account', $transaction->fromAccount->name, $transaction->toAccount->name],
                    ['To Account', $transaction->toAccount->name, $transaction->fromAccount->name],
                    ['Amount', number_format($transaction->amount), number_format($transaction->amount)],
                    ['Note', '', "REVERSAL: {$code} - {$reason}"],
                ]
            );
            
            return Command::SUCCESS;
        }
        
        $this->newLine();
        if (!$this->confirm("⚠️  Do you want to reverse this transaction?")) {
            $this->info("Cancelled.");
            return Command::SUCCESS;
        }
        
        try {
            $this->info("🔄 Creating reversal transaction...");
            
            $lifecycleService = new TransactionLifecycleService();
            $reversalTransaction = $lifecycleService->reverseTransaction($transaction, $reason);
            
            $this->newLine();
            $this->info("✅ Successfully created reversal transaction!");
            $this->newLine();
            
            $this->table(
                ['Field', 'Value'],
                [
                    ['Reversal Code', $reversalTransaction->code],
                    ['Reversal ID', $reversalTransaction->id],
                    ['Original Code', $transaction->code],
                    ['Original Status', 'reversed'],
                    ['Reason', $reason],
                ]
            );
            
            $this->newLine();
            $this->info("💡 Next steps:");
            $this->line("  1. Verify balances: php artisan accounts:reconcile --all");
            $this->line("  2. Check journal entries for both transactions");
            $this->line("  3. Original transaction is marked as 'reversed' but still visible");
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
