<?php

namespace App\Filament\Resources\SeserahanItemResource\Widgets;

use App\Enums\SeserahanStatus;
use App\Models\SeserahanItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SeserahanStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $items = SeserahanItem::where('archived', false)->get();
        $total = $items->count();
        $done = $items->where('status', SeserahanStatus::Done)->count();
        $preparing = $items->where('status', SeserahanStatus::Preparing)->count();
        $planned = $items->where('status', SeserahanStatus::Planned)->count();
        $value = $items->sum('total');

        return [
            Stat::make('Total Item', $total),
            Stat::make('Siap', $done)->color('success'),
            Stat::make('Sedang Disiapkan', $preparing)->color('info'),
            Stat::make('Direncanakan', $planned)->color('warning'),
            Stat::make('Perkiraan Nilai', 'Rp ' . number_format($value, 0, ',', '.'))->color('gray'),
        ];
    }
}
