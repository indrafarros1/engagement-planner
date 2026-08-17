<x-filament-panels::page>
    <div class="space-y-6">
        {{-- FILTER --}}
        <x-filament::section>
            <form wire:submit.prevent class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <label class="text-xs font-semibold text-navy-900/60 uppercase tracking-wider block mb-1.5">Kategori</label>
                    <select wire:model.live="category"
                            class="fi-input w-full rounded-lg border border-navy-900/15 bg-white px-3 py-2 text-sm">
                        <option value="">Semua Kategori</option>
                        @foreach ($this->getCategories() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label class="text-xs font-semibold text-navy-900/60 uppercase tracking-wider block mb-1.5">Vendor</label>
                    <select wire:model.live="vendorId"
                            class="fi-input w-full rounded-lg border border-navy-900/15 bg-white px-3 py-2 text-sm">
                        <option value="">Semua Vendor</option>
                        @foreach ($this->getVendors() as $v)
                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <span class="text-sm text-navy-900/50 pb-2.5">
                        {{ $this->getOptions()->count() }} penawaran
                    </span>
                </div>
            </form>
        </x-filament::section>

        {{-- TABEL PERBANDINGAN --}}
        <x-filament::section heading="Daftar Penawaran" icon="heroicon-m-scale">
            @php
                $options = $this->getOptions();
            @endphp

            @if ($options->isEmpty())
                <p class="text-sm text-navy-900/50 py-4">Belum ada penawaran. Tambahkan vendor & pilihan terlebih dahulu.</p>
            @else
                <div class="overflow-x-auto -mx-2 px-2">
                    <table class="w-full text-sm min-w-[720px]">
                        <thead>
                            <tr class="border-b border-navy-900/10 text-left">
                                <th class="py-2.5 pr-3 font-bold text-[11px] uppercase tracking-wider text-navy-900/50">Vendor</th>
                                <th class="py-2.5 pr-3 font-bold text-[11px] uppercase tracking-wider text-navy-900/50">Kategori</th>
                                <th class="py-2.5 pr-3 font-bold text-[11px] uppercase tracking-wider text-navy-900/50">Paket</th>
                                <th class="py-2.5 pr-3 font-bold text-[11px] uppercase tracking-wider text-navy-900/50">Deskripsi</th>
                                <th class="py-2.5 pr-3 font-bold text-[11px] uppercase tracking-wider text-navy-900/50 text-right">Harga</th>
                                <th class="py-2.5 font-bold text-[11px] uppercase tracking-wider text-navy-900/50">Status</th>
                                <th class="py-2.5 font-bold text-[11px] uppercase tracking-wider text-navy-900/50"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-navy-900/5">
                            @foreach ($options as $opt)
                                @php
                                    $cheapest = $options->where('vendor_id', $opt->vendor_id)->min('price');
                                    $isCheapest = $opt->price === $cheapest;
                                @endphp
                                <tr class="{{ $opt->selected ? 'bg-emerald-50/60' : 'hover:bg-cream-50' }} transition-colors">
                                    <td class="py-3 pr-3 font-semibold text-navy-900">{{ $opt->vendor?->name }}</td>
                                    <td class="py-3 pr-3">
                                        <span class="inline-block text-[11px] font-semibold rounded-md px-2 py-0.5 bg-coral-50 text-coral-700 border border-coral-200">
                                            {{ $opt->vendor?->category?->label() }}
                                        </span>
                                    </td>
                                    <td class="py-3 pr-3 font-medium text-navy-900">
                                        {{ $opt->name }}
                                        @if ($isCheapest && ! $opt->selected)
                                            <span class="ml-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded px-1.5 py-0.5">TERMURAH</span>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-3 text-navy-900/60 max-w-[280px]">{{ $opt->description }}</td>
                                    <td class="py-3 pr-3 text-right font-bold tabular-nums text-navy-900">Rp {{ number_format($opt->price, 0, ',', '.') }}</td>
                                    <td class="py-3 pr-3">
                                        @if ($opt->selected)
                                            <span class="inline-block text-[11px] font-semibold rounded-md px-2 py-0.5 bg-emerald-100 text-emerald-700 border border-emerald-300">✓ Terpilih</span>
                                        @else
                                            <span class="inline-block text-[11px] font-semibold rounded-md px-2 py-0.5 bg-gray-100 text-gray-500 border border-gray-200">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right">
                                        @if (! $opt->selected)
                                            <button wire:click="selectOption({{ $opt->id }})"
                                                    wire:confirm="Pilih paket '{{ $opt->name }}' dari {{ $opt->vendor?->name }}?"
                                                    class="text-xs font-semibold text-coral-600 hover:text-coral-700 bg-coral-50 hover:bg-coral-100 border border-coral-200 rounded-lg px-3 py-1.5 transition-colors whitespace-nowrap">
                                                Pilih
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-xs text-navy-900/40">
                    Paket bertanda <span class="font-semibold text-emerald-700">TERMURAH</span> = harga terendah di vendor tersebut. Pilih satu paket per vendor.
                </p>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
