<?php

namespace App\Models;

use App\Enums\Payer;
use App\Enums\SeserahanStatus;
use Illuminate\Database\Eloquent\Model;

class SeserahanItem extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
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