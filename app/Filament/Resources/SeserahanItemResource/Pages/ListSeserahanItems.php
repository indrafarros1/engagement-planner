<?php

namespace App\Filament\Resources\SeserahanItemResource\Pages;

use App\Filament\Resources\SeserahanItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSeserahanItems extends ListRecords
{
    protected static string $resource = SeserahanItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Tambah Seserahan'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SeserahanItemResource\Widgets\SeserahanStatsOverview::class,
        ];
    }
}
