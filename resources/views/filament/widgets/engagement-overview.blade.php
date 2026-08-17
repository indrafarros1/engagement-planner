<x-filament-widgets::widget>
    <x-filament::section>
        @php
            $event = $this->getEvent();
            $stats = $this->getBudgetStats();
            $cd = $this->countdownParts();
            $days = $this->daysUntil();
        @endphp

        {{-- HERO: identitas pasangan + countdown --}}
        <div class="rounded-xl overflow-hidden mb-4"
             style="background: linear-gradient(135deg, #25233A 0%, #3A3459 55%, #25233A 100%);">
            <div class="p-5 sm:p-8 relative">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-coral-300 text-xs font-semibold tracking-widest uppercase mb-1">
                            Persiapan Acara Lamaran
                        </p>
                        <h2 class="text-2xl sm:text-3xl font-bold text-cream-100">
                            {{ $event ? $event->coupleDisplay() : 'Pasangan' }}
                        </h2>
                        @if ($event)
                            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-cream-200/80 text-sm">
                                <span>📅 {{ $event->event_date?->translatedFormat('l, d F Y') }}</span>
                                @if ($event->venue_name)
                                    <span>📍 {{ $event->venue_name }}</span>
                                @endif
                                @if ($event->estimated_guests)
                                    <span>👥 ±{{ number_format($event->estimated_guests) }} tamu</span>
                                @endif
                            </div>
                        @endif

                        @if ($event && ! $cd['past'])
                            <div class="mt-4 flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 text-cream-100/70 text-xs">
                                    <span class="w-2 h-2 rounded-full bg-coral-500 animate-pulse"></span>
                                    Countdown (Asia/Jakarta)
                                </span>
                                <button
                                    type="button"
                                    onclick="window.location='{{ \App\Filament\Resources\EventProfileResource::getUrl('index') }}'"
                                    class="text-coral-300 hover:text-coral-200 text-xs underline underline-offset-2">
                                    Edit profil →
                                </button>
                            </div>
                            <div class="mt-3 grid grid-cols-4 gap-2 sm:gap-3 max-w-md" id="engagement-countdown">
                                <div class="bg-white/10 rounded-xl px-1 py-3 text-center">
                                    <div data-cd="days" class="text-2xl sm:text-3xl font-extrabold text-coral-400">{{ $cd['days'] }}</div>
                                    <div class="text-[10px] sm:text-xs text-cream-200/70 uppercase">Hari</div>
                                </div>
                                <div class="bg-white/10 rounded-xl px-1 py-3 text-center">
                                    <div data-cd="hours" class="text-2xl sm:text-3xl font-extrabold text-coral-400">{{ $cd['hours'] }}</div>
                                    <div class="text-[10px] sm:text-xs text-cream-200/70 uppercase">Jam</div>
                                </div>
                                <div class="bg-white/10 rounded-xl px-1 py-3 text-center">
                                    <div data-cd="minutes" class="text-2xl sm:text-3xl font-extrabold text-coral-400">{{ $cd['minutes'] }}</div>
                                    <div class="text-[10px] sm:text-xs text-cream-200/70 uppercase">Menit</div>
                                </div>
                                <div class="bg-white/10 rounded-xl px-1 py-3 text-center">
                                    <div data-cd="seconds" class="text-2xl sm:text-3xl font-extrabold text-coral-400">{{ $cd['seconds'] }}</div>
                                    <div class="text-[10px] sm:text-xs text-cream-200/70 uppercase">Detik</div>
                                </div>
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
                                        var now = new Date();
                                        var diff = target - now;
                                        if (diff <= 0) { window.location.reload(); return; }
                                        var s = Math.floor(diff / 1000);
                                        var days = Math.floor(s / 86400);
                                        s -= days * 86400;
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
                            <p class="mt-3 inline-flex items-center gap-2 bg-coral-600/90 text-white text-sm font-semibold rounded-lg px-3 py-1.5">
                                🎉 Acara telah dilaksanakan
                            </p>
                        @else
                            <a href="{{ \App\Filament\Resources\EventProfileResource::getUrl('create') }}"
                               class="mt-4 inline-flex items-center gap-2 bg-coral-500 hover:bg-coral-600 text-navy-900 font-semibold rounded-lg px-4 py-2 text-sm">
                                + Lengkapi Profil Acara
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- RINGKASAN ANGGARAN: semua klik → data sumber --}}
        @if ($stats['count'] > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-2">
                <a href="{{ \App\Filament\Resources\BudgetItemResource::getUrl('index') }}"
                   class="flex items-center justify-between rounded-xl bg-white p-4 border border-black/5 hover:border-coral-500 hover:shadow-sm transition-all">
                    <div>
                        <p class="text-xs text-gray-500">Total Anggaran</p>
                        <p class="text-lg font-bold text-navy-900 mt-0.5">Rp {{ number_format($stats['total'], 0, ',', '.') }}</p>
                    </div>
                    <span class="text-coral-500 text-lg">💰</span>
                </a>
                <a href="{{ \App\Filament\Resources\PaymentResource::getUrl('index') }}"
                   class="flex items-center justify-between rounded-xl bg-white p-4 border border-black/5 hover:border-coral-500 hover:shadow-sm transition-all">
                    <div>
                        <p class="text-xs text-gray-500">Dibayar</p>
                        <p class="text-lg font-bold text-emerald-600 mt-0.5">Rp {{ number_format($stats['paid'], 0, ',', '.') }}</p>
                    </div>
                    <span class="text-emerald-500 text-lg">✅</span>
                </a>
                <a href="{{ \App\Filament\Resources\BudgetItemResource::getUrl('index') }}"
                   class="flex items-center justify-between rounded-xl bg-white p-4 border border-black/5 hover:border-coral-500 hover:shadow-sm transition-all">
                    <div>
                        <p class="text-xs text-gray-500">Sisa Kontrak</p>
                        <p class="text-lg font-bold text-amber-600 mt-0.5">Rp {{ number_format($stats['remaining'], 0, ',', '.') }}</p>
                    </div>
                    <span class="text-amber-500 text-lg">📌</span>
                </a>
                <a href="{{ \App\Filament\Resources\PaymentResource::getUrl('index') }}"
                   class="flex items-center justify-between rounded-xl bg-white p-4 border border-black/5 hover:border-coral-500 hover:shadow-sm transition-all">
                    <div>
                        <p class="text-xs text-gray-500">Belum Bayar (Rencana)</p>
                        <p class="text-lg font-bold text-rose-600 mt-0.5">Rp {{ number_format($stats['outstanding'], 0, ',', '.') }}</p>
                    </div>
                    <span class="text-rose-500 text-lg">⏳</span>
                </a>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center">
                <p class="text-gray-500">Belum ada item anggaran.</p>
                <a href="{{ \App\Filament\Resources\BudgetItemResource::getUrl('create') }}"
                   class="mt-3 inline-flex items-center gap-2 bg-coral-500 hover:bg-coral-600 text-white rounded-lg px-4 py-2 text-sm font-semibold">
                    + Tambah Item Anggaran
                </a>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
