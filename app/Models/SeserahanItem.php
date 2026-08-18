<?php

namespace App\Models;

use App\Enums\Payer;
use App\Enums\SeserahanStatus;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\OwnedByUser;

class SeserahanItem extends Model
{
    use OwnedByUser;
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'quantity' => 'integer',
            'unit_price' => 'integer',
            'status' => SeserahanStatus::class,
            'pic' => Payer::class,
            'archived' => 'boolean',
        ];
    }

    public function getTotalAttribute(): int
    {
        return (int) $this->unit_price * (int) $this->quantity;
    }
}