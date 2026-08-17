<?php

namespace App\Enums;

use App\Enums\Concerns\HasLabel;

/**
 * Status pembayaran — SELALU dihitung otomatis (aturan bisnis 5.5),
 * tidak pernah disimpan manual.
 */
enum PaymentStatus: string
{
    case Unpaid = 'unpaid';          // Belum Bayar
    case Overdue = 'overdue';        // Terlambat
    case Partial = 'partial';        // Sebagian Dibayar
    case Paid = 'paid';              // Lunas
    case Cancelled = 'cancelled';    // Dibatalkan

    use HasLabel;

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Belum Bayar',
            self::Overdue => 'Terlambat',
            self::Partial => 'Sebagian Dibayar',
            self::Paid => 'Lunas',
            self::Cancelled => 'Dibatalkan',
        };
    }
}
