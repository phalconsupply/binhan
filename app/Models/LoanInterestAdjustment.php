<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanInterestAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'old_interest_rate',
        'new_interest_rate',
        'effective_date',
        'note',
        'created_by',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'old_interest_rate' => 'decimal:2',
        'new_interest_rate' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function loan()
    {
        return $this->belongsTo(LoanProfile::class, 'loan_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
