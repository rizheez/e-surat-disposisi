<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TembusanResource\Pages;
use App\Models\Disposisi;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class TembusanResource extends Resource
{
    protected static ?string $model = Disposisi::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-information-circle';

    protected static string|UnitEnum|null $navigationGroup = 'Persuratan';

    protected static ?string $navigationLabel = 'Tembusan';

    protected static ?string $modelLabel = 'Tembusan';

    protected static ?string $pluralModelLabel = 'Tembusan';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->where('is_tembusan', true)
            ->with(['suratMasuk', 'dariUser', 'keUser']);

        $user = Auth::user();
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isAdminRole()) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($user): void {
            $query
                ->where('dari_user_id', $user->id)
                ->orWhere('ke_user_id', $user->id);
        });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('suratMasuk.nomor_agenda')
                    ->label('No. Agenda')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('suratMasuk.perihal')
                    ->label('Perihal Surat')
                    ->limit(40)
                    ->searchable()
                    ->tooltip(fn (Disposisi $record): ?string => $record->suratMasuk?->perihal),
                Tables\Columns\TextColumn::make('dariUser.name')
                    ->label('Dari')
                    ->sortable(),
                Tables\Columns\TextColumn::make('keUser.name')
                    ->label('Kepada')
                    ->default('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status_informasi')
                    ->label('Jenis')
                    ->badge()
                    ->state('Tembusan')
                    ->color('info'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diterima')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                \Filament\Actions\Action::make('lihatSuratMasuk')
                    ->label('Detail Surat')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->url(fn (Disposisi $record): string => SuratMasukResource::getUrl('view', ['record' => $record->surat_masuk_id])),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTembusans::route('/'),
        ];
    }
}
