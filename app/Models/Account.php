<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'category',
        'reference_id',
        'reference_type',
        'parent_id',
        'balance',
        'description',
        'is_active',
        'system_account',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_active' => 'boolean',
        'system_account' => 'boolean',
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Prevent deletion of system accounts
        static::deleting(function ($account) {
            if ($account->system_account) {
                throw new \Exception("Cannot delete system account: {$account->name}");
            }
        });
    }

    /**
     * Get the parent account
     */
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    /**
     * Get child accounts
     */
    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    /**
     * Get transactions where this is the source account
     */
    public function transactionsFrom()
    {
        return $this->hasMany(Transaction::class, 'from_account_id');
    }

    /**
     * Get transactions where this is the destination account
     */
    public function transactionsTo()
    {
        return $this->hasMany(Transaction::class, 'to_account_id');
    }

    /**
     * Get the reference model (Vehicle, Staff, etc)
     */
    public function reference()
    {
        return $this->morphTo('reference');
    }

    /**
     * Scope: Active accounts only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: By type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: By category
     */
    public function scopeOfCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get display name with code
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->code} - {$this->name}";
    }

    /**
     * Get formatted balance
     */
    public function getFormattedBalanceAttribute(): string
    {
        return number_format($this->balance, 0, ',', '.') . 'đ';
    }

    /**
     * Check if account has positive balance
     */
    public function hasPositiveBalance(): bool
    {
        return $this->balance > 0;
    }

    /**
     * Check if account has sufficient balance
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Update balance (should be called within transaction)
     */
    public function updateBalance(float $amount, string $operation = 'add'): void
    {
        if ($operation === 'add') {
            $this->increment('balance', $amount);
        } elseif ($operation === 'subtract') {
            $this->decrement('balance', $amount);
        }
    }

    /**
     * Get account type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'asset' => 'Tài sản',
            'liability' => 'Nợ phải trả',
            'equity' => 'Vốn chủ',
            'revenue' => 'Doanh thu',
            'expense' => 'Chi phí',
            default => $this->type,
        };
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'company_fund' => 'Quỹ công ty',
            'company_reserved' => 'Quỹ dự kiến chi',
            'vehicle' => 'Tài khoản xe',
            'staff' => 'Nhân viên',
            'customer' => 'Khách hàng',
            'partner' => 'Đối tác',
            'external' => 'Bên ngoài',
            'income' => 'Thu nhập',
            'system' => 'Hệ thống',
            default => $this->category,
        };
    }
}
