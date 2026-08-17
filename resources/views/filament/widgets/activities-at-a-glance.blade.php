<x-filament-widgets::widget>
    <x-filament::section>
        @php
            $data = $this->getViewData();
            $dueSoon = $data['dueSoon'];
            $overdue = $data['overdue'];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Kegiatan < 7 hari --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-bold text-navy-900 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        Deadline &lt; 7 Hari ({{ $dueSoon->count() }})
                    </h3>
                    <a href="{{ \App\Filament\Resources\ActivityResource::getUrl('index') }}" class="text-xs text-coral-600 font-semibold">
                        Semua →
                    </a>
                </div>
                @if ($dueSoon->isEmpty())
                    <p class="text-sm text-gray-400">Tidak ada kegiatan dalam 7 hari ke depan.</p>
                @else
                    <ul class="space-y-1.5">
                        @foreach ($dueSoon as $a)
                            <li>
                                <a href="{{ \App\Filament\Resources\ActivityResource::getUrl('edit', ['record' => $a]) }}"
                                   class="flex items-center justify-between rounded-lg bg-amber-50 border border-amber-100 hover:border-amber-300 px-3 py-2">
                                    <span class="text-sm text-navy-900 truncate">{{ $a->name }}</span>
                                    <span class="text-xs text-amber-700 font-semibold shrink-0 ml-2">
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
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-bold text-navy-900 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Terlambat ({{ $overdue->count() }})
                    </h3>
                    <a href="{{ \App\Filament\Resources\ActivityResource::getUrl('index', ['tableFilters' => ['overdue' => true]]) }}" class="text-xs text-coral-600 font-semibold">
                        Semua →
                    </a>
                </div>
                @if ($overdue->isEmpty())
                    <p class="text-sm text-gray-400">Tidak ada kegiatan terlambat. 👍</p>
                @else
                    <ul class="space-y-1.5">
                        @foreach ($overdue as $a)
                            <li>
                                <a href="{{ \App\Filament\Resources\ActivityResource::getUrl('edit', ['record' => $a]) }}"
                                   class="flex items-center justify-between rounded-lg bg-rose-50 border border-rose-100 hover:border-rose-300 px-3 py-2">
                                    <span class="text-sm text-navy-900 truncate">{{ $a->name }}</span>
                                    <span class="text-xs text-rose-700 font-semibold shrink-0 ml-2">
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
