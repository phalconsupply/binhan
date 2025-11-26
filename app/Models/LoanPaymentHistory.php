<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanPaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'loan_payment_history';

    protected $fillable = [
        'transaction_id',
        'loan_id',
        'payment_type',
        'amount',
        'schedules_snapshot',
        'previous_remaining_balance',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'previous_remaining_balance' => 'decimal:2',
        'schedules_snapshot' => 'array',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function loan()
    {
        return $this->belongsTo(LoanProfile::class, 'loan_id');
    }
}
