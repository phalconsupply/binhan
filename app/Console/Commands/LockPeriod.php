<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Services\TransactionLifecycleService;

class LockPeriod extends Command
{
    protected $signature = 'period:lock 
                            {start-date : Start date (YYYY-MM-DD)}
                            {end-date : End date (YYYY-MM-DD)}
                            {reason : Reason for locking period}';
    
    protected $description = 'Lock all transactions in a period to prevent modifications';

    public function handle()
    {
        $startDate = new \DateTime($this->argument('start-date'));
        $endDate = new \DateTime($this->argument('end-date'));
        $reason = $this->argument('reason');
        
        $this->info("🔍 Analyzing period: {$startDate->format('d/m/Y')} - {$endDate->format('d/m/Y')}");
        
        $count = Transaction::whereBetween('date', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->where('is_locked', false)
            ->count();
        
        if ($count === 0) {
            $this->warn("No unlocked transactions found in this period.");
            return Command::SUCCESS;
        }
        
        $this->newLine();
        $this->info("Found {$count} transactions to lock.");
        
        if (!$this->confirm("⚠️  Do you want to lock these transactions?")) {
            $this->info("Cancelled.");
            return Command::SUCCESS;
        }
        
        try {
            $lifecycleService = new TransactionLifecycleService();
            $locked = $lifecycleService->lockPeriod($startDate, $endDate, $reason);
            
            $this->newLine();
            $this->info("✅ Successfully locked {$locked} transactions!");
            $this->warn("⚠️  These transactions can no longer be modified or deleted.");
            $this->line("Use 'transaction:unlock {code}' to unlock individual transactions if needed.");
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
