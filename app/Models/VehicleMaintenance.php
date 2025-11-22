<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class VehicleMaintenance extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'vehicle_id',
        'maintenance_service_id',
        'partner_id',
        'incident_id',
        'user_id',
        'date',
        'cost',
        'mileage',
        'description',
        'note'
    ];

    protected $casts = [
        'date' => 'date',
        'cost' => 'decimal:2'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['vehicle_id', 'maintenance_service_id', 'partner_id', 'incident_id', 'date', 'cost', 'mileage', 'description', 'note'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function maintenanceService()
    {
        return $this->belongsTo(MaintenanceService::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
