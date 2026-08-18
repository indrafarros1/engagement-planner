<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Akun yang dibuat oleh owner → otomatis menjadi anggota (member) owner tsb.
        // Owner yang sedang login akan menjadi "owner" dari akun baru ini.
        if (auth()->user()?->isOwner()) {
            $data['owner_id'] = auth()->id();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
