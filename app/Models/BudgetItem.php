<?php

namespace App\Models;

use App\Enums\BudgetCategory;
use App\Enums\Payer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\OwnedByUser;

class BudgetItem extends Model
{
    use OwnedByUser;
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'category' => BudgetCategory::class,
            'payer' => Payer::class,
            'unit_price' => 'integer',
            'quantity' => 'integer',
            'contract_value' => 'integer',
            'archived' => 'boolean',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /** Estimasi = harga satuan × jumlah (PRD: satuan × jumlah = estimasi). */
    public function getEstimateTotalAttribute(): int
    {
        return (int) $this->unit_price * (int) $this->quantity;
    }

    /** Nilai yang dipakai sebagai "total anggaran": kontrak bila ada, else estimasi. */
    public function getBudgetAmountAttribute(): int
    {
        return $this->contract_value ?? $this->estimate_total;
    }

    /**
     * Total dibayar (aturan 5.5.1): jumlah pembayaran VALID (tidak dibatalkan).
     * DP/Cicilan/Pelunasan = +; Refund/Koreksi = − (mengurangi total dibayar).
     */
    public function getTotalPaidAttribute(): int
    {
        return (int) $this->payments()
            ->where('cancelled', false)
            ->get()
            ->sum(function (Payment $p) {
                return $p->signedAmount();
            });
    }

    /** Sisa = kontrak − dibayar (aturan 5.5.2). */
    public function getRemainingAttribute(): int
    {
        return max(0, $this->budget_amount - $this->total_paid);
    }

    /** Total yang masih belum dibayar dari seluruh rencana pembayaran (DP/Cicilan/Pelunasan). */
    public function getOutstandingPaymentsAttribute(): int
    {
        return (int) $this->payments()
            ->where('cancelled', false)
            ->whereIn('type', ['dp', 'installment', 'full'])
            ->get()
            ->sum(function (Payment $p) {
                return max(0, $p->amount - $p->paidAmount());
            });
    }
}
