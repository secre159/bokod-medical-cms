<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'causer_id', 'subject_type', 'subject_id', 'action', 'properties', 'ip', 'user_agent', 'path'
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public static function record(string $action, $subject = null, array $properties = []): self
    {
        // Do not store sensitive properties
        $redactKeys = ['password', 'remember_token', 'mail_password', 'api_key', 'token'];
        $properties = static::redact($properties, $redactKeys);

        $req = request();
        return static::create([
            'causer_id'   => auth()->id(),
            'subject_type'=> is_object($subject) ? get_class($subject) : (is_string($subject) ? $subject : null),
            'subject_id'  => is_object($subject) && method_exists($subject, 'getKey') ? $subject->getKey() : null,
            'action'      => $action,
            'properties'  => empty($properties) ? null : $properties,
            'ip'          => $req ? $req->ip() : null,
            'user_agent'  => $req ? substr((string) $req->userAgent(), 0, 512) : null,
            'path'        => $req ? substr((string) $req->path(), 0, 255) : null,
        ]);
    }

    private static function redact(array $data, array $keys): array
    {
        $out = [];
        foreach ($data as $k => $v) {
            if (in_array($k, $keys, true)) {
                $out[$k] = '***';
            } elseif (is_array($v)) {
                $out[$k] = static::redact($v, $keys);
            } else {
                $out[$k] = $v;
            }
        }
        return $out;
    }
}
