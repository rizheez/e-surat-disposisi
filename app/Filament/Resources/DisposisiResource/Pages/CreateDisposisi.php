<?php

namespace App\Filament\Resources\DisposisiResource\Pages;

use App\Filament\Resources\DisposisiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDisposisi extends CreateRecord
{
    protected static string $resource = DisposisiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dari_user_id'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
