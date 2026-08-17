<?php

namespace App\Filament\Resources\BudgetItemResource\Pages;

use App\Filament\Resources\BudgetItemResource;
use App\Filament\Resources\BudgetItemResource\Actions\DownloadBudgetTemplateAction;
use App\Filament\Resources\BudgetItemResource\Actions\ExportBudgetCsvAction;
use App\Filament\Resources\BudgetItemResource\Actions\ExportBudgetExcelAction;
use App\Filament\Resources\BudgetItemResource\Actions\ImportBudgetExcelAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBudgetItems extends ListRecords
{
    protected static string $resource = BudgetItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ Tambah Item Anggaran'),
            \Filament\Actions\ActionGroup::make([
                ImportBudgetExcelAction::make(),
                DownloadBudgetTemplateAction::make(),
                ExportBudgetExcelAction::make(),
                ExportBudgetCsvAction::make(),
            ])->label('File')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BudgetItemResource\Widgets\BudgetStatsOverview::class,
        ];
    }
}
