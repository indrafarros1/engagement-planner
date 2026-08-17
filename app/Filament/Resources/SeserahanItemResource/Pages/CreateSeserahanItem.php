<?php

namespace App\Filament\Resources\SeserahanItemResource\Pages;

use App\Filament\Resources\SeserahanItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSeserahanItem extends CreateRecord
{
    protected static string $resource = SeserahanItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
