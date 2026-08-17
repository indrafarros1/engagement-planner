<?php

namespace App\Filament\Resources\PaymentResource\Actions;

use App\Models\Payment;
use Filament\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportPaymentsExcelAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'exportPaymentsExcel';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Export Excel Pembayaran')
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->action(function () {
                return $this->stream();
            });
    }

    public function stream(): StreamedResponse
    {
        $payments = Payment::with('budgetItem')->orderBy('due_date')->get();

        return response()->streamDownload(function () use ($payments) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Pembayaran');

            $header = [
                'No', 'Item Anggaran', 'Jenis', 'Nominal', 'Status',
                'Jatuh Tempo', 'Tanggal Dibayar', 'Nominal Dibayar', 'Metode', 'Catatan', 'Bukti',
            ];
            $sheet->fromArray($header, null, 'A1');
            $sheet->getStyle('A1:K1')->getFont()->setBold(true);

            $row = 2;
            foreach ($payments as $i => $p) {
                $sheet->fromArray([
                    $i + 1, $p->budgetItem?->name ?? '', $p->type?->label() ?? '', $p->amount, $p->status()->label(),
                    $p->due_date?->format('d-m-Y') ?? '', $p->paid_date?->format('d-m-Y') ?? '', $p->paidAmount(),
                    $p->method?->label() ?? '', $p->notes ?? '', $p->proof_path ? 'Ada' : '',
                ], null, "A{$row}");
                $row++;
            }

            foreach (['D', 'H'] as $col) {
                $sheet->getStyle("{$col}2:{$col}" . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
            }
            foreach (range('A', 'K') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            ob_start();
            $writer->save('php://output');
            $content = ob_get_clean();

            echo $content;
        }, 'pembayaran-' . now()->format('Y-m-d') . '.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
