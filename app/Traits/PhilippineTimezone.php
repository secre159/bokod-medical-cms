<?php

namespace App\Traits;

use Carbon\Carbon;

trait PhilippineTimezone
{
    /**
     * Convert UTC time to Philippine time for display
     */
    public function toPhilippineTime($dateTime)
    {
        if (!$dateTime) {
            return null;
        }

        return Carbon::parse($dateTime)
            ->setTimezone('Asia/Manila')
            ->format('Y-m-d H:i:s');
    }

    /**
     * Get current Philippine time
     */
    public static function nowInPhilippines()
    {
        return Carbon::now('Asia/Manila');
    }

    /**
     * Convert Philippine time to UTC for database storage
     */
    public function toUtcTime($dateTime)
    {
        if (!$dateTime) {
            return null;
        }

        return Carbon::parse($dateTime, 'Asia/Manila')
            ->utc()
            ->format('Y-m-d H:i:s');
    }

    /**
     * Format Philippine time for display
     */
    public function formatPhilippineTime($dateTime, $format = 'M j, Y g:i A')
    {
        if (!$dateTime) {
            return 'N/A';
        }

        return Carbon::parse($dateTime)
            ->setTimezone('Asia/Manila')
            ->format($format);
    }

    /**
     * Get Philippine timezone info
     */
    public function getPhilippineTimezoneInfo()
    {
        $carbon = Carbon::now('Asia/Manila');
        
        return [
            'timezone' => 'Asia/Manila',
            'offset' => $carbon->getOffset(),
            'offset_hours' => $carbon->getOffset() / 3600,
            'dst' => $carbon->dst,
            'current_time' => $carbon->format('Y-m-d H:i:s T'),
            'formatted_time' => $carbon->format('F j, Y g:i:s A T')
        ];
    }
}