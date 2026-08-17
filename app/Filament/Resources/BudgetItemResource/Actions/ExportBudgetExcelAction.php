<?php

namespace App\Filament\Resources\BudgetItemResource\Actions;

use App\Models\BudgetItem;
use Filament\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportBudgetExcelAction extends Action
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
            ->color('success')
            ->action(function () {
                return $this->streamCsv();
            });
    }

    public function streamCsv(): StreamedResponse
    {
        $items = BudgetItem::with('payments')->orderBy('name')->get();

        return response()->streamDownload(function () use ($items) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM supaya Excel membaca aksen/format IDR dengan benar
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'No', 'Item', 'Kategori', 'Penanggung', 'Harga Satuan (Rp)', 'Qty',
                'Estimasi (Rp)', 'Kontrak (Rp)', 'Total Dibayar (Rp)', 'Sisa (Rp)',
                'Belum Bayar (Rp)', 'Arsip',
            ]);

            foreach ($items as $i => $item) {
                fputcsv($handle, [
                    $i + 1,
                    $item->name,
                    $item->category?->label() ?? '',
                    $item->payer?->label() ?? '',
                    $item->unit_price,
                    $item->quantity,
                    $item->estimate_total,
                    $item->contract_value ?? '',
                    $item->total_paid,
                    $item->remaining,
                    $item->outstanding_payments,
                    $item->archived ? 'Ya' : 'Tidak',
                ]);
            }

            // Baris ringkasan
            fputcsv($handle, []);
            fputcsv($handle, ['RINGKASAN', '', '', '', '', '', '', '', '', '', '', '']);
            fputcsv($handle, [
                'Total Anggaran', '', '', '', '', '',
                number_format($items->sum('estimate_total'), 0, '', '.'),
                number_format($items->sum(fn ($x) => $x->contract_value ?? 0), 0, '', '.'),
                number_format($items->sum('total_paid'), 0, '', '.'),
                number_format($items->sum('remaining'), 0, '', '.'),
                number_format($items->sum('outstanding_payments'), 0, '', '.'),
                '',
            ]);

            fclose($handle);
        }, 'anggaran-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }
}
