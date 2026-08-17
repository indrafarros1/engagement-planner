<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum ActivityStatus: string
{
    case NotStarted = 'not_started';
    case InProgress = 'in_progress';
    case Done = 'done';

    use HasLabel;

    public function label(): string
    {
        return match ($this) {
            self::NotStarted => 'Belum Mulai',
            self::InProgress => 'Sedang Berjalan',
            self::Done => 'Selesai',
        };
    }
}
