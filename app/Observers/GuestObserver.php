<?php

namespace App\Observers;

use App\Models\Guest;
use App\Support\ActivityLogger;

class GuestObserver
{
    public function created(Guest $guest): void
    {
        ActivityLogger::log('created', $guest, "Tamu dibuat: {$guest->name}");
    }

    public function updated(Guest $guest): void
    {
        ActivityLogger::log('updated', $guest, "Tamu diperbarui: {$guest->name}", $guest->getChanges());
    }

    public function deleted(Guest $guest): void
    {
        ActivityLogger::log('deleted', $guest, "Tamu dihapus: {$guest->name}");
    }
}
