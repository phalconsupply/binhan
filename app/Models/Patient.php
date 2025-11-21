<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Patient extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'birth_year',
        'phone',
        'gender',
        'address',
        'notes',
    ];

    protected $casts = [
        'birth_year' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'birth_year', 'phone', 'gender', 'address'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    // Accessors
    public function getAgeAttribute()
    {
        if (!$this->birth_year) {
            return null;
        }
        return date('Y') - $this->birth_year;
    }

    public function getGenderLabelAttribute()
    {
        return [
            'male' => 'Nam',
            'female' => 'Nữ',
            'other' => 'Khác',
        ][$this->gender] ?? $this->gender;
    }
}
