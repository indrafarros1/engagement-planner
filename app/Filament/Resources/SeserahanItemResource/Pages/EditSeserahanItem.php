<?php

namespace App\Filament\Resources\SeserahanItemResource\Pages;

use App\Filament\Resources\SeserahanItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeserahanItem extends EditRecord
{
    protected static string $resource = SeserahanItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Hapus'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
