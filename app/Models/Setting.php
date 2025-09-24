<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'description',
        'type',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function() use ($key, $default) {
            $setting = static::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            // Cast value based on type
            switch ($setting->type) {
                case 'boolean':
                    return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
                case 'integer':
                    return (int) $setting->value;
                case 'array':
                case 'json':
                    return json_decode($setting->value, true);
                default:
                    return $setting->value;
            }
        });
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value, $description = null, $type = 'string', $isPublic = false)
    {
        // Convert value to string for storage
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
            $type = 'json';
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
            $type = 'boolean';
        }

        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description,
                'type' => $type,
                'is_public' => $isPublic
            ]
        );

        // Clear cache
        Cache::forget("setting_{$key}");

        return $setting;
    }

    /**
     * Get all public settings (for frontend use)
     */
    public static function getPublic()
    {
        return Cache::remember('settings_public', 3600, function() {
            $settings = static::where('is_public', true)->get();
            $result = [];
            
            foreach ($settings as $setting) {
                $result[$setting->key] = static::get($setting->key);
            }
            
            return $result;
        });
    }

    /**
     * Clear all setting caches
     */
    public static function clearCache()
    {
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget("setting_{$key}");
        }
        Cache::forget('settings_public');
    }

    /**
     * Get system name
     */
    public static function getSystemName()
    {
        return static::get('app_name', 'Bokod CMS');
    }

    /**
     * Get system logo
     */
    public static function getSystemLogo()
    {
        $logo = static::get('app_logo');
        return $logo ? asset('storage/' . $logo) : null;
    }

    /**
     * Get system favicon
     */
    public static function getSystemFavicon()
    {
        $favicon = static::get('app_favicon');
        return $favicon ? asset('storage/' . $favicon) : null;
    }
}