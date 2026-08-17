<x-filament-widgets::widget>
    <x-filament::section>
        @php
            $event = $this->getEvent();
            $stats = $this->getBudgetStats();
            $cd = $this->countdownParts();
            $days = $this->daysUntil();
        @endphp

        {{-- HERO: identitas pasangan + countdown — gaya Kimi K3 (lapang, tegas, minimalis) --}}
        <div class="rounded-2xl overflow-hidden mb-6"
             style="background: linear-gradient(150deg, #25233A 0%, #35324F 60%, #25233A 100%);">
            <div class="px-6 py-8 sm:px-10 sm:py-12">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-6">
                    <div class="max-w-xl">
                        <p class="text-coral-400 text-[11px] font-semibold tracking-[0.22em] uppercase mb-2">
                            Persiapan Acara Lamaran
                        </p>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                            {{ $event ? $event->coupleDisplay() : 'Pasangan' }}
                        </h2>
                        @if ($event)
                            <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 text-sm text-white/70">
                                <span class="inline-flex items-center gap-2">
                                    <x-filament::icon icon="heroicon-m-calendar-days" class="w-4 h-4 text-coral-400" />
                                    {{ $event->event_date?->translatedFormat('l, d F Y') }}
                                </span>
                                @if ($event->venue_name)
                                    <span class="inline-flex items-center gap-2">
                                        <x-filament::icon icon="heroicon-m-map-pin" class="w-4 h-4 text-coral-400" />
                                        {{ $event->venue_name }}
                                    </span>
                                @endif
                                @if ($event->estimated_guests)
                                    <span class="inline-flex items-center gap-2">
                                        <x-filament::icon icon="heroicon-m-users" class="w-4 h-4 text-coral-400" />
                                        ±{{ number_format($event->estimated_guests) }} tamu
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if ($event && ! $cd['past'])
                        <div class="text-sm text-white/70">
                            <a href="{{ \App\Filament\Resources\EventProfileResource::getUrl('index') }}"
                               class="inline-flex items-center gap-1.5 text-coral-400 hover:text-coral-300 font-medium transition-colors">
                                Edit profil acara
                                <x-filament::icon icon="heroicon-m-arrow-right" class="w-4 h-4" />
                            </a>
                        </div>
                    @endif
                </div>

                @if ($event && ! $cd['past'])
                    <div class="mt-8 flex items-center gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-coral-500"></span>
                        <span class="text-white/60 text-xs font-medium tracking-wide uppercase whitespace-nowrap">
                            Countdown · WIB (Asia/Jakarta)
                        </span>
                    </div>
                    <div class="mt-4 grid grid-cols-4 gap-3 sm:gap-6 max-w-lg" id="engagement-countdown">
                        @foreach ([['days','Hari'],['hours','Jam'],['minutes','Menit'],['seconds','Detik']] as [$key, $label])
                            <div class="bg-white/[0.07] backdrop-blur rounded-xl px-2 py-4 sm:py-5 text-center border border-white/10">
                                <div data-cd="{{ $key }}" class="text-3xl sm:text-5xl font-extrabold text-white tabular-nums tracking-tight">
                                    {{ $key === 'days' ? $cd['days'] : str_pad($cd[$key], 2, '0', STR_PAD_LEFT) }}
                                </div>
                                <div class="mt-1 text-[10px] sm:text-xs text-white/50 uppercase tracking-[0.18em] font-semibold">{{ $label }}</div>
                            </div>
                        @endforeach
                    </div>
                    <script>
                        (function () {
                            var box = document.getElementById('engagement-countdown');
                            if (! box || box.dataset.cdInit) return;
                            box.dataset.cdInit = '1';
                            var cells = {
                                days: box.querySelector('[data-cd=days]'),
                                hours: box.querySelector('[data-cd=hours]'),
                                minutes: box.querySelector('[data-cd=minutes]'),
                                seconds: box.querySelector('[data-cd=seconds]'),
                            };
                            var eventDate = @json($event->event_date?->format('Y-m-d'));
                            function tick() {
                                var target = new Date(eventDate + 'T00:00:00+07:00');
                                var diff = target - new Date();
                                if (diff <= 0) { window.location.reload(); return; }
                                var s = Math.floor(diff / 1000);
                                var days = Math.floor(s / 86400); s -= days * 86400;
                                var h = Math.floor(s / 3600); s -= h * 3600;
                                var m = Math.floor(s / 60); var sec = s - m * 60;
                                var pad = function (n) { return String(n).padStart(2, '0'); };
                                cells.days.textContent = days;
                                cells.hours.textContent = pad(h);
                                cells.minutes.textContent = pad(m);
                                cells.seconds.textContent = pad(sec);
                            }
                            tick();
                            setInterval(tick, 1000);
                        })();
                    </script>
                @elseif ($event && $cd['past'])
                    <p class="mt-6 inline-flex items-center gap-2 bg-coral-500/90 text-white text-sm font-semibold rounded-lg px-4 py-2">
                        <x-filament::icon icon="heroicon-m-check-circle" class="w-5 h-5" />
                        Acara telah dilaksanakan
                    </p>
                @else
                    <a href="{{ \App\Filament\Resources\EventProfileResource::getUrl('create') }}"
                       class="mt-6 inline-flex items-center gap-2 bg-coral-500 hover:bg-coral-600 text-white font-semibold rounded-lg px-5 py-2.5 text-sm shadow-sm transition-colors">
                        <x-filament::icon icon="heroicon-m-plus" class="w-4 h-4" />
                        Lengkapi Profil Acara
                    </a>
                @endif
            </div>
        </div>

        {{-- RINGKASAN ANGGARAN — kartu bersih, semua klik → data sumber --}}
        @if ($stats['count'] > 0 && $this->canSeeAmounts())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $cards = [
                        ['Total Anggaran', $stats['total'], 'heroicon-m-banknotes', '#25233A', 'bg-navy-50', \App\Filament\Resources\BudgetItemResource::getUrl('index')],
                        ['Total Dibayar', $stats['paid'], 'heroicon-m-check-circle', '#16A34A', 'bg-emerald-50', \App\Filament\Resources\PaymentResource::getUrl('index')],
                        ['Sisa Kontrak', $stats['remaining'], 'heroicon-m-scale', '#D97706', 'bg-amber-50', \App\Filament\Resources\BudgetItemResource::getUrl('index')],
                        ['Belum Bayar', $stats['outstanding'], 'heroicon-m-clock', '#DC2626', 'bg-rose-50', \App\Filament\Resources\PaymentResource::getUrl('index')],
                    ];
                @endphp
                @foreach ($cards as [$label, $value, $icon, $color, $tint, $url])
                    <a href="{{ $url }}"
                       class="group rounded-xl bg-white border border-navy-900/5 hover:border-coral-400 hover:shadow-md transition-all p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-[11px] font-semibold text-navy-900/50 uppercase tracking-wider">{{ $label }}</p>
                                <p class="mt-2 text-2xl font-extrabold tabular-nums tracking-tight" style="color: {{ $color }}">
                                    Rp {{ number_format($value, 0, ',', '.') }}
                                </p>
                            </div>
                            <span class="rounded-lg p-2.5 {{ $tint }} shrink-0">
                                <x-filament::icon :icon="$icon" class="w-5 h-5" style="color: {{ $color }}" />
                            </span>
                        </div>
                        <p class="mt-3 text-xs font-medium text-navy-900/40 group-hover:text-coral-600 transition-colors inline-flex items-center gap-1">
                            Lihat detail
                            <x-filament::icon icon="heroicon-m-arrow-right" class="w-3.5 h-3.5" />
                        </p>
                    </a>
                @endforeach
            </div>
        @elseif ($stats['count'] > 0 && ! $this->canSeeAmounts())
            <div class="rounded-xl border border-navy-900/10 bg-white/60 p-6 text-center">
                <x-filament::icon icon="heroicon-m-lock-closed" class="w-6 h-6 mx-auto text-navy-900/30" />
                <p class="mt-2 text-sm text-navy-900/50 font-medium">
                    Nominal anggaran disembunyikan untuk akun ini (izin khusus Owner).
                </p>
            </div>
        @else
            <a href="{{ \App\Filament\Resources\BudgetItemResource::getUrl('create') }}"
               class="mt-4 inline-flex items-center gap-2 bg-coral-500 hover:bg-coral-600 text-white rounded-lg px-5 py-2.5 text-sm font-semibold shadow-sm transition-colors">
                <x-filament::icon icon="heroicon-m-plus" class="w-4 h-4" />
                Tambah Item Anggaran
            </a>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>