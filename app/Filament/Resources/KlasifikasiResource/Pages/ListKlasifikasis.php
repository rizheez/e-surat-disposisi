<?php

namespace App\Filament\Resources\KlasifikasiResource\Pages;

use App\Filament\Resources\KlasifikasiResource;
use Filament\Resources\Pages\ListRecords;

class ListKlasifikasis extends ListRecords
{
    protected static string $resource = KlasifikasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
