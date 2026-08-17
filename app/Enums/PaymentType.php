<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum PaymentType: string
{
    case DP = 'dp';
    case Installment = 'installment';
    case Full = 'full';
    case Refund = 'refund';
    case Correction = 'correction';

    use HasLabel;

    public function label(): string
    {
        return match ($this) {
            self::DP => 'DP',
            self::Installment => 'Cicilan',
            self::Full => 'Pelunasan',
            self::Refund => 'Refund',
            self::Correction => 'Koreksi',
        };
    }
}
