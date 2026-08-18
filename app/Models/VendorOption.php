<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\OwnedByUser;

class VendorOption extends Model
{
    use OwnedByUser;
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'price' => 'integer',
            'selected' => 'boolean',
        ];
    }

    public function vendor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}