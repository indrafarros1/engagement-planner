<?php

namespace App\Models;

use App\Enums\GuestGroup;
use App\Enums\GuestStatus;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'group' => GuestGroup::class,
            'status' => GuestStatus::class,
            'total_people' => 'integer',
            'archived' => 'boolean',
        ];
    }
}