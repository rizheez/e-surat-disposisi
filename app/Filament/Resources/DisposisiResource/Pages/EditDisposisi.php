<?php

namespace App\Filament\Resources\DisposisiResource\Pages;

use App\Filament\Resources\DisposisiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDisposisi extends EditRecord
{
    protected static string $resource = DisposisiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('lihatSuratMasuk')
                ->label('Detail Surat')
                ->icon('heroicon-o-document-text')
                ->color('gray')
                ->url(fn (): string => DisposisiResource::getSuratMasukUrl($this->record))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
