<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuratKeluarResource\Pages;
use App\Models\SuratKeluar;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class SuratKeluarResource extends Resource
{
    protected static ?string $model = SuratKeluar::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-paper-clip';

    protected static string | UnitEnum | null $navigationGroup = 'Persuratan';

    protected static ?string $navigationLabel = 'Surat Keluar';

    protected static ?string $modelLabel = 'Surat Keluar';

    protected static ?string $pluralModelLabel = 'Surat Keluar';

    protected static ?int $navigationSort = 2;

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
                            ->label('Nomor Surat')
                            ->helperText('Akan di-generate otomatis jika kosong')
                            ->disabled(fn(string $operation): bool => $operation === 'edit'),
                        Forms\Components\DatePicker::make('tanggal')
                            ->required()
                            ->label('Tanggal')
                            ->default(now()),
                        Forms\Components\TextInput::make('perihal')
                            ->required()
                            ->maxLength(255)
                            ->label('Perihal')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('tujuan')
                            ->required()
                            ->maxLength(255)
                            ->label('Tujuan'),
                        Forms\Components\Select::make('unit_kerja_id')
                            ->relationship('unitKerja', 'nama')
                            ->searchable()
                            ->preload()
                            ->label('Unit Kerja Pengirim'),
                    ])->columns(2),

                Section::make('Isi Surat')
                    ->schema([
                        Forms\Components\Select::make('template_surat_id')
                            ->relationship('templateSurat', 'nama')
                            ->searchable()
                            ->preload()
                            ->label('Template')
                            ->placeholder('Pilih template (opsional)'),
                        Forms\Components\RichEditor::make('isi_surat')
                            ->label('Isi Surat')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Lampiran')
                            ->directory('surat-keluar')
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->maxSize(10240)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Hidden::make('created_by')
                    ->default(auth()->id()),
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
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('perihal')
                    ->label('Perihal')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('tujuan')
                    ->label('Tujuan')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('unitKerja.nama')
                    ->label('Unit Kerja')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'review' => 'warning',
                        'approved' => 'success',
                        'sent' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'review' => 'Review',
                        'approved' => 'Approved',
                        'sent' => 'Terkirim',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Dibuat Oleh')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'review' => 'Review',
                        'approved' => 'Approved',
                        'sent' => 'Terkirim',
                    ])
                    ->label('Status'),
            ])
            ->actions([
                \Filament\Actions\Action::make('submitReview')
                    ->label('Submit Review')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (SuratKeluar $record) {
                        $record->update(['status' => 'review']);
                        Notification::make()
                            ->title('Surat disubmit untuk review')
                            ->success()
                            ->send();
                    })
                    ->visible(fn($record) => $record->status === 'draft'),
                \Filament\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (SuratKeluar $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                        ]);
                        Notification::make()
                            ->title('Surat telah diapprove')
                            ->success()
                            ->send();
                    })
                    ->visible(fn($record) => $record->status === 'review'),
                \Filament\Actions\Action::make('kirim')
                    ->label('Kirim')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function (SuratKeluar $record) {
                        $record->update(['status' => 'sent']);
                        Notification::make()
                            ->title('Surat telah dikirim')
                            ->success()
                            ->send();
                    })
                    ->visible(fn($record) => $record->status === 'approved'),
                \Filament\Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('Surat akan dikembalikan ke status draft.')
                    ->action(function (SuratKeluar $record) {
                        $record->update(['status' => 'draft', 'approved_by' => null]);
                        Notification::make()
                            ->title('Surat ditolak, dikembalikan ke draft')
                            ->warning()
                            ->send();
                    })
                    ->visible(fn($record) => $record->status === 'review'),
                \Filament\Actions\Action::make('downloadPdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->url(fn(SuratKeluar $record): string => route('pdf.surat-keluar', $record))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => in_array($record->status, ['approved', 'sent'])),
                \Filament\Actions\Action::make('previewPdf')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn(SuratKeluar $record): string => route('pdf.surat-keluar.preview', $record))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => in_array($record->status, ['approved', 'sent'])),
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make()
                    ->visible(fn($record) => in_array($record->status, ['draft'])),
                \Filament\Actions\DeleteAction::make()
                    ->visible(fn($record) => $record->status === 'draft'),
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
            //
        ];
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
}
