<?php

namespace App\Observers;

use App\Models\Vendor;
use App\Support\ActivityLogger;

class VendorObserver
{
    public function created(Vendor $vendor): void
    {
        ActivityLogger::log('created', $vendor, "Vendor dibuat: {$vendor->name}");
    }

    public function updated(Vendor $vendor): void
    {
        ActivityLogger::log('updated', $vendor, "Vendor diperbarui: {$vendor->name}", $vendor->getChanges());
    }

    public function deleted(Vendor $vendor): void
    {
        ActivityLogger::log('deleted', $vendor, "Vendor dihapus: {$vendor->name}");
    }
}
