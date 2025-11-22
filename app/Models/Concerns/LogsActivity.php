<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            ActivityLog::record('created', $model, [
                'attributes' => $model->getAttributes(),
            ]);
        });

        static::updated(function ($model) {
            $changes = [];
            $dirty = $model->getChanges();
            unset($dirty[$model->getKeyName()]);
            if (!empty($dirty)) {
                foreach ($dirty as $key => $new) {
                    $changes[$key] = [
                        'old' => $model->getOriginal($key),
                        'new' => $new,
                    ];
                }
            }
            if (!empty($changes)) {
                ActivityLog::record('updated', $model, ['changes' => $changes]);
            }
        });

        static::deleted(function ($model) {
            ActivityLog::record('deleted', $model);
        });
    }
}
