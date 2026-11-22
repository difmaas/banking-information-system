<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditLogger
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public function log(string $action, string $subjectType, int $subjectId, array $meta = [], ?int $userId = null): AuditLog
    {
        return AuditLog::query()->create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'meta' => $meta,
            'created_at' => now(),
        ]);
    }
}
