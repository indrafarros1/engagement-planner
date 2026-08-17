<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum BudgetCategory: string
{
    case Venue = 'venue';
    case Catering = 'catering';
    case Decoration = 'decoration';
    case Attire = 'attire';
    case Invitation = 'invitation';
    case Documentation = 'documentation';
    case Entertainment = 'entertainment';
    case Transportation = 'transportation';
    case Ring = 'ring';
    case Other = 'other';

    use HasLabel;

    public function label(): string
    {
        return match ($this) {
            self::Venue => 'Tempat & Venue',
            self::Catering => 'Katering',
            self::Decoration => 'Dekorasi',
            self::Attire => 'Busana & Aksesori',
            self::Invitation => 'Undangan',
            self::Documentation => 'Dokumentasi',
            self::Entertainment => 'Hiburan',
            self::Transportation => 'Transportasi',
            self::Ring => 'Cincin Lamaran',
            self::Other => 'Lainnya',
        };
    }
}
