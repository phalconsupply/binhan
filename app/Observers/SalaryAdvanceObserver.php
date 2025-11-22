<?php

namespace App\Observers;

use App\Models\SalaryAdvance;
use App\Models\Transaction;

class SalaryAdvanceObserver
{
    /**
     * Handle the SalaryAdvance "created" event.
     */
    public function created(SalaryAdvance $salaryAdvance): void
    {
        //
    }

    /**
     * Handle the SalaryAdvance "updated" event.
     */
    public function updated(SalaryAdvance $salaryAdvance): void
    {
        //
    }

    /**
     * Handle the SalaryAdvance "deleting" event.
     * Transactions are manually deleted in controller, not here.
     */
    public function deleting(SalaryAdvance $salaryAdvance): void
    {
        // Transactions are manually managed in destroySalaryAdvance() method
        // to ensure proper control flow
    }

    /**
     * Handle the SalaryAdvance "deleted" event.
     */
    public function deleted(SalaryAdvance $salaryAdvance): void
    {
        //
    }

    /**
     * Handle the SalaryAdvance "restored" event.
     */
    public function restored(SalaryAdvance $salaryAdvance): void
    {
        //
    }

    /**
     * Handle the SalaryAdvance "force deleted" event.
     */
    public function forceDeleted(SalaryAdvance $salaryAdvance): void
    {
        //
    }
}
