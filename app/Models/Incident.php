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
        'from_location_id',
        'to_location_id',
        'partner_id',
        'commission_amount',
        'summary',
        'tags',
    ];

    protected $casts = [
        'date' => 'datetime',
        'tags' => 'array',
        'commission_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['vehicle_id', 'patient_id', 'date', 'dispatch_by', 'destination', 'from_location_id', 'to_location_id', 'partner_id', 'commission_amount', 'summary', 'tags'])
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

    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function vehicleMaintenances()
    {
        return $this->hasMany(VehicleMaintenance::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'incident_staff')
                    ->withPivot('role', 'notes', 'wage_amount', 'wage_details')
                    ->withTimestamps();
    }

    public function drivers()
    {
        return $this->belongsToMany(Staff::class, 'incident_staff')
                    ->wherePivot('role', 'driver')
                    ->withPivot('notes', 'wage_amount', 'wage_details')
                    ->withTimestamps();
    }

    public function medicalStaff()
    {
        return $this->belongsToMany(Staff::class, 'incident_staff')
                    ->wherePivot('role', 'medical_staff')
                    ->withPivot('notes', 'wage_amount', 'wage_details')
                    ->withTimestamps();
    }

    public function additionalServices()
    {
        return $this->hasMany(IncidentAdditionalService::class);
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

    public function getManagementFeeAttribute()
    {
        // 15% management fee only for vehicles with owners
        if ($this->vehicle && $this->vehicle->hasOwner() && $this->net_amount > 0) {
            return $this->net_amount * 0.15;
        }
        return 0;
    }

    public function getProfitAfterFeeAttribute()
    {
        return $this->net_amount - $this->management_fee;
    }
}
