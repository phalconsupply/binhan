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
}
