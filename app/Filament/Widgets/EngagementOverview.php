<?php

namespace App\Filament\Widgets;

use App\Models\BudgetItem;
use App\Models\EventProfile;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class EngagementOverview extends Widget
{
    protected string $view = 'filament.widgets.engagement-overview';

    protected int|string|array $columnSpan = 'full';

    public function getEvent(): ?EventProfile
    {
        return EventProfile::first();
    }

    public function getBudgetStats(): array
    {
        $items = BudgetItem::where('archived', false)->get();

        return [
            'total' => $items->sum('budget_amount'),
            'paid' => $items->sum('total_paid'),
            'remaining' => $items->sum('remaining'),
            'outstanding' => $items->sum('outstanding_payments'),
            'count' => $items->count(),
        ];
    }

    public function countdownParts(): array
    {
        $event = $this->getEvent();
        if (! $event?->event_date) {
            return ['days' => null, 'hours' => null, 'minutes' => null, 'seconds' => null, 'past' => false];
        }

        $now = Carbon::now('Asia/Jakarta');
        $target = Carbon::parse($event->event_date)->setTime(0, 0);

        $diff = $now->diff($target);
        $totalDays = max(0, (int) $now->startOfDay()->diffInDays(Carbon::parse($event->event_date)->startOfDay()));

        return [
            'days' => $totalDays,
            'hours' => $now->lt($target) ? $diff->h : 0,
            'minutes' => $now->lt($target) ? $diff->i : 0,
            'seconds' => $now->lt($target) ? $diff->s : 0,
            'past' => $now->gt($target),
        ];
    }

    public function daysUntil(): ?int
    {
        $event = $this->getEvent();
        if (! $event?->event_date) {
            return null;
        }

        return (int) Carbon::now('Asia/Jakarta')->startOfDay()->diffInDays(Carbon::parse($event->event_date)->startOfDay(), false);
    }
}
