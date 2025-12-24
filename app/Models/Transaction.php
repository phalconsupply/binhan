<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Transaction extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'code',
        'incident_id',
        'vehicle_id',
        'vehicle_maintenance_id',
        'staff_id',
        'type',
        'category',
        'transaction_category',
        'amount',
        'method',
        'payment_method',
        'note',
        'recorded_by',
        'date',
        'is_active',
        'replaced_by',
        'edited_at',
        'edited_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
        'edited_at' => 'datetime',
    ];

    /**
     * Boot method to add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate transaction code
        static::creating(function ($transaction) {
            if (empty($transaction->code)) {
                // Get the last transaction ID to ensure uniqueness
                $lastId = static::max('id') ?? 0;
                $nextId = $lastId + 1;
                $date = $transaction->date ? $transaction->date->format('Ymd') : now()->format('Ymd');
                $transaction->code = "GD{$date}-" . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }
        });

        // When a loan payment transaction is deleted, reverse the loan schedule
        static::deleting(function ($transaction) {
            if (in_array($transaction->category, ['trả_nợ_gốc', 'trả_nợ_lãi', 'trả_nợ_sớm'])) {
                static::reverseLoanPayment($transaction);
            }
        });
    }

    /**
     * Reverse loan payment when transaction is deleted
     */
    protected static function reverseLoanPayment($transaction)
    {
        try {
            $vehicle = $transaction->vehicle;
            if (!$vehicle || !$vehicle->loanProfile) {
                return;
            }

            $loan = $vehicle->loanProfile;

            // Check if there's a payment history record
            $history = LoanPaymentHistory::where('transaction_id', $transaction->id)->first();

            if ($history) {
                // Restore from snapshot
                if ($history->payment_type === 'partial_prepayment') {
                    // Restore remaining balance
                    $loan->remaining_balance = $history->previous_remaining_balance;
                    $loan->save();

                    // Restore schedules from snapshot
                    foreach ($history->schedules_snapshot as $snapshotSchedule) {
                        $schedule = $loan->schedules()->find($snapshotSchedule['id']);
                        if ($schedule) {
                            $schedule->update([
                                'principal' => $snapshotSchedule['principal'],
                                'total' => $snapshotSchedule['total'],
                            ]);
                        }
                    }

                    Log::info('Reversed partial prepayment from snapshot', [
                        'transaction_id' => $transaction->id,
                        'restored_balance' => $history->previous_remaining_balance,
                    ]);
                } elseif ($history->payment_type === 'full_payoff') {
                    // Reactivate loan
                    $loan->update(['status' => 'active', 'remaining_balance' => $history->previous_remaining_balance]);

                    // Restore all schedules
                    foreach ($history->schedules_snapshot as $snapshotSchedule) {
                        $schedule = $loan->schedules()->find($snapshotSchedule['id']);
                        if ($schedule) {
                            $schedule->update([
                                'status' => $snapshotSchedule['status'],
                                'principal' => $snapshotSchedule['principal'],
                                'total' => $snapshotSchedule['total'],
                            ]);
                        }
                    }

                    Log::info('Reversed full payoff from snapshot', [
                        'transaction_id' => $transaction->id,
                        'loan_id' => $loan->id,
                    ]);
                }

                // Delete history record
                $history->delete();
            } else {
                // Fallback: Find the schedule that was paid with this transaction
                $schedule = $loan->schedules()
                    ->where('transaction_id', $transaction->id)
                    ->first();

                if ($schedule) {
                    // Reset schedule to pending
                    $schedule->update([
                        'status' => 'pending',
                        'paid_date' => null,
                        'paid_amount' => null,
                        'transaction_id' => null,
                    ]);

                    // Restore remaining balance if principal was paid
                    if ($transaction->category === 'trả_nợ_gốc' && $transaction->amount > 0) {
                        $loan->remaining_balance += $transaction->amount;
                        $loan->save();
                    }

                    Log::info('Reversed regular loan payment', [
                        'transaction_id' => $transaction->id,
                        'schedule_id' => $schedule->id,
                        'amount' => $transaction->amount,
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to reverse loan payment', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['incident_id', 'vehicle_id', 'type', 'amount', 'method', 'note', 'date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function vehicleMaintenance()
    {
        return $this->belongsTo(VehicleMaintenance::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function replacedByTransaction()
    {
        return $this->belongsTo(Transaction::class, 'replaced_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeRevenue($query)
    {
        return $query->where('type', 'thu');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'chi');
    }

    public function scopePlannedExpense($query)
    {
        return $query->where('type', 'du_kien_chi');
    }

    public function scopeFundDeposit($query)
    {
        return $query->where('type', 'nop_quy');
    }

    public function scopeBorrowFromCompany($query)
    {
        return $query->where('type', 'vay_cong_ty');
    }

    public function scopeReturnToCompany($query)
    {
        return $query->where('type', 'tra_cong_ty');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
                     ->whereYear('date', now()->year);
    }

    public function scopeByVehicle($query, $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeCash($query)
    {
        return $query->where('method', 'cash');
    }

    public function scopeBank($query)
    {
        return $query->where('method', 'bank');
    }

    public function scopeStaffWage($query)
    {
        return $query->where('type', 'chi')->whereNotNull('staff_id');
    }

    public function scopeByStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    // Accessors
    public function getTypeLabelAttribute()
    {
        return [
            'thu' => 'Thu',
            'chi' => 'Chi',
            'du_kien_chi' => 'Dự kiến chi',
            'nop_quy' => 'Nộp quỹ',
            'vay_cong_ty' => 'Vay công ty',
            'tra_cong_ty' => 'Trả công ty',
        ][$this->type] ?? $this->type;
    }

    public function getMethodLabelAttribute()
    {
        return [
            'cash' => 'Tiền mặt',
            'bank' => 'Chuyển khoản',
            'other' => 'Khác',
        ][$this->method] ?? $this->method;
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 0, ',', '.') . ' đ';
    }
}
