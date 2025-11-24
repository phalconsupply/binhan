<?php

if (!function_exists('setting')) {
    /**
     * Get a system setting value by key.
     * 
     * @param string $key The setting key
     * @param mixed $default Default value if setting not found
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        return \App\Models\SystemSetting::get($key, $default);
    }
}

if (!function_exists('setting_set')) {
    /**
     * Set a system setting value.
     * 
     * @param string $key The setting key
     * @param mixed $value The value to set
     * @return bool
     */
    function setting_set(string $key, $value): bool
    {
        return \App\Models\SystemSetting::set($key, $value);
    }
}

if (!function_exists('settings_clear_cache')) {
    /**
     * Clear all system settings cache.
     * 
     * @return void
     */
    function settings_clear_cache(): void
    {
        \App\Models\SystemSetting::clearCache();
    }
}
