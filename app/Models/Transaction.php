<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Transaction extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'incident_id',
        'vehicle_id',
        'staff_id',
        'type',
        'category',
        'amount',
        'method',
        'payment_method',
        'note',
        'recorded_by',
        'date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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

    // Scopes
    public function scopeRevenue($query)
    {
        return $query->where('type', 'thu');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'chi');
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
