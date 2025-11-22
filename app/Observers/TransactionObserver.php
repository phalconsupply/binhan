<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Models\StaffAdjustment;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        // Only process for wage payments (chi) with staff_id
        if ($transaction->type === 'chi' && $transaction->staff_id) {
            $this->processDebtPayment($transaction->staff_id);
        }
    }

    /**
     * Process debt payment when staff receives new income
     */
    private function processDebtPayment($staffId): void
    {
        // Get all pending debts ordered by oldest first
        $debts = StaffAdjustment::where('staff_id', $staffId)
            ->where('status', 'debt')
            ->where('debt_amount', '>', 0)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($debts->isEmpty()) {
            return;
        }

        // Calculate available balance from current month
        $currentMonth = now()->startOfMonth();
        
        $staff = \App\Models\Staff::find($staffId);
        if (!$staff) {
            return;
        }

        $baseSalary = $staff->base_salary ?? 0;
        $wageEarnings = Transaction::where('staff_id', $staffId)
            ->where('type', 'chi')
            ->whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->sum('amount');

        $additions = StaffAdjustment::where('staff_id', $staffId)
            ->where('type', 'addition')
            ->where('status', 'applied')
            ->whereYear('month', $currentMonth->year)
            ->whereMonth('month', $currentMonth->month)
            ->sum('amount');

        $appliedDeductions = StaffAdjustment::where('staff_id', $staffId)
            ->where('type', 'deduction')
            ->where('status', 'applied')
            ->whereYear('month', $currentMonth->year)
            ->whereMonth('month', $currentMonth->month)
            ->sum('amount');

        $availableBalance = $baseSalary + $wageEarnings + $additions - $appliedDeductions;

        // Pay off debts with available balance
        foreach ($debts as $debt) {
            if ($availableBalance <= 0) {
                break;
            }

            if ($availableBalance >= $debt->debt_amount) {
                // Can pay off entire debt
                $availableBalance -= $debt->debt_amount;
                $debt->update([
                    'status' => 'applied',
                    'debt_amount' => 0,
                    'applied_at' => now(),
                ]);
            } else {
                // Partial payment
                $debt->update([
                    'debt_amount' => $debt->debt_amount - $availableBalance,
                ]);
                $availableBalance = 0;
            }
        }
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }
}
