<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;

class UpcomingDpWidget extends Widget
{
    protected string $view = 'filament.widgets.upcoming-dp';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $canSee = auth()->user()?->canViewAmounts() ?? true;

        $payments = Payment::with('budgetItem')
            ->where('cancelled', false)
            ->whereNotNull('due_date')
            ->orderBy('due_date')
            ->get()
            ->filter(fn (Payment $p) => $p->status() !== PaymentStatus::Paid)
            ->sortBy('due_date')
            ->take(6);

        return [
            'payments' => $payments,
            'canSeeAmounts' => $canSee,
        ];
    }
}
