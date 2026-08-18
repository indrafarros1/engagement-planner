<?php

namespace App\Models;

use App\Enums\GuestGroup;
use App\Enums\GuestStatus;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\OwnedByUser;

class Guest extends Model
{
    use OwnedByUser;
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'group' => GuestGroup::class,
            'status' => GuestStatus::class,
            'total_people' => 'integer',
            'archived' => 'boolean',
        ];
    }
}