<?php

namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Model;

class EventProfile extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'estimated_guests' => 'integer',
            'status' => EventStatus::class,
        ];
    }

    public function coupleDisplay(): string
    {
        return $this->couple_a_name . ' & ' . $this->couple_b_name;
    }
}
