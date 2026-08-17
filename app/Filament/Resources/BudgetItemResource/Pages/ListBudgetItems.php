<?php

namespace App\Filament\Resources\BudgetItemResource\Pages;

use App\Filament\Resources\BudgetItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBudgetItems extends ListRecords
{
    protected static string $resource = BudgetItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Tambah Item Anggaran'),
            \App\Filament\Resources\BudgetItemResource\Actions\ExportBudgetExcelAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BudgetItemResource\Widgets\BudgetStatsOverview::class,
        ];
    }
}
