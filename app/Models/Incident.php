<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Incident extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'vehicle_id',
        'patient_id',
        'date',
        'dispatch_by',
        'destination',
        'summary',
        'tags',
    ];

    protected $casts = [
        'date' => 'datetime',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['vehicle_id', 'patient_id', 'date', 'dispatch_by', 'destination', 'summary', 'tags'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function dispatcher()
    {
        return $this->belongsTo(User::class, 'dispatch_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
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

    // Accessors
    public function getTotalRevenueAttribute()
    {
        return $this->transactions()->where('type', 'thu')->sum('amount');
    }

    public function getTotalExpenseAttribute()
    {
        return $this->transactions()->where('type', 'chi')->sum('amount');
    }

    public function getNetAmountAttribute()
    {
        return $this->total_revenue - $this->total_expense;
    }
}
