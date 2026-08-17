<x-filament-widgets::widget>
    <x-filament::section heading="Jatuh Tempo Pembayaran Terdekat" icon="heroicon-m-credit-card">
        @php
            $payments = \Illuminate\Support\Arr::get($this->getViewData(), 'payments', collect());
            $badgeStyles = [
                'danger' => 'bg-rose-50 text-rose-700 border border-rose-200',
                'warning' => 'bg-amber-50 text-amber-700 border border-amber-200',
                'success' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                'gray' => 'bg-gray-50 text-gray-600 border border-gray-200',
            ];
        @endphp

        @if ($payments->isEmpty())
            <p class="text-sm text-navy-900/50">Tidak ada pembayaran terjadwal. Semua lunas.</p>
        @else
            <ul class="divide-y divide-navy-900/5">
                @foreach ($payments as $p)
                    @php
                        $st = $p->status();
                        $badge = match ($st) {
                            \App\Enums\PaymentStatus::Overdue => ['Terlambat', 'danger'],
                            \App\Enums\PaymentStatus::Partial => ['Sebagian Dibayar', 'warning'],
                            \App\Enums\PaymentStatus::Paid => ['Lunas', 'success'],
                            default => [$st->label(), 'gray'],
                        };
                        $isOverdue = $st === \App\Enums\PaymentStatus::Overdue;
                    @endphp
                    <li class="flex items-center justify-between gap-4 py-3.5">
                        <div class="min-w-0">
                            <a href="{{ \App\Filament\Resources\PaymentResource::getUrl('edit', ['record' => $p]) }}"
                               class="text-sm font-semibold text-navy-900 hover:text-coral-600 transition-colors truncate block">
                                {{ $p->budgetItem?->name }}
                                <span class="text-navy-900/40 font-medium">· {{ $p->type?->label() }}</span>
                            </a>
                            <span class="text-xs text-navy-900/50 mt-0.5 block">
                                {{ $p->due_date ? $p->due_date->translatedFormat('d M Y') : 'Tanpa jatuh tempo' }}
                                @if ($isOverdue && $p->due_date)
                                    · <span class="font-semibold text-rose-600">lewat {{ $p->due_date->diffForHumans() }}</span>
                                @endif
                            </span>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-navy-900 tabular-nums">Rp {{ number_format($p->amount, 0, ',', '.') }}</p>
                            <span class="inline-block mt-1 text-[11px] font-semibold rounded-md px-2 py-0.5 {{ $badgeStyles[$badge[1]] }}">
                                {{ $badge[0] }}
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="mt-4 pt-4 border-t border-navy-900/5 text-right">
                <a href="{{ \App\Filament\Resources\PaymentResource::getUrl('index') }}"
                   class="text-xs font-semibold text-coral-600 hover:text-coral-700 transition-colors inline-flex items-center gap-1">
                    Lihat semua pembayaran
                    <x-filament::icon icon="heroicon-m-arrow-right" class="w-3.5 h-3.5" />
                </a>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>