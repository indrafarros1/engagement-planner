<?php

namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\OwnedByUser;

class EventProfile extends Model
{
    use OwnedByUser;
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
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
