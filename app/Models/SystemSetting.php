<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'options',
        'description',
        'order',
        'is_public',
    ];

    protected $casts = [
        'options' => 'array',
        'is_public' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when settings are updated
        static::saved(function () {
            Cache::forget('system_settings');
        });

        static::deleted(function () {
            Cache::forget('system_settings');
        });
    }

    /**
     * Scope to get settings by group
     */
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group)->orderBy('order');
    }

    /**
     * Scope to get public settings
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Get setting by key
     */
    public static function get($key, $default = null)
    {
        $settings = Cache::remember('system_settings', 3600, function () {
            return self::all()->pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Set setting value
     */
    public static function set($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get all settings as array
     */
    public static function getAllSettings()
    {
        return Cache::remember('system_settings', 3600, function () {
            return self::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get settings by group
     */
    public static function getGroupSettings($group)
    {
        return self::byGroup($group)->get();
    }

    /**
     * Clear settings cache
     */
    public static function clearCache()
    {
        Cache::forget('system_settings');
    }

    /**
     * Check if setting is a file type
     */
    public function isFileType()
    {
        return in_array($this->type, ['file', 'image']);
    }

    /**
     * Get file URL for file/image type settings
     */
    public function getFileUrl()
    {
        if ($this->isFileType() && $this->value) {
            return Storage::url($this->value);
        }
        return null;
    }
}
