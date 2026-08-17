<?php

namespace App\Filament\Resources\EventProfileResource\Pages;

use App\Filament\Resources\EventProfileResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEventProfile extends CreateRecord
{
    protected static string $resource = EventProfileResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}