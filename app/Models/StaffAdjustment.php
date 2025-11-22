<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'created_by',
        'incident_id',
        'type',
        'amount',
        'month',
        'category',
        'reason',
        'status',
        'debt_amount',
        'applied_at',
        'transaction_ids',
        'from_incident_amount',
        'from_company_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'debt_amount' => 'decimal:2',
        'from_incident_amount' => 'decimal:2',
        'from_company_amount' => 'decimal:2',
        'month' => 'date',
        'applied_at' => 'datetime',
        'transaction_ids' => 'array',
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    // Accessors
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'addition' => 'Cộng tiền',
            'deduction' => 'Trừ tiền',
            default => $this->type,
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'Chờ xử lý',
            'applied' => 'Đã áp dụng',
            'debt' => 'Nợ',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'applied' => 'green',
            'debt' => 'red',
            default => 'gray',
        };
    }

    // Scopes
    public function scopeForMonth($query, $month)
    {
        return $query->whereYear('month', '=', $month->year)
                    ->whereMonth('month', '=', $month->month);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDebt($query)
    {
        return $query->where('status', 'debt')->where('debt_amount', '>', 0);
    }
}
