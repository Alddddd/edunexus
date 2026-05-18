<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogService
{
    public static function record(
        string $eventType,
        string $title,
        ?string $description = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $status = null,
        ?int $userId = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId ?? auth()->id(),
            'event_type' => $eventType,
            'title' => $title,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'status' => $status,
        ]);
    }
}