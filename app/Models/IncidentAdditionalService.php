<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentAdditionalService extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'additional_service_id',
        'service_name',
        'amount',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Relationship: Belongs to Incident
     */
    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * Relationship: Belongs to Additional Service
     */
    public function additionalService()
    {
        return $this->belongsTo(AdditionalService::class);
    }
}
