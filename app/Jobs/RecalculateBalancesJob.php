<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\AccountBalanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RecalculateBalancesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 3;

    protected $fromDate;
    protected $recalculateAll;

    /**
     * Create a new job instance.
     */
    public function __construct($fromDate = null, $recalculateAll = false)
    {
        $this->fromDate = $fromDate ? Carbon::parse($fromDate) : null;
        $this->recalculateAll = $recalculateAll;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('RecalculateBalancesJob started', [
                'from_date' => $this->fromDate?->toDateString(),
                'recalculate_all' => $this->recalculateAll,
            ]);

            if ($this->recalculateAll) {
                // Tính lại tất cả
                AccountBalanceService::recalculateAllBalances();
                Log::info('Recalculated all balances successfully');
            } elseif ($this->fromDate) {
                // Tính lại từ ngày cụ thể
                AccountBalanceService::recalculateBalancesFromDate($this->fromDate);
                Log::info('Recalculated balances from date successfully', [
                    'from_date' => $this->fromDate->toDateString(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('RecalculateBalancesJob failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            throw $e;
        }
    }
}
