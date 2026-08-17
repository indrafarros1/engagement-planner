<?php

namespace App\Observers;

use App\Models\BudgetItem;
use App\Support\ActivityLogger;

class BudgetItemObserver
{
    public function created(BudgetItem $item): void
    {
        ActivityLogger::log('created', $item, "Item anggaran dibuat: {$item->name}");
    }

    public function updated(BudgetItem $item): void
    {
        ActivityLogger::log('updated', $item, "Item anggaran diperbarui: {$item->name}", $item->getChanges());
    }

    public function deleted(BudgetItem $item): void
    {
        ActivityLogger::log('deleted', $item, "Item anggaran dihapus: {$item->name}");
    }
}
