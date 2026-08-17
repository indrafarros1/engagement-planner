<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Catat Pembayaran'),
            \Filament\Actions\ActionGroup::make([
                \App\Filament\Resources\PaymentResource\Actions\ExportPaymentsExcelAction::make(),
                \App\Filament\Resources\PaymentResource\Actions\ExportPaymentsCsvAction::make(),
            ])->label('Export')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PaymentResource\Widgets\PaymentStatsOverview::class,
        ];
    }
}
