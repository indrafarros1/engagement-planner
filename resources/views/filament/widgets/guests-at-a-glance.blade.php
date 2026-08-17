<x-filament-widgets::widget>
    <x-filament::section heading="Tamu & Keluarga" icon="heroicon-m-users">
        @php $d = $this->getViewData(); @endphp

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-4">
            <div class="rounded-xl bg-white border border-navy-900/5 p-4">
                <p class="text-[11px] font-semibold text-navy-900/50 uppercase tracking-wider">Kelompok</p>
                <p class="mt-1 text-xl font-extrabold text-navy-900 tabular-nums">{{ $d['total'] }}</p>
            </div>
            <div class="rounded-xl bg-white border border-navy-900/5 p-4">
                <p class="text-[11px] font-semibold text-navy-900/50 uppercase tracking-wider">Total Orang</p>
                <p class="mt-1 text-xl font-extrabold text-navy-900 tabular-nums">{{ $d['people'] }}</p>
            </div>
            <div class="rounded-xl bg-white border border-emerald-200/60 p-4">
                <p class="text-[11px] font-semibold text-emerald-700/70 uppercase tracking-wider">Konfirmasi Hadir</p>
                <p class="mt-1 text-xl font-extrabold text-emerald-600 tabular-nums">{{ $d['confirmed'] }}</p>
            </div>
            <div class="rounded-xl bg-white border border-amber-200/60 p-4">
                <p class="text-[11px] font-semibold text-amber-700/70 uppercase tracking-wider">Belum Konfirmasi</p>
                <p class="mt-1 text-xl font-extrabold text-amber-600 tabular-nums">{{ $d['unknown'] }}</p>
            </div>
            <div class="rounded-xl bg-white border border-rose-200/60 p-4">
                <p class="text-[11px] font-semibold text-rose-700/70 uppercase tracking-wider">Tidak Hadir</p>
                <p class="mt-1 text-xl font-extrabold text-rose-600 tabular-nums">{{ $d['declined'] }}</p>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ \App\Filament\Resources\GuestResource::getUrl('index') }}"
               class="inline-flex items-center gap-1 text-xs font-semibold text-coral-600 hover:text-coral-700">
                Kelola tamu & keluarga
                <x-filament::icon icon="heroicon-m-arrow-right" class="w-3.5 h-3.5" />
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
