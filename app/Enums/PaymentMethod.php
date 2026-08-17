<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

enum PaymentMethod: string
{
    case Transfer = 'transfer';
    case Cash = 'cash';
    case Qris = 'qris';
    case VirtualAccount = 'va';
    case EWallet = 'ewallet';
    case Other = 'other';

    use HasLabel;

    public function label(): string
    {
        return match ($this) {
            self::Transfer => 'Transfer Bank',
            self::Cash => 'Tunai',
            self::Qris => 'QRIS',
            self::VirtualAccount => 'Virtual Account',
            self::EWallet => 'E-Wallet',
            self::Other => 'Lainnya',
        };
    }
}
