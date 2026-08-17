<?php

namespace App\Observers;

use App\Models\Payment;
use App\Support\ActivityLogger;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        ActivityLogger::log('created', $payment, "Pembayaran dibuat: {$payment->type?->label()} Rp " . number_format($payment->amount, 0, ',', '.'));
    }

    public function updated(Payment $payment): void
    {
        ActivityLogger::log('updated', $payment, "Pembayaran diperbarui: {$payment->type?->label()}", $payment->getChanges());
    }

    public function deleted(Payment $payment): void
    {
        ActivityLogger::log('deleted', $payment, "Pembayaran dihapus: {$payment->type?->label()}");
    }
}
