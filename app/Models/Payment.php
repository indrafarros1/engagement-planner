<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => PaymentType::class,
            'method' => PaymentMethod::class,
            'amount' => 'integer',
            'paid_amount' => 'integer',
            'due_date' => 'date',
            'paid_date' => 'date',
            'cancelled' => 'boolean',
        ];
    }

    public function budgetItem(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BudgetItem::class);
    }

    /** Nominal yang benar-benar dibayar (fallback: amount bila paid_date terisi). */
    public function paidAmount(): int
    {
        if ($this->paid_amount !== null) {
            return (int) $this->paid_amount;
        }

        return $this->paid_date ? (int) $this->amount : 0;
    }

    /** Efek terhadap total dibayar: + untuk bayar keluar, − untuk Refund/Koreksi (aturan 5.5.5). */
    public function signedAmount(): int
    {
        $effective = match ($this->type) {
            PaymentType::Refund, PaymentType::Correction => 0 - (int) $this->amount,
            default => $this->paidAmount(),
        };

        return $effective;
    }

    public function isFullyPaid(): bool
    {
        return $this->paidAmount() >= (int) $this->amount;
    }

    /**
     * Status pembayaran — dihitung OTOMATIS (aturan 5.5.3).
     * Terlambat = jatuh tempo < hari ini DAN sisa > 0.
     */
    public function status(): PaymentStatus
    {
        if ($this->cancelled) {
            return PaymentStatus::Cancelled;
        }

        // Refund/Koreksi = transaksi koreksi; lunas bila sudah tercatat (paid_date).
        if (in_array($this->type, [PaymentType::Refund, PaymentType::Correction], true)) {
            return $this->paid_date ? PaymentStatus::Paid : PaymentStatus::Unpaid;
        }

        if ($this->isFullyPaid()) {
            return PaymentStatus::Paid;
        }

        if ($this->paidAmount() > 0) {
            return PaymentStatus::Partial;
        }

        if ($this->due_date && $this->due_date->isBefore(today())) {
            return PaymentStatus::Overdue;
        }

        return PaymentStatus::Unpaid;
    }
}
