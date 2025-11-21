<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'vehicle_id',
        'user_id',
        'note',
        'severity',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeInfo($query)
    {
        return $query->where('severity', 'info');
    }

    public function scopeWarning($query)
    {
        return $query->where('severity', 'warning');
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    // Accessors
    public function getSeverityLabelAttribute()
    {
        return [
            'info' => 'Thông tin',
            'warning' => 'Cảnh báo',
            'critical' => 'Nghiêm trọng',
        ][$this->severity] ?? $this->severity;
    }

    public function getSeverityColorAttribute()
    {
        return [
            'info' => 'blue',
            'warning' => 'yellow',
            'critical' => 'red',
        ][$this->severity] ?? 'gray';
    }
}
