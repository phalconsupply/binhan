<?php

namespace App\Console\Commands;

use App\Models\LoanProfile;
use App\Models\LoanRepaymentSchedule;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessLoanRepayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:process-repayments {--dry-run : Run without making actual changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process due loan repayments automatically by deducting from vehicle profit';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('Running in DRY-RUN mode - no changes will be made');
        }

        $today = now()->format('Y-m-d');
        
        // Get all schedules due today that are still pending
        $dueSchedules = LoanRepaymentSchedule::with(['loan.vehicle'])
            ->where('due_date', $today)
            ->where('status', 'pending')
            ->get();

        if ($dueSchedules->isEmpty()) {
            $this->info('No loan repayments due today.');
            return 0;
        }

        $this->info("Found {$dueSchedules->count()} repayments due today");
        
        $processedCount = 0;
        $failedCount = 0;
        $insufficientFundsCount = 0;

        foreach ($dueSchedules as $schedule) {
            $loan = $schedule->loan;
            $vehicle = $loan->vehicle;

            $this->line("Processing: Xe {$vehicle->license_plate} - Kỳ {$schedule->period_no}/{$loan->total_periods} - Số tiền: " . number_format($schedule->total, 0, ',', '.') . 'đ');

            try {
                if (!$isDryRun) {
                    DB::beginTransaction();
                }

                // Calculate vehicle's current profit (profit transactions minus expenses)
                $vehicleProfit = Transaction::where('vehicle_id', $vehicle->id)
                    ->where('type', 'thu')
                    ->where('category', 'lợi_nhuận')
                    ->sum('amount');

                $vehicleExpenses = Transaction::where('vehicle_id', $vehicle->id)
                    ->whereIn('type', ['chi'])
                    ->whereNotIn('category', ['lợi_nhuận']) // Exclude profit distributions
                    ->sum('amount');

                $currentBalance = $vehicleProfit - $vehicleExpenses;

                $this->line("  - Lợi nhuận khả dụng: " . number_format($currentBalance, 0, ',', '.') . 'đ');

                // Create principal transaction
                $principalTransaction = null;
                if (!$isDryRun) {
                    $principalTransaction = Transaction::create([
                        'vehicle_id' => $vehicle->id,
                        'type' => 'chi',
                        'category' => 'trả_nợ_gốc',
                        'amount' => $schedule->principal,
                        'method' => 'other',
                        'recorded_by' => 1, // System user
                        'date' => $today,
                        'note' => "Tự động trả nợ gốc kỳ {$schedule->period_no}/{$loan->total_periods} - {$loan->bank_name}",
                    ]);
                }

                // Create interest transaction
                $interestTransaction = null;
                if (!$isDryRun) {
                    $interestTransaction = Transaction::create([
                        'vehicle_id' => $vehicle->id,
                        'type' => 'chi',
                        'category' => 'trả_nợ_lãi',
                        'amount' => $schedule->interest,
                        'method' => 'other',
                        'recorded_by' => 1, // System user
                        'date' => $today,
                        'note' => "Tự động trả lãi kỳ {$schedule->period_no}/{$loan->total_periods} - {$loan->bank_name} (lãi suất {$schedule->interest_rate}%)",
                    ]);
                }

                // Mark schedule as paid
                if (!$isDryRun) {
                    $schedule->markAsPaid($principalTransaction->id ?? null);
                }

                // Check if insufficient funds
                if ($currentBalance < $schedule->total) {
                    $insufficientFundsCount++;
                    $this->warn("  ⚠ Không đủ tiền! Lợi nhuận sẽ âm: " . number_format($currentBalance - $schedule->total, 0, ',', '.') . 'đ');
                } else {
                    $this->info("  ✓ Đã xử lý thành công");
                }

                if (!$isDryRun) {
                    DB::commit();
                }
                
                $processedCount++;

            } catch (\Exception $e) {
                if (!$isDryRun) {
                    DB::rollBack();
                }
                
                $failedCount++;
                $this->error("  ✗ Lỗi: {$e->getMessage()}");
                
                Log::error('Failed to process loan repayment', [
                    'schedule_id' => $schedule->id,
                    'loan_id' => $loan->id,
                    'vehicle_id' => $vehicle->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Summary
        $this->newLine();
        $this->info("=== Tổng kết ===");
        $this->info("Đã xử lý: {$processedCount}");
        if ($insufficientFundsCount > 0) {
            $this->warn("Không đủ tiền: {$insufficientFundsCount}");
        }
        if ($failedCount > 0) {
            $this->error("Thất bại: {$failedCount}");
        }

        if ($isDryRun) {
            $this->newLine();
            $this->warn('Chạy thử nghiệm - không có thay đổi nào được lưu');
        }

        return 0;
    }
}
