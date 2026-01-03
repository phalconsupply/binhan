<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AccountingPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'status',
        'closed_by',
        'closed_at',
        'locked_by',
        'locked_at',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'closed_at' => 'datetime',
        'locked_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function closedByUser()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function lockedByUser()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * Scopes
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeLocked($query)
    {
        return $query->where('status', 'locked');
    }

    /**
     * Static methods
     */
    public static function forDate($date)
    {
        $carbon = Carbon::parse($date);
        
        return static::firstOrCreate(
            [
                'year' => $carbon->year,
                'month' => $carbon->month,
            ],
            [
                'status' => 'open',
            ]
        );
    }

    public static function isLocked($date)
    {
        $period = static::forDate($date);
        return $period->status === 'locked';
    }

    public static function isClosed($date)
    {
        $period = static::forDate($date);
        return in_array($period->status, ['closed', 'locked']);
    }

    public static function current()
    {
        return static::forDate(now());
    }

    /**
     * Instance methods
     */
    public function close($userId = null)
    {
        if ($this->status === 'locked') {
            throw new \Exception('Không thể đóng kỳ đã khóa.');
        }

        $this->update([
            'status' => 'closed',
            'closed_by' => $userId ?? auth()->id(),
            'closed_at' => now(),
        ]);
    }

    public function lock($userId = null)
    {
        $this->update([
            'status' => 'locked',
            'locked_by' => $userId ?? auth()->id(),
            'locked_at' => now(),
        ]);
    }

    public function reopen($userId = null)
    {
        if ($this->status === 'locked') {
            throw new \Exception('Không thể mở lại kỳ đã khóa. Phải unlock trước.');
        }

        $this->update([
            'status' => 'open',
            'closed_by' => null,
            'closed_at' => null,
        ]);
    }

    public function unlock($userId = null)
    {
        $this->update([
            'status' => 'open',
            'locked_by' => null,
            'locked_at' => null,
            'closed_by' => null,
            'closed_at' => null,
        ]);
    }

    public function isOpen()
    {
        return $this->status === 'open';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function isLocked()
    {
        return $this->status === 'locked';
    }

    public function getDisplayNameAttribute()
    {
        return sprintf('%02d/%d', $this->month, $this->year);
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'open' => '🔓 Đang mở',
            'closed' => '🔒 Đã đóng',
            'locked' => '🔐 Đã khóa',
            default => $this->status,
        };
    }

    public function canModify()
    {
        return $this->status === 'open';
    }
}
