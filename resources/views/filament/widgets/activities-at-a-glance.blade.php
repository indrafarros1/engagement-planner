<x-filament-widgets::widget>
    <x-filament::section>
        @php
            $data = $this->getViewData();
            $dueSoon = $data['dueSoon'];
            $overdue = $data['overdue'];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Kegiatan < 7 hari --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-navy-900 inline-flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        Deadline &lt; 7 Hari
                        <span class="text-xs font-semibold bg-amber-50 text-amber-700 rounded-full px-2 py-0.5 tabular-nums">{{ $dueSoon->count() }}</span>
                    </h3>
                    <a href="{{ \App\Filament\Resources\ActivityResource::getUrl('index') }}" class="text-xs font-semibold text-coral-600 hover:text-coral-700 inline-flex items-center gap-1">
                        Semua
                        <x-filament::icon icon="heroicon-m-arrow-right" class="w-3.5 h-3.5" />
                    </a>
                </div>
                @if ($dueSoon->isEmpty())
                    <p class="text-sm text-navy-900/40 py-3">Tidak ada kegiatan dalam 7 hari ke depan.</p>
                @else
                    <ul class="space-y-1.5">
                        @foreach ($dueSoon as $a)
                            <li>
                                <a href="{{ \App\Filament\Resources\ActivityResource::getUrl('edit', ['record' => $a]) }}"
                                   class="flex items-center justify-between rounded-lg bg-amber-50/60 border border-amber-200/60 hover:border-amber-300 px-3.5 py-2.5 transition-colors group">
                                    <span class="text-sm font-medium text-navy-900 truncate">{{ $a->name }}</span>
                                    <span class="text-xs text-amber-700 font-semibold shrink-0 ml-3 tabular-nums">
                                        {{ $a->due_date->translatedFormat('d M') }} · {{ $a->due_date->diffForHumans() }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Kegiatan terlambat --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-navy-900 inline-flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Terlambat
                        <span class="text-xs font-semibold bg-rose-50 text-rose-700 rounded-full px-2 py-0.5 tabular-nums">{{ $overdue->count() }}</span>
                    </h3>
                    <a href="{{ \App\Filament\Resources\ActivityResource::getUrl('index', ['tableFilters' => ['overdue' => true]]) }}" class="text-xs font-semibold text-coral-600 hover:text-coral-700 inline-flex items-center gap-1">
                        Semua
                        <x-filament::icon icon="heroicon-m-arrow-right" class="w-3.5 h-3.5" />
                    </a>
                </div>
                @if ($overdue->isEmpty())
                    <p class="text-sm text-navy-900/40 py-3">Tidak ada kegiatan terlambat.</p>
                @else
                    <ul class="space-y-1.5">
                        @foreach ($overdue as $a)
                            <li>
                                <a href="{{ \App\Filament\Resources\ActivityResource::getUrl('edit', ['record' => $a]) }}"
                                   class="flex items-center justify-between rounded-lg bg-rose-50/60 border border-rose-200/60 hover:border-rose-300 px-3.5 py-2.5 transition-colors">
                                    <span class="text-sm font-medium text-navy-900 truncate">{{ $a->name }}</span>
                                    <span class="text-xs text-rose-700 font-semibold shrink-0 ml-3">
                                        lewat {{ $a->due_date->diffForHumans() }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>