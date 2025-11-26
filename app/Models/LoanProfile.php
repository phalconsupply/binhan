<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoanProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'cif',
        'contract_number',
        'bank_name',
        'principal_amount',
        'term_months',
        'total_periods',
        'disbursement_date',
        'base_interest_rate',
        'payment_day',
        'status',
        'remaining_balance',
        'note',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'disbursement_date' => 'date',
        'principal_amount' => 'decimal:2',
        'base_interest_rate' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'payment_day' => 'integer',
        'term_months' => 'integer',
        'total_periods' => 'integer',
    ];

    /**
     * Relationships
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function schedules()
    {
        return $this->hasMany(LoanRepaymentSchedule::class, 'loan_id');
    }

    public function interestAdjustments()
    {
        return $this->hasMany(LoanInterestAdjustment::class, 'loan_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePaidOff($query)
    {
        return $query->where('status', 'paid_off');
    }

    /**
     * Get current interest rate (considering adjustments)
     */
    public function getCurrentInterestRate()
    {
        $latestAdjustment = $this->interestAdjustments()
            ->where('effective_date', '<=', now())
            ->orderBy('effective_date', 'desc')
            ->first();

        return $latestAdjustment ? $latestAdjustment->new_interest_rate : $this->base_interest_rate;
    }

    /**
     * Calculate monthly principal payment
     */
    public function getMonthlyPrincipal()
    {
        return $this->principal_amount / $this->total_periods;
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentage()
    {
        $paidSchedules = $this->schedules()->where('status', 'paid')->count();
        return ($paidSchedules / $this->total_periods) * 100;
    }

    /**
     * Get total paid amount
     */
    public function getTotalPaidAmount()
    {
        return $this->schedules()->where('status', 'paid')->sum('paid_amount');
    }

    /**
     * Get overdue schedules count
     */
    public function getOverdueCount()
    {
        return $this->schedules()->where('status', 'overdue')->count();
    }

    /**
     * Generate repayment schedule
     */
    public function generateRepaymentSchedule()
    {
        // Delete existing schedules if any
        $this->schedules()->delete();

        $monthlyPrincipal = $this->getMonthlyPrincipal();
        $remainingBalance = $this->principal_amount;
        $disbursementDate = Carbon::parse($this->disbursement_date);

        for ($period = 1; $period <= $this->total_periods; $period++) {
            // Calculate due date
            if ($period == 1) {
                // First period: Check if payment day is in the same month as disbursement
                $firstDueDate = $disbursementDate->copy()->day($this->payment_day);
                
                // If payment day is before or equal to disbursement day, move to next month
                if ($firstDueDate->lte($disbursementDate)) {
                    $firstDueDate->addMonth();
                }
                
                $dueDate = $firstDueDate;
            } else {
                // Subsequent periods: Add one month to previous due date
                $dueDate = $previousDueDate->copy()->addMonth();
            }
            
            // Get applicable interest rate for this period
            $interestRate = $this->getInterestRateForDate($dueDate);
            
            // Calculate interest based on actual days
            if ($period == 1) {
                // First period: Calculate interest based on actual days from disbursement to first due date
                $daysInFirstPeriod = $disbursementDate->diffInDays($dueDate);
                $interest = $remainingBalance * ($interestRate / 100) * ($daysInFirstPeriod / 365);
            } else {
                // Subsequent periods: Calculate interest for full month (30/360 or actual/365)
                // Using actual/365 method for consistency
                $daysInPeriod = $previousDueDate->diffInDays($dueDate);
                $interest = $remainingBalance * ($interestRate / 100) * ($daysInPeriod / 365);
            }
            
            // Principal for this period
            $principal = $monthlyPrincipal;
            
            // Total payment
            $total = $principal + $interest;

            // Create schedule entry
            LoanRepaymentSchedule::create([
                'loan_id' => $this->id,
                'period_no' => $period,
                'due_date' => $dueDate,
                'principal' => $principal,
                'interest' => $interest,
                'total' => $total,
                'interest_rate' => $interestRate,
                'status' => 'pending',
            ]);

            // Update remaining balance and previous due date
            $remainingBalance -= $principal;
            $previousDueDate = $dueDate;
        }

        // Update remaining balance
        $this->update(['remaining_balance' => $this->principal_amount]);
    }

    /**
     * Get interest rate applicable for a specific date
     */
    public function getInterestRateForDate($date)
    {
        $adjustment = $this->interestAdjustments()
            ->where('effective_date', '<=', $date)
            ->orderBy('effective_date', 'desc')
            ->first();

        return $adjustment ? $adjustment->new_interest_rate : $this->base_interest_rate;
    }

    /**
     * Mark as paid off
     */
    public function markAsPaidOff()
    {
        $this->update([
            'status' => 'paid_off',
            'remaining_balance' => 0,
        ]);

        // Mark all pending schedules as cancelled
        $this->schedules()->where('status', 'pending')->delete();
    }
}
