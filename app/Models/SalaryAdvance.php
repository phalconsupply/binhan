<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'amount',
        'from_earnings',
        'from_company',
        'debt_amount',
        'status',
        'note',
        'transaction_ids',
        'approved_by',
        'approved_at',
        'date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'from_earnings' => 'decimal:2',
        'from_company' => 'decimal:2',
        'debt_amount' => 'decimal:2',
        'transaction_ids' => 'array',
        'approved_at' => 'datetime',
        'date' => 'datetime',
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function transactions()
    {
        if (!$this->transaction_ids) {
            return collect();
        }
        return Transaction::whereIn('id', $this->transaction_ids)->get();
    }

    // Scopes
    public function scopeForMonth($query, $month)
    {
        return $query->whereYear('date', $month->year)
            ->whereMonth('date', $month->month);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeWithDebt($query)
    {
        return $query->where('debt_amount', '>', 0);
    }

    public function scopeDebt($query)
    {
        return $query->where('debt_amount', '>', 0);
    }
}
