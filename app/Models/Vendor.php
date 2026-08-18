<?php

namespace App\Models;

use App\Enums\BudgetCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\OwnedByUser;

class Vendor extends Model
{
    use OwnedByUser;
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'category' => BudgetCategory::class,
            'archived' => 'boolean',
        ];
    }

    public function options(): HasMany
    {
        return $this->hasMany(VendorOption::class);
    }

    public function selectedOption(): ?VendorOption
    {
        return $this->options()->where('selected', true)->first();
    }

    public function getSelectedOptionNameAttribute(): ?string
    {
        return $this->selectedOption()?->name;
    }

    public function getSelectedOptionPriceAttribute(): ?int
    {
        return $this->selectedOption()?->price;
    }
}