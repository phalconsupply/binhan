<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Vehicle extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'license_plate',
        'model',
        'driver_name',
        'driver_id',
        'phone',
        'status',
        'note',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['license_plate', 'model', 'driver_name', 'phone', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function driver()
    {
        return $this->belongsTo(Staff::class, 'driver_id');
    }

    public function owner()
    {
        return $this->hasOne(Staff::class, 'vehicle_id')->where('staff_type', 'vehicle_owner');
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function vehicleMaintenances()
    {
        return $this->hasMany(VehicleMaintenance::class);
    }

    public function loanProfile()
    {
        return $this->hasOne(LoanProfile::class)->where('status', 'active');
    }

    public function loanProfiles()
    {
        return $this->hasMany(LoanProfile::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return [
            'active' => 'Hoạt động',
            'inactive' => 'Không hoạt động',
            'maintenance' => 'Bảo trì',
        ][$this->status] ?? $this->status;
    }

    // Helper methods
    public function hasOwner()
    {
        return $this->owner()->exists();
    }
}
