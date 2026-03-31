<?php

namespace App\Filament\Resources\SuratKeluarResource\Pages;

use App\Filament\Exports\SuratKeluarExporter;
use App\Filament\Resources\SuratKeluarResource;
use Filament\Resources\Pages\ListRecords;

class ListSuratKeluars extends ListRecords
{
    protected static string $resource = SuratKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ExportAction::make()
                ->exporter(SuratKeluarExporter::class)
                ->label('Export')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success'),
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
