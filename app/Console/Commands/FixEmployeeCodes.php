<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Staff;

class FixEmployeeCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'staff:fix-employee-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix employee codes for existing staff (assign sequential codes like NV001, NV002, etc.)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fix employee codes...');
        
        // Get all staff ordered by ID (creation order)
        $staffList = Staff::orderBy('id')->get();
        
        if ($staffList->isEmpty()) {
            $this->warn('No staff records found.');
            return 0;
        }
        
        $this->info("Found {$staffList->count()} staff records.");
        
        $counter = 1;
        $updated = 0;
        
        foreach ($staffList as $staff) {
            $newCode = 'NV' . str_pad($counter, 3, '0', STR_PAD_LEFT);
            
            if ($staff->employee_code !== $newCode) {
                $oldCode = $staff->employee_code ?? '(null)';
                $staff->employee_code = $newCode;
                $staff->save();
                
                $this->line("Updated: {$staff->full_name} | {$oldCode} → {$newCode}");
                $updated++;
            } else {
                $this->line("Skipped: {$staff->full_name} | Already has correct code: {$newCode}");
            }
            
            $counter++;
        }
        
        $this->newLine();
        $this->info("✓ Done! Updated {$updated} staff records.");
        
        return 0;
    }
}
