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
            
            // Sync incident_staff.wage_amount if this is a wage transaction
            if ($transaction->incident_id) {
                $this->syncIncidentStaffWage($transaction->incident_id, $transaction->staff_id);
            }
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
     * Sync incident_staff.wage_amount with actual transaction total
     * This keeps pivot table in sync when transactions are created/updated/deleted
     */
    private function syncIncidentStaffWage($incidentId, $staffId): void
    {
        // Calculate actual wage from all transactions for this staff in this incident
        $actualWage = Transaction::where('incident_id', $incidentId)
            ->where('staff_id', $staffId)
            ->where('type', 'chi')
            ->sum('amount');
        
        // Update incident_staff pivot table
        \DB::table('incident_staff')
            ->where('incident_id', $incidentId)
            ->where('staff_id', $staffId)
            ->update([
                'wage_amount' => $actualWage,
                'updated_at' => now()
            ]);
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        // Sync incident_staff.wage_amount if this is a wage transaction
        if ($transaction->type === 'chi' && $transaction->staff_id && $transaction->incident_id) {
            $this->syncIncidentStaffWage($transaction->incident_id, $transaction->staff_id);
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        // Sync incident_staff.wage_amount if this is a wage transaction
        if ($transaction->type === 'chi' && $transaction->staff_id && $transaction->incident_id) {
            $this->syncIncidentStaffWage($transaction->incident_id, $transaction->staff_id);
        }
        
        // Handle deletion of salary advance debt transactions
        if ($transaction->category === 'ứng_lương_nợ') {
            \Log::warning('Salary advance debt transaction deleted directly', [
                'transaction_id' => $transaction->id,
                'staff_id' => $transaction->staff_id,
                'amount' => $transaction->amount,
            ]);
            // Don't auto-delete salary advance as it's managed in controller
        }
        
        // Handle deletion of adjustment transactions (điều_chỉnh_lương)
        if ($transaction->category === 'điều_chỉnh_lương') {
            // Find the adjustment that contains this transaction ID
            $adjustment = StaffAdjustment::whereJsonContains('transaction_ids', $transaction->id)->first();
            
            if ($adjustment) {
                \Log::info('Deleting staff adjustment due to transaction deletion', [
                    'adjustment_id' => $adjustment->id,
                    'transaction_id' => $transaction->id,
                    'staff_id' => $transaction->staff_id,
                ]);
                
                // Delete other transactions linked to this adjustment
                $otherTransactionIds = array_diff($adjustment->transaction_ids ?? [], [$transaction->id]);
                if (!empty($otherTransactionIds)) {
                    Transaction::whereIn('id', $otherTransactionIds)->delete();
                }
                
                // Delete the adjustment record
                $adjustment->delete();
            }
        }
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
