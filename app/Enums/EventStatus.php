<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum EventStatus: string
{
    case Draft = 'draft';
    case Planning = 'planning';
    case Ready = 'ready';
    case Done = 'done';
    case Cancelled = 'cancelled';

    use HasLabel;

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::Planning => 'Sedang Dipersiapkan',
            self::Ready => 'Siap',
            self::Done => 'Selesai',
            self::Cancelled => 'Dibatalkan',
        };
    }
}
