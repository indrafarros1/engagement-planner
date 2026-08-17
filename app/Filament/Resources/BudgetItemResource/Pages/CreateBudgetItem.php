<?php

namespace App\Filament\Resources\BudgetItemResource\Pages;

use App\Filament\Resources\BudgetItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBudgetItem extends CreateRecord
{
    protected static string $resource = BudgetItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
