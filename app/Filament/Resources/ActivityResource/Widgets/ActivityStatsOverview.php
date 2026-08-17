<?php

namespace App\Filament\Resources\ActivityResource\Widgets;

use App\Enums\ActivityStatus;
use App\Models\Activity;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActivityStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $total = Activity::where('archived', false)->count();
        $done = Activity::where('archived', false)->where('status', ActivityStatus::Done->value)->count();
        $inProgress = Activity::where('archived', false)->where('status', ActivityStatus::InProgress->value)->count();
        $overdue = Activity::where('archived', false)
            ->where('status', '!=', ActivityStatus::Done->value)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->count();
        $dueSoon = Activity::where('archived', false)
            ->where('status', '!=', ActivityStatus::Done->value)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>=', today())
            ->whereDate('due_date', '<=', today()->addDays(7))
            ->count();

        return [
            Stat::make('Total Kegiatan (aktif)', $total)
                ->description('Belum termasuk arsip'),
            Stat::make('Selesai', $done)->color('success'),
            Stat::make('Sedang Berjalan', $inProgress)->color('info'),
            Stat::make('Terlambat', $overdue)->color('danger')
                ->description($overdue > 0 ? 'Segera ditindaklanjuti' : 'Tidak ada'),
            Stat::make('Deadline < 7 Hari', $dueSoon)->color('warning'),
        ];
    }
}
