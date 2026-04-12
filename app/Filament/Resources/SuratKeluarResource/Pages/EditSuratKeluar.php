<?php

namespace App\Filament\Resources\SuratKeluarResource\Pages;

use App\Filament\Resources\SuratKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuratKeluar extends EditRecord
{
    protected static string $resource = SuratKeluarResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (filled($data['file_path'] ?? null) && $this->record->status === 'draft') {
            $data['status'] = 'approved';
            $data['approved_at'] = $this->record->approved_at ?? now();
        }

        return $data;
    }

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
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
