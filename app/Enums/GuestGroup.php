<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum GuestGroup: string
{
    case CPP = 'cpp';      // Keluarga calon pria
    case CPW = 'cpw';      // Keluarga calon wanita

    use HasLabel;

    public function label(): string
    {
        return match ($this) {
            self::CPP => 'Keluarga CPP',
            self::CPW => 'Keluarga CPW',
        };
    }
}