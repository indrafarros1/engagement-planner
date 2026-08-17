<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum Payer: string
{
    case CPP = 'cpp';
    case CPW = 'cpw';
    case Bersama = 'bersama';
    case Lainnya = 'lainnya';

    use HasLabel;

    public function label(): string
    {
        return match ($this) {
            self::CPP => 'CPP',
            self::CPW => 'CPW',
            self::Bersama => 'Bersama',
            self::Lainnya => 'Lainnya',
        };
    }
}
