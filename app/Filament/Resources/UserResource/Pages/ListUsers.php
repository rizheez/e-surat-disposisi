<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Exports\UserExporter;
use App\Filament\Imports\UserImporter;
use App\Filament\Resources\UserResource;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ExportAction::make()
                ->exporter(UserExporter::class)
                ->label('Export')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success'),
            ImportAction::make('importUsers')
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->csvDelimiter(';')
                ->importer(UserImporter::class),
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
