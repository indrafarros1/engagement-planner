<?php

namespace App\Observers;

use App\Models\SeserahanItem;
use App\Support\ActivityLogger;

class SeserahanItemObserver
{
    public function created(SeserahanItem $item): void
    {
        ActivityLogger::log('created', $item, "Seserahan dibuat: {$item->name}");
    }

    public function updated(SeserahanItem $item): void
    {
        ActivityLogger::log('updated', $item, "Seserahan diperbarui: {$item->name}", $item->getChanges());
    }

    public function deleted(SeserahanItem $item): void
    {
        ActivityLogger::log('deleted', $item, "Seserahan dihapus: {$item->name}");
    }
}
