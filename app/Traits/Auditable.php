<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::logAudit('created', $model);
        });

        static::updated(function ($model) {
            self::logAudit('updated', $model);
        });

        static::deleted(function ($model) {
            self::logAudit('deleted', $model);
        });
    }

    protected static function logAudit($action, $model)
    {
        $oldValues = [];
        $newValues = [];

        if ($action === 'updated') {
            $changes = $model->getChanges();
            foreach ($changes as $key => $value) {
                $oldValues[$key] = $model->getOriginal($key);
                $newValues[$key] = $value;
            }
        } elseif ($action === 'created') {
            $newValues = $model->getAttributes();
        } elseif ($action === 'deleted') {
            $oldValues = $model->getAttributes();
        }

        // Filter hidden attributes to avoid logging sensitive data like passwords
        if (isset($model->hidden)) {
            $hidden = $model->getHidden();
            foreach ($hidden as $key) {
                unset($oldValues[$key]);
                unset($newValues[$key]);
            }
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'old_values' => !empty($oldValues) ? $oldValues : null,
            'new_values' => !empty($newValues) ? $newValues : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'request_id' => request()->header('X-Request-ID'),
        ]);
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
