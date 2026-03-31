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
use Illuminate\Support\Facades\Auth;

use BackedEnum;
use UnitEnum;

class SuratMasukResource extends Resource
{
    protected static ?string $model = SuratMasuk::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static UnitEnum|string|null $navigationGroup = 'Persuratan';

    protected static ?string $navigationLabel = 'Surat Masuk';

    protected static ?string $modelLabel = 'Surat Masuk';

    protected static ?string $pluralModelLabel = 'Surat Masuk';

    protected static ?int $navigationSort = 1;


    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Informasi Surat')
                    ->schema([
                        Forms\Components\TextInput::make('nomor_surat')
                            ->required()
                            ->maxLength(100)
                            ->label('Nomor Surat'),
                        Forms\Components\TextInput::make('nomor_agenda')
                            ->maxLength(50)
                            ->label('Nomor Agenda')
                            ->helperText('Otomatis jika kosong')
                            ->disabled(fn(string $operation): bool => $operation === 'edit'),
                        Forms\Components\DatePicker::make('tanggal_surat')
                            ->required()
                            ->label('Tanggal Surat')
                            ->default(now()),
                        Forms\Components\DatePicker::make('tanggal_terima')
                            ->required()
                            ->label('Tanggal Diterima')
                            ->default(now()),
                    ])->columns(2),

                Section::make('Pengirim')
                    ->schema([
                        Forms\Components\TextInput::make('pengirim')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Pengirim'),
                        Forms\Components\Textarea::make('alamat_pengirim')
                            ->label('Alamat Pengirim')
                            ->rows(2)
                            ->placeholder('Alamat pengirim (opsional)'),
                    ])->columns(2),

