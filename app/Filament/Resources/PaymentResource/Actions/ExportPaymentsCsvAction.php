<?php

namespace App\Filament\Resources\PaymentResource\Actions;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Filament\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportPaymentsCsvAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'exportPaymentsCsv';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Export CSV Pembayaran')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->action(function () {
                return $this->streamCsv();
            });
    }

    public function streamCsv(): StreamedResponse
    {
        $payments = Payment::with('budgetItem')->orderBy('due_date')->get();

        return response()->streamDownload(function () use ($payments) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM UTF-8

            fputcsv($handle, [
                'No', 'Item Anggaran', 'Jenis', 'Nominal (Rp)', 'Status',
                'Jatuh Tempo', 'Tanggal Dibayar', 'Nominal Dibayar (Rp)', 'Metode', 'Catatan', 'Bukti',
            ]);

            foreach ($payments as $i => $p) {
                fputcsv($handle, [
                    $i + 1,
                    $p->budgetItem?->name ?? '',
                    $p->type?->label() ?? '',
                    $p->amount,
                    $p->status()->label(),
                    $p->due_date?->format('d-m-Y') ?? '',
                    $p->paid_date?->format('d-m-Y') ?? '',
                    $p->paidAmount(),
                    $p->method?->label() ?? '',
                    $p->notes ?? '',
                    $p->proof_path ? 'Ada' : '',
                ]);
            }

            fclose($handle);
        }, 'pembayaran-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }
}
