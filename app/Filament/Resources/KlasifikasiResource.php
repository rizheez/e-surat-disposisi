<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KlasifikasiResource\Pages;
use App\Models\Klasifikasi;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class KlasifikasiResource extends Resource
{
    protected static ?string $model = Klasifikasi::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Klasifikasi Surat';

    protected static ?string $modelLabel = 'Klasifikasi';

    protected static ?string $pluralModelLabel = 'Klasifikasi Surat';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin']);
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Klasifikasi Surat')
                    ->schema([
                        Forms\Components\TextInput::make('kode')
                            ->required()
                            ->maxLength(10)
                            ->label('Kode')
                            ->placeholder('01.1, 02.3, ND, SPTDD, dll')
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Klasifikasi')
                            ->placeholder('Urusan Umum, Nota Dinas, dll'),
                        Forms\Components\Select::make('kategori')
                            ->options([
                                'internal' => 'Internal (01.x)',
                                'eksternal' => 'Eksternal (02.x)',
                                'khusus' => 'Jenis Surat Khusus',
                            ])
                            ->required()
                            ->default('internal')
                            ->label('Kategori'),
                        Forms\Components\TextInput::make('kode_surat')
                            ->maxLength(10)
                            ->label('Kode Nomor Surat')
                            ->placeholder('ND, SPTDD, S.Kep, dll')
                            ->helperText('Khusus jenis surat tertentu (Nota Dinas, SK, dll)'),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])->columns(2)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'internal' => 'info',
                        'eksternal' => 'success',
                        'khusus' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'internal' => 'Internal',
                        'eksternal' => 'Eksternal',
                        'khusus' => 'Khusus',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('kode_surat')
                    ->label('Kode Nomor')
                    ->default('-')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('surat_keluars_count')
                    ->counts('suratKeluars')
                    ->label('Surat Keluar')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('kode')
            ->filters([
                Tables\Filters\SelectFilter::make('kategori')
                    ->options([
                        'internal' => 'Internal',
                        'eksternal' => 'Eksternal',
                        'khusus' => 'Khusus',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKlasifikasis::route('/'),
            'create' => Pages\CreateKlasifikasi::route('/create'),
            'edit' => Pages\EditKlasifikasi::route('/{record}/edit'),
        ];
    }
}
