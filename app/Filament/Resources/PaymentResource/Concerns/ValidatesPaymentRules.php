<?php

namespace App\Filament\Resources\PaymentResource\Concerns;

use App\Enums\PaymentType;
use App\Models\BudgetItem;
use App\Models\Payment;
use Illuminate\Validation\ValidationException;

/**
 * Aturan bisnis 5.5 untuk pembayaran:
 *  - nominal > 0
 *  - pembayaran valid (DP/Cicilan/Pelunasan) TIDAK boleh melebihi kontrak
 *  - kelebihan hanya bisa dikoreksi via Refund/Koreksi (tanpa hapus histori)
 *  - Refund/Koreksi tidak boleh melebihi total yang sudah dibayar
 */
trait ValidatesPaymentRules
{
    /**
     * @param  array  $data  data form yang akan disimpan
     * @param  int|null  $excludeId  id pembayaran yang sedang diedit (untuk edit)
     * @return array  data yang sudah dilewatkan validasi
     */
    protected function validatePaymentRules(array $data, ?int $excludeId = null): array
    {
        $budgetItem = BudgetItem::findOrFail($data['budget_item_id']);
        $type = PaymentType::from($data['type']);

        // nominal > 0 (aturan 5.5.4) — dipaksa unsigned int di schema, tapi cek ganda
        $amount = (int) ($data['amount'] ?? 0);
        if ($amount <= 0) {
            throw ValidationException::withMessages(['amount' => 'Nominal harus lebih besar dari 0 (aturan 5.5.4).']);
        }

        $cap = $budgetItem->budget_amount; // kontrak bila ada, else estimasi

        // Total nominal pembayaran POSITIF lain (DP/Cicilan/Pelunasan) yang tidak dibatalkan
        $query = Payment::where('budget_item_id', $budgetItem->id)
            ->where('cancelled', false)
            ->whereIn('type', [PaymentType::DP->value, PaymentType::Installment->value, PaymentType::Full->value]);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        $positiveTotal = (int) $query->sum('amount');

        // Total nominal yang benar-benar sudah dibayar (efektif) saat ini
        $paidQuery = Payment::where('budget_item_id', $budgetItem->id)->where('cancelled', false);
        if ($excludeId) {
            $paidQuery->where('id', '!=', $excludeId);
        }
        $effectivePaid = (int) $paidQuery->get()->sum(fn (Payment $p) => $p->signedAmount());

        if (in_array($type, [PaymentType::DP, PaymentType::Installment, PaymentType::Full], true)) {
            // Aturan 5.5.5: pembayaran valid tidak boleh melebihi kontrak
            if ($positiveTotal + $amount > $cap) {
                $excess = ($positiveTotal + $amount) - $cap;
                throw ValidationException::withMessages([
                    'amount' => "Total pembayaran melebihi anggaran ({$budgetItem->name}). Kelebihan Rp " .
                        number_format($excess, 0, ',', '.') . " — gunakan jenis Koreksi/Refund (aturan 5.5.5), tanpa menghapus histori.",
                ]);
            }
        } elseif (in_array($type, [PaymentType::Refund, PaymentType::Correction], true)) {
            // Refund/Koreksi tidak boleh melebihi total yang sudah dibayar
            if ($effectivePaid < $amount) {
                throw ValidationException::withMessages([
                    'amount' => 'Refund/Koreksi tidak boleh melebihi total yang sudah dibayar (' .
                        'Rp ' . number_format($effectivePaid, 0, ',', '.') . ').',
                ]);
            }
        }

        return $data;
    }
}
