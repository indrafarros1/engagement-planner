<?php

namespace App\Filament\Widgets;

use App\Enums\GuestStatus;
use App\Models\Guest;
use Filament\Widgets\Widget;

class GuestsAtAGlance extends Widget
{
    protected string $view = 'filament.widgets.guests-at-a-glance';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $guests = Guest::where('archived', false)->get();

        return [
            'total' => $guests->count(),
            'people' => $guests->sum('total_people'),
            'confirmed' => $guests->where('status', GuestStatus::Confirmed)->sum('total_people'),
            'unknown' => $guests->where('status', GuestStatus::Unknown)->sum('total_people'),
            'declined' => $guests->where('status', GuestStatus::Declined)->sum('total_people'),
            'list' => $guests->sortByDesc('total_people')->take(5),
        ];
    }
}
