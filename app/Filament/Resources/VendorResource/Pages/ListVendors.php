<?php

namespace App\Filament\Resources\VendorResource\Pages;

use App\Filament\Resources\VendorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendors extends ListRecords
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Tambah Vendor'),
            \Filament\Actions\Action::make('compareAll')
                ->label('Bandingkan Semua')
                ->icon('heroicon-o-scale')
                ->color('info')
                ->url(\App\Filament\Pages\VendorComparison::getUrl()),
        ];
    }
}
