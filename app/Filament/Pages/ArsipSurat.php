<?php

namespace App\Filament\Pages;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;

use UnitEnum;
use BackedEnum;

class ArsipSurat extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.arsip-surat';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-archive-box';

    protected static UnitEnum|string|null $navigationGroup = 'Persuratan';

    protected static ?string $navigationLabel = 'Arsip Surat';

    protected static ?string $title = 'Arsip Surat';

    protected static ?int $navigationSort = 5;

    public string $activeTab = 'masuk';

    public function table(Table $table): Table
    {
        if ($this->activeTab === 'keluar') {
            return $this->suratKeluarTable($table);
        }

        return $this->suratMasukTable($table);
    }

    protected function suratMasukTable(Table $table): Table
    {
        return $table
            ->query(SuratMasuk::query()->whereNotNull('archived_at'))
            ->columns([
                Tables\Columns\TextColumn::make('nomor_surat')
                    ->label('No. Surat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pengirim')
                    ->label('Pengirim')
                    ->searchable(),
                Tables\Columns\TextColumn::make('perihal')
                    ->label('Perihal')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_surat')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('archived_at')
                    ->label('Diarsipkan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('archived_at', 'desc')
            ->actions([
                \Filament\Actions\Action::make('unarchive')
                    ->label('Buka Arsip')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (SuratMasuk $record) {
                        $record->update(['archived_at' => null]);
                        Notification::make()->title('Surat dibuka dari arsip')->success()->send();
                    }),
                \Filament\Actions\Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->url(fn(SuratMasuk $record) => route('filament.admin.resources.surat-masuks.view', $record)),
            ])
            ->emptyStateHeading('Belum ada arsip surat masuk')
            ->emptyStateDescription('Arsipkan surat masuk yang sudah selesai diproses.')
            ->emptyStateIcon('heroicon-o-archive-box');
    }

    protected function suratKeluarTable(Table $table): Table
    {
        return $table
            ->query(SuratKeluar::query()->whereNotNull('archived_at'))
            ->columns([
                Tables\Columns\TextColumn::make('nomor_surat')
                    ->label('No. Surat')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tujuan')
                    ->label('Tujuan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('perihal')
                    ->label('Perihal')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_surat')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('archived_at')
                    ->label('Diarsipkan')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('archived_at', 'desc')
            ->actions([
                \Filament\Actions\Action::make('unarchive')
                    ->label('Buka Arsip')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (SuratKeluar $record) {
                        $record->update(['archived_at' => null]);
                        Notification::make()->title('Surat dibuka dari arsip')->success()->send();
                    }),
                \Filament\Actions\Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->url(fn(SuratKeluar $record) => route('filament.admin.resources.surat-keluars.view', $record)),
            ])
            ->emptyStateHeading('Belum ada arsip surat keluar')
            ->emptyStateDescription('Arsipkan surat keluar yang sudah terkirim.')
            ->emptyStateIcon('heroicon-o-archive-box');
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetTable();
    }
}