                Section::make('Detail Surat')
                    ->schema([
                        Forms\Components\Textarea::make('perihal')
                            ->required()
                            ->label('Perihal')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('sifat_surat')
                            ->options([
                                'biasa' => 'Biasa',
                                'penting' => 'Penting',
                                'rahasia' => 'Rahasia',
                                'sangat_rahasia' => 'Sangat Rahasia',
                            ])
                            ->label('Sifat Surat')
                            ->default('biasa'),
                        Forms\Components\Select::make('prioritas')
                            ->options([
                                'rendah' => 'Rendah',
                                'sedang' => 'Sedang',
                                'tinggi' => 'Tinggi',
                                'segera' => 'Segera',
                            ])
                            ->label('Prioritas')
                            ->default('sedang'),
                        Forms\Components\Select::make('penerima')
                            ->label('Penerima')
                            ->relationship('penerimaUser', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih penerima'),
                    ])->columns(2),

                Section::make('Lampiran & Keterangan')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('File Surat (PDF/Scan)')
                            ->directory('surat-masuk')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(10240)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->placeholder('Keterangan tambahan (opsional)')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Hidden::make('created_by')
                    ->default(Auth::id()),
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
                Tables\Columns\TextColumn::make('tanggal_terima')
                    ->label('Tgl. Terima')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pengirim')
                    ->label('Pengirim')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('perihal')
                    ->label('Perihal')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn($record) => $record->perihal),
                Tables\Columns\TextColumn::make('sifat_surat')
                    ->label('Sifat')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'biasa' => 'gray',
                        'penting' => 'warning',
                        'rahasia' => 'danger',
                        'sangat_rahasia' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'biasa' => 'Biasa',
                        'penting' => 'Penting',
                        'rahasia' => 'Rahasia',
                        'sangat_rahasia' => 'Sangat Rahasia',
                        default => $state ?? '-',
                    }),
                Tables\Columns\TextColumn::make('prioritas')
                    ->label('Prioritas')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'rendah' => 'gray',
                        'sedang' => 'info',
                        'tinggi' => 'warning',
                        'segera' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'rendah' => 'Rendah',
                        'sedang' => 'Sedang',
                        'tinggi' => 'Tinggi',
                        'segera' => 'Segera',
                        default => $state ?? '-',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('penerimaUser.name')
                    ->label('Penerima')
                    ->default('-')
                    ->toggleable(),
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
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Dicatat Oleh')
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
                Tables\Filters\SelectFilter::make('sifat_surat')
                    ->options([
                        'biasa' => 'Biasa',
                        'penting' => 'Penting',
                        'rahasia' => 'Rahasia',
                        'sangat_rahasia' => 'Sangat Rahasia',
                    ])
                    ->label('Sifat Surat'),
                Tables\Filters\SelectFilter::make('prioritas')
                    ->options([
                        'rendah' => 'Rendah',
                        'sedang' => 'Sedang',
                        'tinggi' => 'Tinggi',
                        'segera' => 'Segera',
                    ])
                    ->label('Prioritas'),
                Tables\Filters\SelectFilter::make('penerima')
                    ->relationship('penerimaUser', 'name')
                    ->label('Penerima')
                    ->preload(),
                Tables\Filters\TrashedFilter::make(),
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
                        Forms\Components\Select::make('tembusan_user_ids')
                            ->label('Tembusan (Opsional)')
                            ->options(fn() => \App\Models\User::pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->helperText('Pilih user untuk tembusan (hanya mengetahui)'),
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
                            'dari_user_id' => Auth::id(),
                            'ke_user_id' => $data['ke_user_id'] ?? null,
                            'ke_unit_id' => $data['ke_unit_id'] ?? null,
                            'instruksi' => $data['instruksi'],
                            'catatan' => $data['catatan'] ?? null,
                            'batas_waktu' => $data['batas_waktu'] ?? null,
                            'status' => 'belum_diproses',
                        ]);

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

                        // Logika Tembusan Pilihan
                        if (!empty($data['tembusan_user_ids'])) {
                            foreach ($data['tembusan_user_ids'] as $userId) {
                                Disposisi::create([
                                    'surat_masuk_id' => $record->id,
                                    'dari_user_id' => Auth::id(),
                                    'ke_user_id' => $userId,
                                    'ke_unit_id' => null,
                                    'instruksi' => 'Mengetahui (Tembusan). Instruksi utama: ' . $data['instruksi'],
                                    'status' => 'selesai',
                                    'is_tembusan' => true,
                                ]);
                                
                                $tempUser = \App\Models\User::find($userId);
                                if ($tempUser) {
                                    Notification::make()
                                        ->title('Tembusan Disposisi')
                                        ->body("Anda mendapat tembusan disposisi surat: {$record->perihal}")
                                        ->icon('heroicon-o-information-circle')
                                        ->iconColor('info')
                                        ->sendToDatabase($tempUser);
                                }
                            }
                        }

                        Notification::make()
                            ->title('Disposisi berhasil dibuat')
                            ->success()
                            ->send();
                    })
                    ->visible(function (SuratMasuk $record): bool {
                        $user = Auth::user();
                        if (!$user) {
                            return false;
                        }

                        if ($record->status === 'selesai') {
                            return false;
                        }

                        // Admin bebas.
                        if ($user->hasRole('admin')) {
                            return true;
                        }

                        if (!$user->hasRole('pimpinan')) {
                            return false;
                        }

                        // Hanya yang punya disposisi "tindak lanjut" (bukan tembusan) yang bisa membuat disposisi lanjut.
                        $unitId = $user->unit_kerja_id;

                        return $record->disposisis()
                            ->where('is_tembusan', false)
                            ->where('status', '!=', 'selesai')
                            ->where(function (Builder $q) use ($user, $unitId) {
                                $q->where('ke_user_id', $user->id);
                                if (!empty($unitId)) {
                                    $q->orWhere('ke_unit_id', $unitId);
                                }
                            })
                            ->exists();
                    }),
                \Filament\Actions\Action::make('tandaSelesai')
                    ->label('Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Tandai Selesai')
                    ->modalDescription('Apakah surat ini sudah selesai diproses?')
                    ->action(function (SuratMasuk $record) {
                        $record->update(['status' => 'selesai']);

                        $record->disposisis()->where('status', '!=', 'selesai')
                            ->update(['status' => 'selesai']);

                        Notification::make()
                            ->title('Surat ditandai selesai')
                            ->success()
                            ->send();
                    })
                    ->visible(function (SuratMasuk $record): bool {
                        $user = Auth::user();
                        if (!$user) {
                            return false;
                        }

                        if ($record->status === 'selesai') {
                            return false;
                        }

                        // Admin bebas.
                        if ($user->hasRole('admin')) {
                            return true;
                        }

                        if (!$user->hasRole('pimpinan')) {
                            return false;
                        }

                        $unitId = $user->unit_kerja_id;

                        return $record->disposisis()
                            ->where('is_tembusan', false)
                            ->where('status', '!=', 'selesai')
                            ->where(function (Builder $q) use ($user, $unitId) {
                                $q->where('ke_user_id', $user->id);
                                if (!empty($unitId)) {
                                    $q->orWhere('ke_unit_id', $unitId);
                                }
                            })
                            ->exists();
                    }),
                \Filament\Actions\Action::make('arsipkan')
                    ->label('Arsipkan')
                    ->icon('heroicon-o-archive-box-arrow-down')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Arsipkan Surat')
                    ->modalDescription('Surat akan dipindahkan ke arsip.')
                    ->action(function (SuratMasuk $record) {
                        $record->update(['archived_at' => now()]);
                        Notification::make()->title('Surat diarsipkan')->success()->send();
                    })
                    ->visible(function (SuratMasuk $record): bool {
                        $user = Auth::user();
                        if (!$user) {
                            return false;
                        }

                        if ($record->status !== 'selesai' || $record->archived_at) {
                            return false;
                        }

                        if ($user->hasRole('admin')) {
                            return true;
                        }

                        if (!$user->hasRole('pimpinan')) {
                            return false;
                        }

                        // Hanya eksekutor yang berhak mengarsipkan.
                        $unitId = $user->unit_kerja_id;

                        return $record->disposisis()
                            ->where('is_tembusan', false)
                            ->where(function (Builder $q) use ($user, $unitId) {
                                $q->where('ke_user_id', $user->id);
                                if (!empty($unitId)) {
                                    $q->orWhere('ke_unit_id', $unitId);
                                }
                            })
                            ->exists();
                    }),
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
                \Filament\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                    \Filament\Actions\RestoreBulkAction::make(),
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->withTrashed();

        $user = Auth::user();
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // Admin & sekretaris melihat semua surat masuk (historis/operasional).
        if ($user->hasAnyRole(['admin', 'sekretaris'])) {
            return $query;
        }

        // Untuk pimpinan: hanya surat yang relevan (jadi penerima disposisi atau tembusan).
        $unitId = $user->unit_kerja_id;

        return $query->where(function (Builder $q) use ($user, $unitId) {
            $q->where('penerima', $user->id)
                ->orWhereHas('disposisis', function (Builder $d) use ($user) {
                    $d->where('ke_user_id', $user->id);
                })
                ->when(!empty($unitId), function (Builder $d) use ($unitId) {
                    $d->orWhereHas('disposisis', function (Builder $dx) use ($unitId) {
                        $dx->where('ke_unit_id', $unitId);
                    });
                });
        });
    }
}
