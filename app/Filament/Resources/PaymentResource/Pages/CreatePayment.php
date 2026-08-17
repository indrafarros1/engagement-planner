<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Filament\Resources\PaymentResource\Concerns\ValidatesPaymentRules;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    use ValidatesPaymentRules;

    protected static string $resource = PaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Aturan bisnis 5.5 dijalankan SEBELUM insert
        return $this->validatePaymentRules($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
