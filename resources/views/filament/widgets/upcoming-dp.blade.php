<x-filament-widgets::widget>
    <x-filament::section heading="DP & Jatuh Tempo Terdekat" icon="heroicon-o-credit-card">
        @php
            $payments = \Illuminate\Support\Arr::get($this->getViewData(), 'payments', collect());
        @endphp

        @if ($payments->isEmpty())
            <p class="text-sm text-gray-500">Tidak ada pembayaran terjadwal. Semua sudah lunas? 🎉</p>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach ($payments as $p)
                    @php
                        $st = $p->status();
                        $badge = match ($st) {
                            \App\Enums\PaymentStatus::Overdue => ['Terlambat', 'danger'],
                            \App\Enums\PaymentStatus::Partial => ['Sebagian', 'warning'],
                            \App\Enums\PaymentStatus::Paid => ['Lunas', 'success'],
                            default => [$st->label(), 'gray'],
                        };
                        $isOverdue = $st === \App\Enums\PaymentStatus::Overdue;
                    @endphp
                    <li class="flex items-center justify-between gap-3 py-2.5">
                        <div class="min-w-0">
                            <a href="{{ \App\Filament\Resources\PaymentResource::getUrl('edit', ['record' => $p]) }}"
                               class="text-sm font-medium text-navy-900 hover:text-coral-600 truncate block">
                                {{ $p->budgetItem?->name }} · {{ $p->type?->label() }}
                            </a>
                            <span class="text-xs text-gray-500">
                                {{ $p->due_date ? $p->due_date->translatedFormat('d M Y') : 'Tanpa jatuh tempo' }}
                                @if ($isOverdue)
                                    · <span class="font-semibold text-rose-600">melewati {{ $p->due_date->diffForHumans() }}</span>
                                @endif
                            </span>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-navy-900">Rp {{ number_format($p->amount, 0, ',', '.') }}</p>
                            <span class="inline-flex items-center gap-1 text-[11px] font-medium rounded-full px-2 py-0.5
                                {{ match ($badge[1]) {
                                    'danger' => 'bg-rose-100 text-rose-700',
                                    'warning' => 'bg-amber-100 text-amber-700',
                                    'success' => 'bg-emerald-100 text-emerald-700',
                                    default => 'bg-gray-100 text-gray-600',
                                } }}">
                                {{ $badge[0] }}
                            </span>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="mt-3 text-right">
                <a href="{{ \App\Filament\Resources\PaymentResource::getUrl('index') }}"
                   class="text-xs font-semibold text-coral-600 hover:text-coral-700">
                    Lihat semua pembayaran →
                </a>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
