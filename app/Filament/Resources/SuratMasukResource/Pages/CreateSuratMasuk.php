<?php

namespace App\Filament\Resources\SuratMasukResource\Pages;

use App\Filament\Resources\SuratMasukResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSuratMasuk extends CreateRecord
{
    protected static string $resource = SuratMasukResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $recipient = $this->record->penerimaUser;

        if (! $recipient) {
            return;
        }

        Notification::make()
            ->title('Surat Masuk Baru')
            ->body("Anda menerima surat masuk: {$this->record->perihal}")
            ->icon('heroicon-o-inbox-arrow-down')
            ->iconColor('info')
            ->sendToDatabase($recipient);
    }
}
