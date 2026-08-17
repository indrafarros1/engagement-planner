<?php

namespace App\Filament\Resources\BudgetItemResource\Widgets;

use App\Models\BudgetItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BudgetStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $items = BudgetItem::where('archived', false)->get();

        $total = $items->sum('budget_amount');            // total anggaran (kontrak/estimasi)
        $paid = $items->sum('total_paid');                // total dibayar
        $remaining = $items->sum('remaining');            // sisa
        $outstanding = $items->sum('outstanding_payments'); // belum bayar (rencana pembayaran)

        return [
            Stat::make('Total Anggaran', 'Rp ' . number_format($total, 0, ',', '.'))
                ->description($items->count() . ' item aktif'),
            Stat::make('Total Dibayar', 'Rp ' . number_format($paid, 0, ',', '.'))->color('success'),
            Stat::make('Sisa (Kontrak − Dibayar)', 'Rp ' . number_format($remaining, 0, ',', '.'))->color('warning'),
            Stat::make('Belum Bayar (Rencana)', 'Rp ' . number_format($outstanding, 0, ',', '.'))->color('danger'),
        ];
    }
}
