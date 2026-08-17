<?php

namespace App\Filament\Resources\EventProfileResource\Pages;

use App\Filament\Resources\EventProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventProfile extends EditRecord
{
    protected static string $resource = EventProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
