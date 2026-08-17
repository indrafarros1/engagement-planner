<?php

namespace App\Filament\Resources\BudgetItemResource\Actions;

use App\Support\BudgetSpreadsheetImporter;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class ImportBudgetExcelAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'importBudgetXlsx';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Impor Buku.xlsx')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('info')
            ->form([
                FileUpload::make('file')
                    ->label('File Buku.xlsx')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-excel',
                        'application/octet-stream',
                    ])
                    ->maxSize(10240)
                    ->disk('local')
                    ->directory('tmp-imports')
                    ->required()
                    ->helperText('Gunakan template Buku.xlsx (tombol unduh template di bawah). Kolom: Item · Kategori · Harga Satuan · Jumlah · Penanggung · Kontrak · Catatan'),
            ])
            ->action(function (array $data) {
                $path = $data['file'];
                $storagePath = Storage::disk('local')->path($path);

                try {
                    $result = app(BudgetSpreadsheetImporter::class)->import($storagePath);
                    $created = $result['created'] ?? collect();
                    $errors = $result['errors'] ?? [];

                    $body = "Berhasil impor {$created->count()} item anggaran.";
                    if ($errors) {
                        $body .= ' Gagal: ' . implode(' | ', array_slice($errors, 0, 5));
                    }

                    if ($created->isNotEmpty()) {
                        \App\Support\ActivityLogger::log('imported', null, "Import Buku.xlsx: {$created->count()} item");
                        Notification::make()->title('Impor Selesai')->body($body)->success()->send();
                    } else {
                        Notification::make()->title('Tidak Ada Data')
                            ->body(implode(' | ', $errors) ?: 'File tidak mengandung baris item yang valid.')
                            ->danger()->send();
                    }
                } catch (\Throwable $e) {
                    Notification::make()->title('Gagal Mengimpor')
                        ->body('File tidak dapat dibaca: ' . $e->getMessage())
                        ->danger()->send();
                } finally {
                    if (isset($storagePath) && is_file($storagePath)) {
                        @unlink($storagePath);
                    }
                }
            })
            ->modalSubmitActionLabel('Impor Sekarang');
    }
}
