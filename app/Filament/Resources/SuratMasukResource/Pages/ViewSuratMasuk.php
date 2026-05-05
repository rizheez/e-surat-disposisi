<?php

namespace App\Filament\Resources\SuratMasukResource\Pages;

use App\Filament\Resources\SuratMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;

class ViewSuratMasuk extends ViewRecord
{
    protected static string $resource = SuratMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('lihatFile')
                ->label('Lihat File')
                ->icon('heroicon-o-document-magnifying-glass')
                ->color('info')
                ->url(fn (): string => Storage::disk('public')->url($this->record->file_path))
                ->openUrlInNewTab()
                ->visible(fn (): bool => filled($this->record->file_path)),
            Actions\EditAction::make(),
        ];
    }
}
