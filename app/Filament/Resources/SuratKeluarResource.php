<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SuratKeluarResource\Pages;
use App\Models\GeneratedNomorSurat;
use App\Models\Klasifikasi;
use App\Models\SuratKeluar;
use App\Services\QrSignatureService;
use BackedEnum;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class SuratKeluarResource extends Resource
{
    protected static ?string $model = SuratKeluar::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-paper-airplane';

    protected static UnitEnum|string|null $navigationGroup = 'Persuratan';

    protected static ?string $navigationLabel = 'Surat Keluar';

    protected static ?string $modelLabel = 'Surat Keluar';

    protected static ?string $pluralModelLabel = 'Surat Keluar';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Surat Keluar')
                    ->schema([
                        // ── Mode Toggle ──
                        Forms\Components\Radio::make('metode')
                            ->label('Metode Pembuatan')
                            ->options([
                                'web' => 'Buat di Web - nomor surat otomatis',
                                'upload' => 'Upload File (.docx/.pdf) - pilih nomor dari Generate Nomor',
                            ])
                            ->default(fn (?SuratKeluar $record): string => self::getMetodeValue($record))
                            ->afterStateHydrated(function (Set $set, ?SuratKeluar $record): void {
                                $set('metode', self::getMetodeValue($record));
                            })
                            ->live()
                            ->dehydrated(false)
                            ->inline()
                            ->columnSpanFull(),

                        // ── Info Surat ──
                        Forms\Components\Placeholder::make('nomor_surat_otomatis')
                            ->label('Nomor Surat')
                            ->content('Otomatis di-generate saat disimpan')
                            ->visible(fn (Get $get): bool => $get('metode') === 'web'),
                        Forms\Components\Select::make('nomor_surat')
                            ->label('Nomor Surat')
                            ->options(fn (?SuratKeluar $record): array => self::getGeneratedNomorOptions($record))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                self::fillGeneratedNomorFields($state, $set);
                            })
                            ->helperText('Pilih nomor yang sudah dibuat di menu Generate Nomor.')
                            ->visible(fn (Get $get): bool => $get('metode') === 'upload')
                            ->dehydrated(fn (Get $get): bool => $get('metode') === 'upload')
                            ->required(fn (Get $get): bool => $get('metode') === 'upload'),
                        // Forms\Components\TextInput::make('nomor_agenda')
                        //     ->label('Nomor Agenda')
                        //     ->maxLength(50),
                        Forms\Components\Select::make('klasifikasi')
                            ->label('Klasifikasi / Jenis Surat')
                            ->options(
                                fn () => Klasifikasi::query()
                                    ->where('is_active', true)
                                    ->orderBy('kode')
                                    ->get()
                                    ->mapWithKeys(fn (Klasifikasi $klasifikasi): array => [
                                        $klasifikasi->id => "{$klasifikasi->kode} - {$klasifikasi->nama}",
                                    ])
                                    ->all()
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->placeholder('Pilih klasifikasi'),
                        Forms\Components\DatePicker::make('tanggal_surat')
                            ->required()
                            ->label('Tanggal Surat')
                            ->default(now()),
                        // Forms\Components\Select::make('surat_masuk_id')
                        //     ->label('Balasan Surat Masuk')
                        //     ->relationship('suratMasuk', 'nomor_surat')
                        //     ->searchable()
                        //     ->preload()
                        //     ->placeholder('Pilih jika ini balasan'),

                        // ── Tujuan ──
                        Forms\Components\TextInput::make('tujuan')
                            ->required()
                            ->maxLength(255)
                            ->label('Tujuan'),
                        Forms\Components\TextInput::make('alamat_tujuan')
                            ->label('Alamat Tujuan')
                            ->maxLength(255)
                            ->placeholder('Opsional'),

                        // ── Detail ──
                        Forms\Components\Textarea::make('perihal')
                            ->required()
                            ->label('Perihal')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('lampiran')
                            ->label('Lampiran')
                            ->maxLength(255)
                            ->placeholder('contoh: 1 (satu) berkas'),
                        Forms\Components\Select::make('sifat_surat')
                            ->options([
                                'biasa' => 'Biasa',
                                'penting' => 'Penting',
                                'rahasia' => 'Rahasia',
                                'sangat_rahasia' => 'Sangat Rahasia',
                            ])
                            ->label('Sifat Surat')
                            ->default('biasa'),

                        Forms\Components\Select::make('penandatangan_id')
                            ->label('Penandatangan')
                            ->relationship('penandatangan', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih penandatangan'),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(2)
                            ->placeholder('Catatan internal (opsional)'),

                        // ── Tembusan ──
                        Forms\Components\Repeater::make('tembusan')
                            ->label('Tembusan')
                            ->simple(
                                Forms\Components\TextInput::make('nama')
                                    ->label('Penerima Tembusan')
                                    ->placeholder('contoh: Pertinggal')
                                    ->required(),
                            )
                            ->addActionLabel('+ Tambah Tembusan')
                            ->defaultItems(0)
                            ->columnSpanFull()
                            ->collapsible()
                            ->reorderable(),
                    ])->columns(2)->columnSpanFull(),

                // ── Buat di Web (full width) ──
                Section::make('Isi Surat')
                    ->description('Tulis isi surat mulai dari "Kepada Yth." atau salam pembuka hingga salam penutup.')
                    ->schema([
                        Forms\Components\RichEditor::make('isi_surat')
                            ->label('Isi Surat'),
                    ])
                    ->columns(1)
                    ->columnSpanFull()
                    ->visible(fn (Get $get): bool => $get('metode') === 'web'),

                // ── Upload File ──
                Section::make('Upload Dokumen')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('File Surat (.docx / .pdf)')
                            ->directory('surat-keluar')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ])
                            ->maxSize(10240)
                            ->required(fn (Get $get): bool => $get('metode') === 'upload')
                            ->helperText('Maks. 10MB'),
                    ])
                    ->columnSpanFull()
                    ->visible(fn (Get $get): bool => $get('metode') === 'upload'),

                Forms\Components\Hidden::make('pembuat_id')
                    ->default(Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('tanggal_surat')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('perihal')
                    ->label('Perihal')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->perihal),
                Tables\Columns\TextColumn::make('tujuan')
                    ->label('Tujuan')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\IconColumn::make('file_path')
                    ->label('File')
                    ->boolean()
                    ->trueIcon('heroicon-o-document')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('info')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sifat_surat')
                    ->label('Sifat')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'biasa' => 'gray',
                        'penting' => 'warning',
                        'rahasia', 'sangat_rahasia' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'biasa' => 'Biasa',
                        'penting' => 'Penting',
                        'rahasia' => 'Rahasia',
                        'sangat_rahasia' => 'Sangat Rahasia',
                        default => $state ?? '-',
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'review' => 'warning',
                        'approved' => 'success',
                        'sent' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'review' => 'Review',
                        'approved' => 'Disetujui',
                        'sent' => 'Terkirim',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('pembuat.name')
                    ->label('Pembuat')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('penandatangan.name')
                    ->label('Penandatangan')
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tanggal_kirim')
                    ->label('Tgl. Kirim')
                    ->date('d M Y')
                    ->default('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'review' => 'Review',
                        'approved' => 'Disetujui',
                        'sent' => 'Terkirim',
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                \Filament\Actions\Action::make('submitReview')
                    ->label('Submit Review')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (SuratKeluar $record) {
                        $record->update(['status' => 'review']);
                        Notification::make()->title('Surat disubmit untuk review')->success()->send();
                    })
                    ->visible(
                        fn ($record) => $record->status === 'draft'
                            && (Auth::id() === $record->pembuat_id || Auth::user()?->hasRole('admin'))
                    ),
                \Filament\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Setujui surat ini? QR code tanda tangan digital akan otomatis dibuat.')
                    ->action(function (SuratKeluar $record) {
                        // Generate QR token if not yet
                        if (! $record->qr_token) {
                            $service = new QrSignatureService;
                            $record->qr_token = $service->generateToken();
                        }
                        $record->status = 'approved';
                        $record->approved_at = now();
                        $record->save();
                        Notification::make()->title('Surat disetujui & QR dibuat')->success()->send();
                    })
                    ->visible(
                        fn ($record) => $record->status === 'review'
                            && Auth::id() === $record->penandatangan_id
                    ),
                \Filament\Actions\Action::make('kirim')
                    ->label('Kirim')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function (SuratKeluar $record) {
                        $record->update(['status' => 'sent', 'tanggal_kirim' => now()]);
                        Notification::make()->title('Surat telah dikirim')->success()->send();
                    })
                    ->visible(fn ($record) => $record->status === 'approved'),
                \Filament\Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('Surat akan dikembalikan ke status draft.')
                    ->action(function (SuratKeluar $record) {
                        $record->update(['status' => 'draft', 'penandatangan_id' => null]);
                        Notification::make()->title('Surat ditolak, dikembalikan ke draft')->warning()->send();
                    })
                    ->visible(
                        fn ($record) => $record->status === 'review'
                            && Auth::id() === $record->penandatangan_id
                    ),
                \Filament\Actions\Action::make('downloadPdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->url(fn (SuratKeluar $record): string => route('pdf.surat-keluar', $record))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => in_array($record->status, ['approved', 'sent']) && $record->isi_surat),
                \Filament\Actions\Action::make('downloadFile')
                    ->label('File')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->url(fn (SuratKeluar $record): string => route('surat-keluar.file.download', $record))
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => filled($record->file_path)),
                \Filament\Actions\Action::make('arsipkan')
                    ->label('Arsipkan')
                    ->icon('heroicon-o-archive-box-arrow-down')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalDescription('Surat akan dipindahkan ke arsip.')
                    ->action(function (SuratKeluar $record) {
                        $record->update(['archived_at' => now()]);
                        Notification::make()->title('Surat diarsipkan')->success()->send();
                    })
                    ->visible(fn ($record) => $record->status === 'sent' && ! $record->archived_at),
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->status === 'draft'),
                \Filament\Actions\DeleteAction::make()
                    ->visible(fn ($record) => $record->status === 'draft'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuratKeluars::route('/'),
            'create' => Pages\CreateSuratKeluar::route('/create'),
            'view' => Pages\ViewSuratKeluar::route('/{record}'),
            'edit' => Pages\EditSuratKeluar::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withTrashed();
    }

    private static function getMetodeValue(?SuratKeluar $record = null): string
    {
        return filled($record?->file_path) ? 'upload' : 'web';
    }

    private static function getGeneratedNomorOptions(?SuratKeluar $record = null): array
    {
        return GeneratedNomorSurat::query()
            ->where(function (Builder $query) use ($record): void {
                $query->where('status', 'reserved');

                if (filled($record?->nomor_surat)) {
                    $query->orWhere('nomor_surat', $record->nomor_surat);
                }
            })
            ->latest()
            ->get()
            ->mapWithKeys(fn (GeneratedNomorSurat $nomor): array => [
                $nomor->nomor_surat => "{$nomor->nomor_surat} - {$nomor->perihal} - {$nomor->tujuan}",
            ])
            ->all();
    }

    private static function fillGeneratedNomorFields(?string $nomorSurat, Set $set): void
    {
        if (blank($nomorSurat)) {
            return;
        }

        $generatedNomor = GeneratedNomorSurat::query()
            ->where('nomor_surat', $nomorSurat)
            ->first();

        if (! $generatedNomor) {
            return;
        }

        $set('tanggal_surat', $generatedNomor->tanggal_surat?->format('Y-m-d'));
        $set('klasifikasi', $generatedNomor->klasifikasi);
        $set('tujuan', $generatedNomor->tujuan);
        $set('perihal', $generatedNomor->perihal);
        $set('sifat_surat', $generatedNomor->sifat_surat);
        // $set('keterangan', $generatedNomor->keterangan);
    }
}
