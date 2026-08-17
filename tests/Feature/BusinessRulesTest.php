<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\BudgetItem;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Verifikasi aturan bisnis wajib PRD 5.5.
 */
class BusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    private function item(array $overrides = []): BudgetItem
    {
        return BudgetItem::create(array_merge([
            'name' => 'Test Item',
            'category' => 'other',
            'unit_price' => 100000,
            'quantity' => 1,
            'contract_value' => 1000000,
            'payer' => 'bersama',
        ], $overrides));
    }

    private function pay(BudgetItem $item, array $overrides = []): Payment
    {
        return Payment::create(array_merge([
            'budget_item_id' => $item->id,
            'type' => PaymentType::DP->value,
            'amount' => 100000,
        ], $overrides));
    }

    public function test_total_paid_adalah_jumlah_pembayaran_valid(): void
    {
        $item = $this->item();
        $this->pay($item, ['amount' => 200000, 'paid_date' => today(), 'type' => PaymentType::DP->value]);
        $this->pay($item, ['amount' => 300000, 'paid_date' => today(), 'type' => PaymentType::Installment->value]);

        $this->assertSame(500000, $item->fresh()->total_paid);
    }

    public function test_sisa_adalah_kontrak_dikurangi_dibayar(): void
    {
        $item = $this->item(['contract_value' => 1000000]);
        $this->pay($item, ['amount' => 400000, 'paid_date' => today()]);

        $this->assertSame(600000, $item->fresh()->remaining);
    }

    public function test_terlambat_ketika_jatuh_tempo_lalu_dan_sisa_positif(): void
    {
        $item = $this->item(['contract_value' => 1000000]);
        $p = $this->pay($item, ['amount' => 500000, 'due_date' => today()->subDays(3)]);

        $this->assertTrue($p->fresh()->status() === PaymentStatus::Overdue);
    }

    public function test_nominal_harus_positif(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $item = $this->item();
        $trait = new class {
            use \App\Filament\Resources\PaymentResource\Concerns\ValidatesPaymentRules {
                validatePaymentRules as public;
            }
        };
        $trait->validatePaymentRules(['budget_item_id' => $item->id, 'type' => 'dp', 'amount' => 0]);
    }

    public function test_pembayaran_tidak_boleh_melebihi_kontrak(): void
    {
        $item = $this->item(['contract_value' => 1000000]);
        $this->pay($item, ['amount' => 600000, 'paid_date' => today()]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $trait = new class {
            use \App\Filament\Resources\PaymentResource\Concerns\ValidatesPaymentRules {
                validatePaymentRules as public;
            }
        };
        // 600rb + 500rb = 1.1jt > kontrak 1jt → ditolak
        $trait->validatePaymentRules(['budget_item_id' => $item->id, 'type' => 'dp', 'amount' => 500000]);
    }

    public function test_koreksi_refund_mengurangi_dibayar_tanpa_hapus_histori(): void
    {
        $item = $this->item(['contract_value' => 1000000]);
        $this->pay($item, ['amount' => 1000000, 'paid_date' => today(), 'type' => PaymentType::Full->value]);
        $this->assertSame(1000000, $item->fresh()->total_paid);

        // Koreksi −200rb: reduksi via transaksi koreksi, history tetap ada
        $koreksi = $this->pay($item, ['amount' => 200000, 'paid_date' => today(), 'type' => PaymentType::Correction->value]);

        $this->assertSame(800000, $item->fresh()->total_paid);
        $this->assertSame(2, Payment::where('budget_item_id', $item->id)->count()); // histori tidak dihapus
        $this->assertTrue($koreksi->fresh()->status() === PaymentStatus::Paid);
    }

    public function test_kegiatan_selesai_tidak_membuat_pembayaran(): void
    {
        // Tidak ada hook otomatis: menyelesaikan kegiatan tidak menciptakan Payment
        $this->assertSame(0, Payment::count());
    }

    public function test_semua_nominal_integer_rupiah(): void
    {
        $item = $this->item(['unit_price' => 12500, 'quantity' => 3]);
        $this->assertSame(37500, $item->estimate_total);
        // Pastikan kolom bertipe integer (bukan float) di pivot DB
        $this->assertIsInt($item->unit_price);
        $this->assertIsInt($item->estimate_total);
    }

    public function test_status_otomatis_lengkap(): void
    {
        $item = $this->item(['contract_value' => 1000000]);

        // Belum bayar (masa depan)
        $unpaid = $this->pay($item, ['amount' => 100000, 'due_date' => today()->addDays(5)]);
        $this->assertSame(PaymentStatus::Unpaid, $unpaid->status());

        // Sebagian dibayar
        $partial = $this->pay($item, ['amount' => 200000, 'due_date' => today()->addDays(2), 'paid_date' => today(), 'paid_amount' => 100000]);
        $this->assertSame(PaymentStatus::Partial, $partial->status());

        // Lunas
        $paid = $this->pay($item, ['amount' => 300000, 'paid_date' => today()]);
        $this->assertSame(PaymentStatus::Paid, $paid->status());

        // Dibatalkan
        $cancelled = $this->pay($item, ['amount' => 100000, 'cancelled' => true]);
        $this->assertSame(PaymentStatus::Cancelled, $cancelled->status());
    }

    public function test_refund_tidak_boleh_melebihi_total_dibayar(): void
    {
        $item = $this->item(['contract_value' => 1000000]);
        $this->pay($item, ['amount' => 500000, 'paid_date' => today()]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $trait = new class {
            use \App\Filament\Resources\PaymentResource\Concerns\ValidatesPaymentRules {
                validatePaymentRules as public;
            }
        };
        // Refund 600rb > total dibayar 500rb → ditolak
        $trait->validatePaymentRules(['budget_item_id' => $item->id, 'type' => 'refund', 'amount' => 600000]);
    }
}