<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum UserRole: string
{
    case Owner = 'owner';       // Akses penuh
    case Partner = 'partner';   // Pasangan (CPP/CPW) — akses dibatasi

    use HasLabel;

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Owner',
            self::Partner => 'Partner',
        };
    }
}