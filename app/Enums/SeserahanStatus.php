<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum SeserahanStatus: string
{
    case Planned = 'planned';      // Direncanakan
    case Preparing = 'preparing';  // Sedang Disiapkan
    case Done = 'done';            // Siap

    use HasLabel;

    public function label(): string
    {
        return match ($this) {
            self::Planned => 'Direncanakan',
            self::Preparing => 'Sedang Disiapkan',
            self::Done => 'Siap',
        };
    }
}