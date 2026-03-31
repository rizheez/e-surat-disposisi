<?php

namespace App\Filament\Resources\DisposisiResource\Pages;

use App\Filament\Resources\DisposisiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;

class ViewDisposisi extends ViewRecord
{
    protected static string $resource = DisposisiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getFooter(): ?View
    {
        $chain = $this->record->getFullChain();
        return view('filament.disposisi-chain', ['root' => $chain, 'currentId' => $this->record->id]);
    }
}
