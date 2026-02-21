<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuratMasukResource\Pages;
use App\Filament\Resources\SuratMasukResource\RelationManagers;
use App\Models\Disposisi;
use App\Models\SuratMasuk;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class SuratMasukResource extends Resource
{
    protected static ?string $model = SuratMasuk::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-envelope';

    protected static string | UnitEnum | null $navigationGroup = 'Persuratan';

    protected static ?string $navigationLabel = 'Surat Masuk';

    protected static ?string $modelLabel = 'Surat Masuk';

    protected static ?string $pluralModelLabel = 'Surat Masuk';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'pimpinan', 'sekretaris']);
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Informasi Surat')
                    ->schema([
                        Forms\Components\TextInput::make('nomor_surat')
                            ->required()
                            ->maxLength(255)
                            ->label('Nomor Surat'),
                        Forms\Components\DatePicker::make('tanggal_surat')
                            ->required()
                            ->label('Tanggal Surat')
                            ->default(now()),
                        Forms\Components\DatePicker::make('tanggal_terima')
                            ->required()
                            ->label('Tanggal Terima')
                            ->default(now()),
                        Forms\Components\TextInput::make('pengirim')
                            ->required()
                            ->maxLength(255)
                            ->label('Pengirim'),
                    ])->columns(2),

                Section::make('Detail Surat')
                    ->schema([
                        Forms\Components\TextInput::make('perihal')
                            ->required()
                            ->maxLength(255)
                            ->label('Perihal')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('klasifikasi')
                            ->options([
                                'biasa' => 'Biasa',
                                'penting' => 'Penting',
                                'rahasia' => 'Rahasia',
                                'segera' => 'Segera',
                            ])
                            ->label('Klasifikasi')
                            ->default('biasa'),
                        Forms\Components\Select::make('unit_tujuan_id')
                            ->relationship('unitTujuan', 'nama')
                            ->searchable()
                            ->preload()
                            ->label('Unit Tujuan'),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('File Surat (PDF/Scan)')
                            ->directory('surat-masuk')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(10240)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Hidden::make('created_by')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_agenda')
                    ->label('No. Agenda')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_surat')
                    ->label('Tanggal Surat')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pengirim')
                    ->label('Pengirim')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('perihal')
                    ->label('Perihal')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn($record) => $record->perihal),
                Tables\Columns\TextColumn::make('klasifikasi')
                    ->label('Klasifikasi')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'biasa' => 'gray',
                        'penting' => 'warning',
                        'rahasia' => 'danger',
                        'segera' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'diterima' => 'gray',
                        'dibaca' => 'info',
                        'didisposisi' => 'warning',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'diterima' => 'Diterima',
                        'dibaca' => 'Dibaca',
                        'didisposisi' => 'Didisposisi',
                        'selesai' => 'Selesai',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('unitTujuan.nama')
                    ->label('Unit Tujuan')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tanggal_terima')
                    ->label('Tgl. Terima')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'diterima' => 'Diterima',
                        'dibaca' => 'Dibaca',
                        'didisposisi' => 'Didisposisi',
                        'selesai' => 'Selesai',
                    ])
                    ->label('Status'),
                Tables\Filters\SelectFilter::make('klasifikasi')
                    ->options([
                        'biasa' => 'Biasa',
                        'penting' => 'Penting',
                        'rahasia' => 'Rahasia',
                        'segera' => 'Segera',
                    ])
                    ->label('Klasifikasi'),
                Tables\Filters\SelectFilter::make('unit_tujuan_id')
                    ->relationship('unitTujuan', 'nama')
                    ->label('Unit Tujuan')
                    ->preload(),
            ])
            ->actions([
                \Filament\Actions\Action::make('buatDisposisi')
                    ->label('Disposisi')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->modalHeading('Buat Disposisi')
                    ->modalDescription(fn($record) => "Surat: {$record->nomor_surat} - {$record->perihal}")
                    ->form([
                        Forms\Components\Select::make('ke_user_id')
                            ->label('Tujuan (User)')
                            ->relationship('createdBy', 'name', fn(Builder $query) => $query)
                            ->options(fn() => \App\Models\User::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->helperText('Pilih user tujuan disposisi'),
                        Forms\Components\Select::make('ke_unit_id')
                            ->label('Tujuan (Unit Kerja)')
                            ->options(fn() => \App\Models\UnitKerja::pluck('nama', 'id'))
                            ->searchable()
                            ->preload()
                            ->helperText('Atau pilih unit kerja tujuan'),
                        Forms\Components\Textarea::make('instruksi')
                            ->required()
                            ->label('Instruksi')
                            ->placeholder('Tuliskan instruksi disposisi...')
                            ->rows(3),
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan')
                            ->placeholder('Catatan tambahan (opsional)')
                            ->rows(2),
                        Forms\Components\DatePicker::make('batas_waktu')
                            ->label('Batas Waktu')
                            ->minDate(now()),
                    ])
                    ->action(function (SuratMasuk $record, array $data) {
                        $disposisi = Disposisi::create([
                            'surat_masuk_id' => $record->id,
                            'dari_user_id' => auth()->id(),
                            'ke_user_id' => $data['ke_user_id'] ?? null,
                            'ke_unit_id' => $data['ke_unit_id'] ?? null,
                            'instruksi' => $data['instruksi'],
                            'catatan' => $data['catatan'] ?? null,
                            'batas_waktu' => $data['batas_waktu'] ?? null,
                            'status' => 'belum_diproses',
                        ]);

                        // Send notification to target user
                        if ($disposisi->ke_user_id) {
                            $targetUser = \App\Models\User::find($disposisi->ke_user_id);
                            if ($targetUser) {
                                Notification::make()
                                    ->title('Disposisi Baru')
                                    ->body("Anda menerima disposisi untuk surat: {$record->perihal}")
                                    ->icon('heroicon-o-paper-airplane')
                                    ->iconColor('warning')
                                    ->sendToDatabase($targetUser);
                            }
                        }

                        Notification::make()
                            ->title('Disposisi berhasil dibuat')
                            ->success()
                            ->send();
                    })
                    ->visible(fn($record) => $record->status !== 'selesai'),
                \Filament\Actions\Action::make('tandaSelesai')
                    ->label('Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Tandai Selesai')
                    ->modalDescription('Apakah surat ini sudah selesai diproses?')
                    ->action(function (SuratMasuk $record) {
                        $record->update(['status' => 'selesai']);

                        // Mark all dispositions as completed
                        $record->disposisis()->where('status', '!=', 'selesai')
                            ->update(['status' => 'selesai']);

                        Notification::make()
                            ->title('Surat ditandai selesai')
                            ->success()
                            ->send();
                    })
                    ->visible(fn($record) => $record->status !== 'selesai'),
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
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
            RelationManagers\DisposisisRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuratMasuks::route('/'),
            'create' => Pages\CreateSuratMasuk::route('/create'),
            'view' => Pages\ViewSuratMasuk::route('/{record}'),
            'edit' => Pages\EditSuratMasuk::route('/{record}/edit'),
        ];
    }
}
