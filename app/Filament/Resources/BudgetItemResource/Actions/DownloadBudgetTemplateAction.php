<?php

namespace App\Filament\Resources\BudgetItemResource\Actions;

use Filament\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadBudgetTemplateAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'downloadBudgetTemplate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Download Template')
            ->icon('heroicon-o-document-arrow-down')
            ->color('gray')
            ->action(function () {
                return $this->stream();
            });
    }

    public function stream(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Buku');

            $header = ['Item', 'Kategori', 'Harga Satuan', 'Jumlah', 'Penanggung', 'Kontrak', 'Catatan'];
            $sheet->fromArray($header, null, 'A1');
            $sheet->getStyle('A1:G1')->getFont()->setBold(true);

            // contoh data
            $sheet->fromArray([
                ['Seserahan kue', 'Katering', 250000, 2, 'Bersama', 500000, 'Pesan 2 minggu sebelum H-1'],
                ['Siger', 'Busana & Aksesori', 1500000, 1, 'CPW', '', ''],
                ['Cetak undangan', 'Undangan', 5000, 60, 'CPP', 300000, ''],
            ], null, 'A2');

            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            ob_start();
            $writer->save('php://output');
            $content = ob_get_clean();

            echo $content;
        }, 'Buku-Template.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
