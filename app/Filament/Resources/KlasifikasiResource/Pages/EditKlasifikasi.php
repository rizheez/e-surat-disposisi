<?php

namespace App\Filament\Resources\KlasifikasiResource\Pages;

use App\Filament\Resources\KlasifikasiResource;
use Filament\Resources\Pages\EditRecord;

class EditKlasifikasi extends EditRecord
{
    protected static string $resource = KlasifikasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
