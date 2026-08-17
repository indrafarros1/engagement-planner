<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum Priority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    use HasLabel;

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Rendah',
            self::Medium => 'Sedang',
            self::High => 'Tinggi',
        };
    }
}
