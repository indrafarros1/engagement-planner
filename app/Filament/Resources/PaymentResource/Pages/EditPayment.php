<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Filament\Resources\PaymentResource\Concerns\ValidatesPaymentRules;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    use ValidatesPaymentRules;

    protected static string $resource = PaymentResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->validatePaymentRules($data, (int) $this->record->id);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('toggleCancel')
                ->label(fn () => $this->record->cancelled ? 'Aktifkan Kembali' : 'Batalkan Pembayaran')
                ->icon(fn () => $this->record->cancelled ? 'heroicon-o-arrow-uturn-up' : 'heroicon-o-x-circle')
                ->color(fn () => $this->record->cancelled ? 'success' : 'danger')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['cancelled' => ! $this->record->cancelled]);
                    $this->fillForm();
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
