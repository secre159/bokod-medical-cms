<?php

namespace App\Helpers;

use Carbon\Carbon;

class TimezoneHelper
{
    /**
     * Philippine timezone constant
     */
    const PHILIPPINE_TIMEZONE = 'Asia/Manila';

    /**
     * Get current Philippine time
     */
    public static function now()
    {
        return Carbon::now(self::PHILIPPINE_TIMEZONE);
    }

    /**
     * Convert any datetime to Philippine time
     */
    public static function toPhilippineTime($dateTime, $format = 'Y-m-d H:i:s')
    {
        if (!$dateTime) {
            return null;
        }

        return Carbon::parse($dateTime)
            ->setTimezone(self::PHILIPPINE_TIMEZONE)
            ->format($format);
    }

    /**
     * Convert Philippine time to UTC for database storage
     */
    public static function toUtc($dateTime)
    {
        if (!$dateTime) {
            return null;
        }

        return Carbon::parse($dateTime, self::PHILIPPINE_TIMEZONE)
            ->utc();
    }

    /**
     * Format datetime for Philippine display
     */
    public static function formatForDisplay($dateTime, $format = 'F j, Y g:i A')
    {
        if (!$dateTime) {
            return 'N/A';
        }

        return self::toPhilippineTime($dateTime, $format);
    }

    /**
     * Get timezone information
     */
    public static function getTimezoneInfo()
    {
        $now = self::now();
        
        return [
            'timezone' => self::PHILIPPINE_TIMEZONE,
            'abbreviation' => $now->format('T'),
            'offset_hours' => $now->getOffset() / 3600,
            'dst' => $now->dst,
            'current_time' => $now->format('Y-m-d H:i:s'),
            'formatted_time' => $now->format('l, F j, Y g:i:s A T')
        ];
    }

    /**
     * Check if timezone is properly configured
     */
    public static function isConfigured()
    {
        return config('app.timezone') === self::PHILIPPINE_TIMEZONE;
    }

    /**
     * Set application timezone (for runtime)
     */
    public static function setApplicationTimezone()
    {
        date_default_timezone_set(self::PHILIPPINE_TIMEZONE);
    }
}