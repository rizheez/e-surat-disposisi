<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Spatie\Activitylog\Models\Activity;

class ActivityLogWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Log Aktivitas Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(Activity::query()->latest()->limit(15))
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('log_name')
                    ->label('Modul')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'surat-masuk' => 'info',
                        'surat-keluar' => 'success',
                        'disposisi' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'surat-masuk' => 'Surat Masuk',
                        'surat-keluar' => 'Surat Keluar',
                        'disposisi' => 'Disposisi',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Aktivitas'),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Oleh')
                    ->default('Sistem'),
                Tables\Columns\TextColumn::make('event')
                    ->label('Event')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->paginated(false);
    }
}
