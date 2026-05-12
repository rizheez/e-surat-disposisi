<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DisposisiResource\Pages;
use App\Filament\Resources\DisposisiResource\RelationManagers;
use App\Models\Disposisi;
use App\Models\User;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class DisposisiResource extends Resource
{
    protected static ?string $model = Disposisi::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static string|UnitEnum|null $navigationGroup = 'Persuratan';

    protected static ?string $navigationLabel = 'Disposisi';

    protected static ?string $modelLabel = 'Disposisi';

    protected static ?string $pluralModelLabel = 'Disposisi';

    protected static ?int $navigationSort = 3;

    public static function getSuratMasukUrl(Disposisi $record): string
    {
        return SuratMasukResource::getUrl('view', ['record' => $record->surat_masuk_id]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->where('is_tembusan', false);

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
                            ->label('Tujuan User')
                            ->options(fn () => User::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
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
                        Forms\Components\DatePicker::make('batas_waktu')
                            ->label('Batas Waktu')
                            ->required()
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
                    ->authorize('complete')
                    ->modalHeading('Selesaikan Disposisi')
                    ->modalSubmitActionLabel('Simpan dan Selesaikan')
                    ->form([
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan Penyelesaian')
                            ->placeholder('Tuliskan hasil tindak lanjut atau alasan disposisi dinyatakan selesai...')
                            ->required()
                            ->rows(4)
                            ->maxLength(1000),
                    ])
                    ->action(function (Disposisi $record, array $data) {
                        $record->update([
                            'status' => 'selesai',
                            'catatan' => $data['catatan'],
                        ]);

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
                            ->label('Tujuan User')
                            ->options(fn () => User::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('tembusan_user_ids')
                            ->label('Tembusan (Opsional)')
                            ->options(fn () => User::pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Textarea::make('instruksi')
                            ->required()
                            ->label('Instruksi')
                            ->rows(3),
                        Forms\Components\DatePicker::make('batas_waktu')
                            ->label('Batas Waktu')
                            ->required(),
                    ])
                    ->action(function (Disposisi $record, array $data) {
                        $newDisposisi = Disposisi::create([
                            'surat_masuk_id' => $record->surat_masuk_id,
                            'dari_user_id' => \Illuminate\Support\Facades\Auth::id(),
                            'ke_user_id' => $data['ke_user_id'] ?? null,
                            'ke_unit_id' => null,
                            'instruksi' => $data['instruksi'],
                            'batas_waktu' => $data['batas_waktu'] ?? null,
                            'status' => 'belum_diproses',
                            'parent_id' => $record->id,
                        ]);

                        if ($record->status === 'belum_diproses') {
                            $record->update(['status' => 'sedang_diproses']);
                        }

                        if ($newDisposisi->ke_user_id) {
                            $targetUser = User::find($newDisposisi->ke_user_id);
                            if ($targetUser) {
                                Notification::make()
                                    ->title('Disposisi Diteruskan')
                                    ->body("Anda menerima disposisi lanjutan untuk surat: {$record->suratMasuk->perihal}")
                                    ->icon('heroicon-o-paper-airplane')
                                    ->iconColor('warning')
                                    ->sendToDatabase($targetUser);
                            }
                        }

                        if (! empty($data['tembusan_user_ids'])) {
                            foreach ($data['tembusan_user_ids'] as $userId) {
                                Disposisi::create([
                                    'surat_masuk_id' => $record->surat_masuk_id,
                                    'dari_user_id' => \Illuminate\Support\Facades\Auth::id(),
                                    'ke_user_id' => $userId,
                                    'ke_unit_id' => null,
                                    'instruksi' => 'Mengetahui (Tembusan). Instruksi utama: '.$data['instruksi'],
                                    'status' => 'selesai',
                                    'is_tembusan' => true,
                                    'parent_id' => $newDisposisi->id,
                                ]);

                                $tempUser = User::find($userId);
                                if ($tempUser) {
                                    Notification::make()
                                        ->title('Tembusan Disposisi')
                                        ->body("Anda mendapat tembusan disposisi untuk surat: {$record->suratMasuk->perihal}")
                                        ->icon('heroicon-o-information-circle')
                                        ->iconColor('info')
                                        ->sendToDatabase($tempUser);
                                }
                            }
                        }

                        Notification::make()->title('Disposisi berhasil diteruskan')->success()->send();
                    })
                    ->visible(fn (Disposisi $record): bool => Auth::user()?->can('forward', $record) ?? false),
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('lihatSuratMasuk')
                        ->label('Detail Surat')
                        ->icon('heroicon-o-document-text')
                        ->color('gray')
                        ->url(fn (Disposisi $record): string => static::getSuratMasukUrl($record))
                        ->openUrlInNewTab(),
                    \Filament\Actions\ViewAction::make(),
                    \Filament\Actions\EditAction::make(),
                ])
                    ->label('Menu')
                    ->size(Size::Small)
                    ->button()
                    ->color('warning'),
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
            'view' => Pages\ViewDisposisi::route('/{record}'),
            'edit' => Pages\EditDisposisi::route('/{record}/edit'),
        ];
    }
}
