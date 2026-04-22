<?php

namespace App\Filament\Resources\SuratMasukResource\Pages;

use App\Filament\Resources\SuratMasukResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSuratMasuk extends EditRecord
{
    protected static string $resource = SuratMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        if (! $this->record->wasChanged('penerima')) {
            return;
        }

        $recipient = $this->record->penerimaUser;

        if (! $recipient) {
            return;
        }

        Notification::make()
            ->title('Penerima Surat Masuk')
            ->body("Anda ditetapkan sebagai penerima surat masuk: {$this->record->perihal}")
            ->icon('heroicon-o-inbox-arrow-down')
            ->iconColor('info')
            ->sendToDatabase($recipient);
    }
}
