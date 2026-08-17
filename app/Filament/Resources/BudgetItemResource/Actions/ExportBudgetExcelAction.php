<?php

namespace App\Filament\Resources\BudgetItemResource\Actions;

use App\Models\BudgetItem;
use Filament\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportBudgetExcelAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'exportBudgetExcel';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Export Excel Anggaran')
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->action(function () {
                return $this->stream();
            });
    }

    public function stream(): StreamedResponse
    {
        $items = BudgetItem::with('payments')->orderBy('name')->get();

        return response()->streamDownload(function () use ($items) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Anggaran');

            $header = [
                'No', 'Item', 'Kategori', 'Penanggung', 'Harga Satuan', 'Qty',
                'Estimasi', 'Kontrak', 'Total Dibayar', 'Sisa', 'Belum Bayar', 'Arsip',
            ];
            $sheet->fromArray($header, null, 'A1');
            $sheet->getStyle('A1:L1')->getFont()->setBold(true);

            $row = 2;
            foreach ($items as $i => $item) {
                $sheet->fromArray([
                    $i + 1, $item->name, $item->category?->label() ?? '', $item->payer?->label() ?? '',
                    $item->unit_price, $item->quantity,
                    $item->estimate_total, $item->contract_value ?? '', $item->total_paid,
                    $item->remaining, $item->outstanding_payments, $item->archived ? 'Ya' : 'Tidak',
                ], null, "A{$row}");
                $row++;
            }

            // kolom uang → format #,##0
            foreach (['E', 'G', 'H', 'I', 'J', 'K'] as $col) {
                $sheet->getStyle("{$col}2:{$col}" . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
            }

            // ringkasan
            $summaryRow = $row + 1;
            $sheet->setCellValue("A{$summaryRow}", 'RINGKASAN');
            $sheet->getStyle("A{$summaryRow}")->getFont()->setBold(true);
            $sheet->setCellValue("F{$summaryRow}", 'Estimasi');
            $sheet->setCellValue("G{$summaryRow}", $items->sum('estimate_total'));
            $sheet->setCellValue("H{$summaryRow}", $items->sum(fn ($x) => $x->contract_value ?? 0));
            $sheet->setCellValue("I{$summaryRow}", $items->sum('total_paid'));
            $sheet->setCellValue("J{$summaryRow}", $items->sum('remaining'));
            $sheet->setCellValue("K{$summaryRow}", $items->sum('outstanding_payments'));

            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            ob_start();
            $writer->save('php://output');
            $content = ob_get_clean();

            echo $content;
        }, 'anggaran-' . now()->format('Y-m-d') . '.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
