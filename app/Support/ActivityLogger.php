<?php

namespace App\Support;

use App\Models\ActivityLog;

/**
 * Pencatat aktivitas pengguna (Fase 3): dipanggil dari observer model.
 */
class ActivityLogger
{
    public static function log(
        string $action,
        $model = null,
        ?string $description = null,
        array $changes = []
    ): void {
        if (! auth()->check()) {
            return; // hanya aksi dari pengguna yang tercatat
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->getKey(),
            'description' => $description,
            'changes' => $changes ?: null,
            'ip' => request()->ip(),
        ]);
    }
}
