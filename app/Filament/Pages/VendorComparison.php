<?php

namespace App\Filament\Pages;

use App\Enums\BudgetCategory;
use App\Models\Vendor;
use App\Models\VendorOption;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class VendorComparison extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-scale';

    protected static string|\UnitEnum|null $navigationGroup = 'Persiapan';

    protected static ?string $navigationLabel = 'Perbandingan Vendor';

    protected static ?string $title = 'Perbandingan Penawaran Vendor';

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.vendor-comparison';

    public ?int $vendorId = null;

    public ?string $category = null;

    public function mount(int|string|null $vendor = null): void
    {
        $this->vendorId = $vendor ? (int) $vendor : null;
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function selectOption(int $optionId): void
    {
        $option = VendorOption::findOrFail($optionId);

        // satu paket terpilih per vendor
        VendorOption::where('vendor_id', $option->vendor_id)->update(['selected' => false]);
        $option->update(['selected' => true]);

        \App\Support\ActivityLogger::log('updated', $option, "Paket dipilih: {$option->name} ({$option->vendor?->name})");
    }

    public function getVendors(): Collection
    {
        return Vendor::with('options')->where('archived', false)->orderBy('name')->get();
    }

    public function getOptions(): Collection
    {
        $query = VendorOption::with('vendor')
            ->whereHas('vendor', fn ($q) => $q->where('archived', false));

        if ($this->vendorId) {
            $query->where('vendor_id', $this->vendorId);
        }
        if ($this->category) {
            $query->whereHas('vendor', fn ($q) => $q->where('category', $this->category));
        }

        return $query->orderBy('price')->get();
    }

    public function getCategories(): array
    {
        return BudgetCategory::options();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->canViewAmounts() ?? false;
    }
}
