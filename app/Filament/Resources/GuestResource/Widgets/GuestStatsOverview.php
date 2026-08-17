<?php

namespace App\Filament\Resources\GuestResource\Widgets;

use App\Enums\GuestStatus;
use App\Models\Guest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GuestStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $guests = Guest::where('archived', false)->get();

        return [
            Stat::make('Total Kelompok', $guests->count()),
            Stat::make('Total Orang', $guests->sum('total_people'))->color('info'),
            Stat::make('Konfirmasi Hadir', $guests->where('status', GuestStatus::Confirmed)->sum('total_people'))->color('success'),
            Stat::make('Tidak Hadir', $guests->where('status', GuestStatus::Declined)->sum('total_people'))->color('danger'),
            Stat::make('Belum Konfirmasi', $guests->where('status', GuestStatus::Unknown)->sum('total_people'))->color('warning'),
        ];
    }
}
