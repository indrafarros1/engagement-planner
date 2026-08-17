<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ActivitiesAtAGlance;
use App\Filament\Widgets\EngagementOverview;
use App\Filament\Widgets\GuestsAtAGlance;
use App\Filament\Widgets\UpcomingDpWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard Lamaran';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    public function getWidgets(): array
    {
        return [
            EngagementOverview::class,
            ActivitiesAtAGlance::class,
            GuestsAtAGlance::class,
            UpcomingDpWidget::class,
        ];
    }
}
