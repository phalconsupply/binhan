<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class SyncAccountBalancesFromJournal extends Command
{
    protected $signature = 'accounts:sync-from-journal';
    protected $description = 'Sync Account.balance from journal entries (Phase 3)';

    public function handle()
    {
        $this->info('🔄 Syncing account balances from journal entries...');
        $this->newLine();
        
        $accounts = Account::all();
        
        $bar = $this->output->createProgressBar($accounts->count());
        $bar->start();
        
        $updated = 0;
        
        foreach ($accounts as $account) {
            // Calculate balance from journal entries
            $debit = DB::table('transaction_lines')
                ->where('account_id', $account->id)
                ->sum('debit');
            
            $credit = DB::table('transaction_lines')
                ->where('account_id', $account->id)
                ->sum('credit');
            
            $calculatedBalance = $debit - $credit;
            
            if (abs($account->balance - $calculatedBalance) > 0.01) {
                $account->balance = $calculatedBalance;
                $account->save();
                $updated++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("✅ Updated {$updated} account balances");
        
        // Verify
        $this->newLine();
        $this->info('🔍 Verifying balances...');
        
        $mismatches = DB::table('accounts')
            ->leftJoin('transaction_lines', 'accounts.id', '=', 'transaction_lines.account_id')
            ->select(
                'accounts.id',
                'accounts.name',
                'accounts.balance as recorded',
                DB::raw('COALESCE(SUM(transaction_lines.debit), 0) - COALESCE(SUM(transaction_lines.credit), 0) as calculated')
            )
            ->groupBy('accounts.id', 'accounts.name', 'accounts.balance')
            ->havingRaw('ABS(accounts.balance - (COALESCE(SUM(transaction_lines.debit), 0) - COALESCE(SUM(transaction_lines.credit), 0))) > 0.01')
            ->get();
        
        if ($mismatches->isEmpty()) {
            $this->info('✅ All account balances match journal entries!');
        } else {
            $this->warn("Found {$mismatches->count()} mismatches:");
            $this->table(
                ['Account', 'Recorded', 'Calculated'],
                $mismatches->map(fn($m) => [
                    $m->name,
                    number_format($m->recorded, 0, ',', '.'),
                    number_format($m->calculated, 0, ',', '.'),
                ])
            );
        }
        
        return Command::SUCCESS;
    }
}
