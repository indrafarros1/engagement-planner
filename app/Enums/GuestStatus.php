<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum GuestStatus: string
{
    case Invited = 'invited';        // Diundang
    case Confirmed = 'confirmed';    // Konfirmasi Hadir
    case Declined = 'declined';      // Tidak Hadir
    case Unknown = 'unknown';        // Belum Konfirmasi

    use HasLabel;

    public function label(): string
    {
        return match ($this) {
            self::Invited => 'Diundang',
            self::Confirmed => 'Konfirmasi Hadir',
            self::Declined => 'Tidak Hadir',
            self::Unknown => 'Belum Konfirmasi',
        };
    }
}