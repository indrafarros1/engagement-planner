<?php

namespace App\Filament\Resources\EventProfileResource\Pages;

use App\Filament\Resources\EventProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEventProfiles extends ListRecords
{
    protected static string $resource = EventProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
