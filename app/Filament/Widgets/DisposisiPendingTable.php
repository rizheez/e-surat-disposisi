<?php

namespace App\Filament\Widgets;

use App\Models\Disposisi;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class DisposisiPendingTable extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Disposisi Menunggu Tindakan';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Disposisi::query()
                    ->whereIn('status', ['belum_diproses', 'sedang_diproses'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('suratMasuk.nomor_agenda')
                    ->label('No. Agenda'),
                Tables\Columns\TextColumn::make('suratMasuk.perihal')
                    ->label('Perihal')
                    ->limit(30),
                Tables\Columns\TextColumn::make('dariUser.name')
                    ->label('Dari'),
                Tables\Columns\TextColumn::make('keUser.name')
                    ->label('Kepada')
                    ->default('-'),
                Tables\Columns\TextColumn::make('instruksi')
                    ->label('Instruksi')
                    ->limit(30),
                Tables\Columns\TextColumn::make('batas_waktu')
                    ->label('Batas Waktu')
                    ->date('d M Y')
                    ->color(fn($record) => $record->batas_waktu?->isPast() ? 'danger' : null),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'belum_diproses' => 'danger',
                        'sedang_diproses' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'belum_diproses' => 'Belum Diproses',
                        'sedang_diproses' => 'Sedang Diproses',
                        default => $state,
                    }),
            ])
            ->paginated(false);
    }
}
