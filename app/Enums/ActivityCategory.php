<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum ActivityCategory: string
{
    case Invitation = 'invitation';
    case Venue = 'venue';
    case Catering = 'catering';
    case Attire = 'attire';
    case Documentation = 'documentation';
    case Entertainment = 'entertainment';
    case FamilyMeeting = 'family_meeting';
    case Religious = 'religious';
    case Preparation = 'preparation';
    case Other = 'other';

    use HasLabel;

    public function label(): string
    {
        return match ($this) {
            self::Invitation => 'Undangan',
            self::Venue => 'Tempat & Venue',
            self::Catering => 'Katering',
            self::Attire => 'Busana & Aksesori',
            self::Documentation => 'Dokumentasi',
            self::Entertainment => 'Hiburan',
            self::FamilyMeeting => 'Silaturahmi Keluarga',
            self::Religious => 'Kegiatan Adat/Agama',
            self::Preparation => 'Persiapan Umum',
            self::Other => 'Lainnya',
        };
    }
}
