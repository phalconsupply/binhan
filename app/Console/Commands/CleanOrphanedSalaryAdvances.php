<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SalaryAdvance;
use App\Models\Transaction;

class CleanOrphanedSalaryAdvances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'salary-advances:clean-orphaned';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up salary advances that have no valid transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for orphaned salary advances...');

        $salaryAdvances = SalaryAdvance::all();
        $deleted = 0;

        foreach ($salaryAdvances as $advance) {
            if (!$advance->transaction_ids || empty($advance->transaction_ids)) {
                $this->warn("Salary advance #{$advance->id} has no transaction_ids. Deleting...");
                $advance->delete();
                $deleted++;
                continue;
            }

            // Check if any of the transactions still exist
            $existingTransactions = Transaction::whereIn('id', $advance->transaction_ids)->count();
            
            if ($existingTransactions === 0) {
                $this->warn("Salary advance #{$advance->id} has no existing transactions. Deleting...");
                $advance->delete();
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("Deleted {$deleted} orphaned salary advance(s).");
        } else {
            $this->info('No orphaned salary advances found.');
        }

        return 0;
    }
}
