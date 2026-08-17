<?php

namespace App\Observers;

use App\Models\Activity;
use App\Support\ActivityLogger;

class ActivityObserver
{
    public function created(Activity $activity): void
    {
        ActivityLogger::log('created', $activity, "Kegiatan dibuat: {$activity->name}");
    }

    public function updated(Activity $activity): void
    {
        ActivityLogger::log('updated', $activity, "Kegiatan diperbarui: {$activity->name}", $activity->getChanges());
    }

    public function deleted(Activity $activity): void
    {
        ActivityLogger::log('deleted', $activity, "Kegiatan dihapus: {$activity->name}");
    }
}
