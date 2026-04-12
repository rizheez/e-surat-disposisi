<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DisposisiResource\Pages;
use App\Filament\Resources\DisposisiResource\RelationManagers;
use App\Models\Disposisi;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class DisposisiResource extends Resource
{
    protected static ?string $model = Disposisi::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paper-airplane';

    protected static string|UnitEnum|null $navigationGroup = 'Disposisi';

    protected static ?string $navigationLabel = 'Disposisi';

    protected static ?string $modelLabel = 'Disposisi';

    protected static ?string $pluralModelLabel = 'Disposisi';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Auth::user();
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isAdminRole()) {
            return $query;
        }

        $unitId = $user->unit_kerja_id;

        return $query->where(function (Builder $query) use ($user, $unitId): void {
            $query
                ->where('dari_user_id', $user->id)
                ->orWhere('ke_user_id', $user->id)
                ->when(filled($unitId), function (Builder $query) use ($unitId): void {
                    $query->orWhere('ke_unit_id', $unitId);
                });
        });
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Disposisi')
                    ->schema([
                        Forms\Components\Select::make('surat_masuk_id')
                            ->relationship('suratMasuk', 'nomor_surat')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nomor_agenda} - {$record->perihal}")
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Surat Masuk'),
                        Forms\Components\Select::make('ke_user_id')
                            ->label('Tujuan (User)')
                            ->options(fn () => \App\Models\User::pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('ke_unit_id')
                            ->label('Tujuan (Unit Kerja)')
                            ->options(fn () => \App\Models\UnitKerja::pluck('nama', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'belum_diproses' => 'Belum Diproses',
                                'sedang_diproses' => 'Sedang Diproses',
                                'selesai' => 'Selesai',
                            ])
                            ->default('belum_diproses')
                            ->required()
                            ->label('Status'),
                        Forms\Components\Textarea::make('instruksi')
                            ->required()
                            ->label('Instruksi')
                            ->placeholder('Tuliskan instruksi disposisi...')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan')
                            ->placeholder('Catatan tambahan...')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('batas_waktu')
                            ->label('Batas Waktu')
                            ->minDate(now()),
                    ])->columns(2)->columnSpanFull(),

                Forms\Components\Hidden::make('dari_user_id')
                    ->default(\Illuminate\Support\Facades\Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                    ->state(fn (Disposisi $record): string => 'Level '.$record->getLevel())
                    ->color(fn (Disposisi $record): string => match ($record->getLevel()) {
                        1 => 'primary',
                        2 => 'warning',
                        3 => 'info',
                        default => 'gray',
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw('(CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END) '.$direction);
                    }),
                Tables\Columns\TextColumn::make('suratMasuk.nomor_agenda')
                    ->label('No. Agenda')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('suratMasuk.perihal')
                    ->label('Perihal Surat')
                    ->limit(25)
                    ->searchable()
                    ->tooltip(fn ($record) => $record->suratMasuk?->perihal),
                Tables\Columns\TextColumn::make('dariUser.name')
                    ->label('Dari')
                    ->sortable(),
                Tables\Columns\TextColumn::make('keUser.name')
                    ->label('Kepada')
                    ->default('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('keUnit.nama')
                    ->label('Unit Tujuan')
                    ->default('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('instruksi')
                    ->label('Instruksi')
                    ->limit(35)
                    ->tooltip(fn ($record) => $record->instruksi),
                Tables\Columns\TextColumn::make('batas_waktu')
                    ->label('Batas Waktu')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record) => $record->batas_waktu && $record->batas_waktu->isPast() && $record->status !== 'selesai' ? 'danger' : null)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'belum_diproses' => 'danger',
                        'sedang_diproses' => 'warning',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'belum_diproses' => 'Belum Diproses',
                        'sedang_diproses' => 'Sedang Diproses',
                        'selesai' => 'Selesai',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'belum_diproses' => 'Belum Diproses',
                        'sedang_diproses' => 'Sedang Diproses',
                        'selesai' => 'Selesai',
                    ])
                    ->label('Status'),
                Tables\Filters\Filter::make('disposisi_saya')
                    ->label('Disposisi Saya')
                    ->toggle()
                    ->query(
                        fn (Builder $query): Builder => $query->where('ke_user_id', \Illuminate\Support\Facades\Auth::id())
                    ),
                Tables\Filters\Filter::make('disposisi_dari_saya')
                    ->label('Dari Saya')
                    ->toggle()
                    ->query(
                        fn (Builder $query): Builder => $query->where('dari_user_id', \Illuminate\Support\Facades\Auth::id())
                    ),
                Tables\Filters\Filter::make('terlambat')
                    ->label('Terlambat')
                    ->toggle()
                    ->query(
                        fn (Builder $query): Builder => $query->where('batas_waktu', '<', now())
                            ->where('status', '!=', 'selesai')
                    ),
            ])
            ->actions([
                \Filament\Actions\Action::make('prosesDisposisi')
                    ->label('Proses')
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->authorize('process')
                    ->action(function (Disposisi $record) {
                        $record->update(['status' => 'sedang_diproses']);
                        Notification::make()->title('Disposisi sedang diproses')->success()->send();
                    })
                    ->visible(fn (Disposisi $record): bool => Auth::user()?->can('process', $record) ?? false),
                \Filament\Actions\Action::make('selesaiDisposisi')
                    ->label('Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->authorize('complete')
                    ->action(function (Disposisi $record) {
                        $record->update(['status' => 'selesai']);
                        Notification::make()
                            ->title('Disposisi Selesai')
                            ->body("Disposisi untuk surat {$record->suratMasuk->perihal} telah selesai")
                            ->icon('heroicon-o-check-circle')
                            ->iconColor('success')
                            ->sendToDatabase($record->dariUser);
                        Notification::make()->title('Disposisi ditandai selesai')->success()->send();
                    })
                    ->visible(fn (Disposisi $record): bool => Auth::user()?->can('complete', $record) ?? false),
                \Filament\Actions\Action::make('teruskanDisposisi')
                    ->label('Teruskan')
                    ->icon('heroicon-o-arrow-right')
                    ->color('info')
                    ->authorize('forward')
                    ->modalHeading('Teruskan Disposisi')
                    ->form([
                        Forms\Components\Select::make('ke_user_id')
                            ->label('Tujuan (User)')
                            ->options(fn () => \App\Models\User::pluck('name', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('ke_unit_id')
                            ->label('Tujuan (Unit Kerja)')
                            ->options(fn () => \App\Models\UnitKerja::pluck('nama', 'id'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\Textarea::make('instruksi')
                            ->required()
                            ->label('Instruksi')
                            ->rows(3),
                        Forms\Components\DatePicker::make('batas_waktu')
                            ->label('Batas Waktu'),
                    ])
                    ->action(function (Disposisi $record, array $data) {
                        $newDisposisi = Disposisi::create([
                            'surat_masuk_id' => $record->surat_masuk_id,
                            'dari_user_id' => \Illuminate\Support\Facades\Auth::id(),
                            'ke_user_id' => $data['ke_user_id'] ?? null,
                            'ke_unit_id' => $data['ke_unit_id'] ?? null,
                            'instruksi' => $data['instruksi'],
                            'batas_waktu' => $data['batas_waktu'] ?? null,
                            'status' => 'belum_diproses',
                            'parent_id' => $record->id,
                        ]);

                        if ($newDisposisi->ke_user_id) {
                            $targetUser = \App\Models\User::find($newDisposisi->ke_user_id);
                            if ($targetUser) {
                                Notification::make()
                                    ->title('Disposisi Diteruskan')
                                    ->body("Anda menerima disposisi lanjutan untuk surat: {$record->suratMasuk->perihal}")
                                    ->icon('heroicon-o-paper-airplane')
                                    ->iconColor('warning')
                                    ->sendToDatabase($targetUser);
                            }
                        }

                        Notification::make()->title('Disposisi berhasil diteruskan')->success()->send();
                    })
                    ->visible(fn (Disposisi $record): bool => Auth::user()?->can('forward', $record) ?? false),
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BalasansRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDisposisis::route('/'),
            'create' => Pages\CreateDisposisi::route('/create'),
            'view' => Pages\ViewDisposisi::route('/{record}'),
            'edit' => Pages\EditDisposisi::route('/{record}/edit'),
        ];
    }
}
