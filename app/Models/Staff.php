<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'user_id',
        'full_name',
        'employee_code',
        'staff_type',
        'equity_percentage',
        'phone',
        'email',
        'id_card',
        'birth_date',
        'gender',
        'address',
        'hire_date',
        'department',
        'position',
        'base_salary',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hire_date' => 'date',
        'is_active' => 'boolean',
        'equity_percentage' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function incidents()
    {
        return $this->belongsToMany(Incident::class, 'incident_staff')
                    ->withPivot('role', 'notes', 'wage_amount')
                    ->withTimestamps();
    }

    public function assignedVehicles()
    {
        return $this->hasMany(Vehicle::class, 'driver_id');
    }

    public function wageTransactions()
    {
        return $this->hasMany(Transaction::class)->where('type', 'chi');
    }

    public function adjustments()
    {
        return $this->hasMany(StaffAdjustment::class);
    }

    public function salaryAdvances()
    {
        return $this->hasMany(SalaryAdvance::class);
    }

    // Accessors
    public function getStaffTypeLabelAttribute()
    {
        return match($this->staff_type) {
            'medical_staff' => 'Nhân viên y tế',
            'driver' => 'Lái xe',
            'manager' => 'Quản lý',
            'investor' => 'Cổ đông',
            'admin' => 'Admin',
            default => $this->staff_type,
        };
    }

    public function getGenderLabelAttribute()
    {
        return match($this->gender) {
            'male' => 'Nam',
            'female' => 'Nữ',
            'other' => 'Khác',
            default => '',
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('staff_type', $type);
    }

    // Methods
    public static function generateEmployeeCode()
    {
        // Get the last staff record ordered by ID
        $lastStaff = self::orderBy('id', 'desc')->first();
        
        if (!$lastStaff || !$lastStaff->employee_code) {
            // Start from NV001 if no staff exists or no code
            return 'NV001';
        }
        
        // Extract number from last code (e.g., NV005 -> 5)
        $lastNumber = (int) substr($lastStaff->employee_code, 2);
        
        // Increment and format with leading zeros
        $newNumber = $lastNumber + 1;
        
        return 'NV' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Boot method to auto-generate employee code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($staff) {
            if (empty($staff->employee_code)) {
                $staff->employee_code = self::generateEmployeeCode();
            }
        });
    }
}
