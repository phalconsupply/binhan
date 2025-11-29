<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Asset extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'equipped_date',
        'quantity',
        'brand',
        'usage_type',
        'vehicle_id',
        'staff_id',
        'note',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'equipped_date' => 'date',
        'quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Activity log configuration
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'equipped_date', 'quantity', 'brand', 'usage_type', 'vehicle_id', 'staff_id', 'note', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Relationships
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForVehicle($query)
    {
        return $query->where('usage_type', 'vehicle')->whereNotNull('vehicle_id');
    }

    public function scopeForStaff($query)
    {
        return $query->where('usage_type', 'staff')->whereNotNull('staff_id');
    }

    /**
     * Accessors
     */
    public function getUsageTypeNameAttribute()
    {
        return $this->usage_type === 'vehicle' ? 'Xe' : 'Cá nhân';
    }

    public function getUsageDisplayAttribute()
    {
        if ($this->usage_type === 'vehicle' && $this->vehicle) {
            return 'Xe ' . $this->vehicle->license_plate;
        } elseif ($this->usage_type === 'staff' && $this->staff) {
            return $this->staff->full_name;
        }
        return 'Chưa phân bổ';
    }
}
