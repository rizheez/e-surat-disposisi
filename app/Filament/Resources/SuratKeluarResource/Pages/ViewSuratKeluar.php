<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratKeluarResource\Pages;

use App\Filament\Resources\SuratKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSuratKeluar extends ViewRecord
{
    protected static string $resource = SuratKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('previewPdf')
                ->label('Preview PDF')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn () => route('pdf.surat-keluar.preview', $this->record))
                ->openUrlInNewTab()
                ->visible(fn () => filled($this->record->isi_surat)),
            Actions\Action::make('downloadFile')
                ->label('Download File')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->url(fn () => route('surat-keluar.file.download', $this->record))
                ->openUrlInNewTab()
                ->visible(fn () => filled($this->record->file_path)),
            Actions\EditAction::make(),
        ];
    }
}
