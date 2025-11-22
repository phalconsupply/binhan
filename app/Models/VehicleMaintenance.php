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

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'vehicle_maintenance_id');
    }

    /**
     * Create a transaction for this maintenance
     * If vehicle has owner, deduct from vehicle profit
     * Otherwise, deduct from company account
     */
    public function createTransaction()
    {
        // Skip if transaction already exists
        if ($this->transaction()->exists()) {
            return $this->transaction;
        }

        $vehicle = $this->vehicle;
        $hasOwner = $vehicle->hasOwner();
        
        $serviceName = $this->maintenanceService ? $this->maintenanceService->name : 'Bảo trì xe';
        $partnerName = $this->partner ? ' - ' . $this->partner->name : '';
        
        $note = "[Bảo trì] {$serviceName}{$partnerName}";
        if ($this->description) {
            $note .= " - {$this->description}";
        }

        $transaction = Transaction::create([
            'vehicle_id' => $this->vehicle_id,
            'vehicle_maintenance_id' => $this->id,
            'incident_id' => $this->incident_id,
            'type' => 'chi',
            'category' => $hasOwner ? 'bảo_trì_xe_chủ_riêng' : 'bảo_trì_xe',
            'amount' => $this->cost,
            'method' => 'cash',
            'note' => $note,
            'date' => $this->date,
            'recorded_by' => $this->user_id ?? auth()->id(),
        ]);

        return $transaction;
    }
}
