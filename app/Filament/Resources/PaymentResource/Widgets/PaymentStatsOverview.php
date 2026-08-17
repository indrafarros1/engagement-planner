<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $payments = Payment::with('budgetItem')->get();

        $paidTotal = $payments->where('cancelled', false)->sum(fn (Payment $p) => $p->signedAmount());
        $overdue = $payments->filter(fn (Payment $p) => $p->status() === PaymentStatus::Overdue)->count();
        $unpaid = $payments->filter(fn (Payment $p) => $p->status() === PaymentStatus::Unpaid)->count();
        $partial = $payments->filter(fn (Payment $p) => $p->status() === PaymentStatus::Partial)->count();
        $cancelled = $payments->where('cancelled', true)->count();

        return [
            Stat::make('Total Efektif Dibayar', 'Rp ' . number_format(max(0, $paidTotal), 0, ',', '.'))->color('success'),
            Stat::make('Belum Bayar', $unpaid)->color('gray'),
            Stat::make('Sebagian Dibayar', $partial)->color('info'),
            Stat::make('Terlambat', $overdue)->color('danger')
                ->description($overdue > 0 ? 'Segera diproses' : 'Tidak ada'),
            Stat::make('Dibatalkan', $cancelled)->color('gray'),
        ];
    }
}
