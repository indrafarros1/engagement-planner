<?php

namespace App\Filament\Resources\BudgetItemResource\Actions;

use App\Models\BudgetItem;
use Filament\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportBudgetCsvAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'exportBudgetCsv';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Export CSV Anggaran')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->action(function () {
                return $this->streamCsv();
            });
    }

    public function streamCsv(): StreamedResponse
    {
        $items = BudgetItem::with('payments')->orderBy('name')->get();

        return response()->streamDownload(function () use ($items) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No', 'Item', 'Kategori', 'Penanggung', 'Harga Satuan (Rp)', 'Qty',
                'Estimasi (Rp)', 'Kontrak (Rp)', 'Total Dibayar (Rp)', 'Sisa (Rp)',
                'Belum Bayar (Rp)', 'Arsip',
            ]);

            foreach ($items as $i => $item) {
                fputcsv($handle, [
                    $i + 1, $item->name, $item->category?->label() ?? '', $item->payer?->label() ?? '',
                    $item->unit_price, $item->quantity,
                    $item->estimate_total, $item->contract_value ?? '', $item->total_paid,
                    $item->remaining, $item->outstanding_payments, $item->archived ? 'Ya' : 'Tidak',
                ]);
            }

            fclose($handle);
        }, 'anggaran-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }
}
