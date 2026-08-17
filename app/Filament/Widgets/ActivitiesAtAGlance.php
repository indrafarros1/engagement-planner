<?php

namespace App\Filament\Widgets;

use App\Enums\ActivityStatus;
use App\Models\Activity;
use Filament\Widgets\Widget;

class ActivitiesAtAGlance extends Widget
{
    protected string $view = 'filament.widgets.activities-at-a-glance';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $base = Activity::where('archived', false)->where('status', '!=', ActivityStatus::Done->value);

        $dueSoon = (clone $base)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>=', today())
            ->whereDate('due_date', '<=', today()->addDays(7))
            ->orderBy('due_date')
            ->get();

        $overdue = (clone $base)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->orderBy('due_date')
            ->get();

        return [
            'dueSoon' => $dueSoon,
            'overdue' => $overdue,
        ];
    }
}
