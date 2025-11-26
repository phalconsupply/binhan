<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoanRepaymentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'period_no',
        'due_date',
        'principal',
        'interest',
        'total',
        'interest_rate',
        'status',
        'paid_date',
        'paid_amount',
        'overdue_days',
        'late_fee',
        'transaction_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'paid_date' => 'date',
        'principal' => 'decimal:2',
        'interest' => 'decimal:2',
        'total' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'period_no' => 'integer',
        'overdue_days' => 'integer',
    ];

    /**
     * Relationships
     */
    public function loan()
    {
        return $this->belongsTo(LoanProfile::class, 'loan_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopeDueToday($query)
    {
        return $query->where('due_date', today())->where('status', 'pending');
    }

    public function scopeDueSoon($query, $days = 7)
    {
        return $query->where('due_date', '<=', today()->addDays($days))
            ->where('due_date', '>=', today())
            ->where('status', 'pending');
    }

    /**
     * Check if payment is overdue
     */
    public function isOverdue()
    {
        return $this->status === 'pending' && $this->due_date < today();
    }

    /**
     * Calculate overdue days
     */
    public function calculateOverdueDays()
    {
        if ($this->isOverdue()) {
            return today()->diffInDays($this->due_date);
        }
        return 0;
    }

    /**
     * Mark as paid
     */
    public function markAsPaid($transactionId = null, $paidAmount = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => today(),
            'paid_amount' => $paidAmount ?? $this->total,
            'transaction_id' => $transactionId,
            'overdue_days' => $this->calculateOverdueDays(),
        ]);

        // Update loan remaining balance
        $this->loan->decrement('remaining_balance', $this->principal);
    }

    /**
     * Mark as overdue
     */
    public function markAsOverdue()
    {
        $this->update([
            'status' => 'overdue',
            'overdue_days' => $this->calculateOverdueDays(),
        ]);
    }
}
