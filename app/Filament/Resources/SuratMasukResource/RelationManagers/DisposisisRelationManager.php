<?php

namespace App\Filament\Resources\SuratMasukResource\RelationManagers;

use App\Models\Disposisi;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DisposisisRelationManager extends RelationManager
{
    protected static string $relationship = 'disposisis';

    protected static ?string $title = 'Disposisi';

    protected static ?string $modelLabel = 'Disposisi';

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ke_user_id')
                    ->label('Tujuan (User)')
                    ->options(fn() => \App\Models\User::pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('ke_unit_id')
                    ->label('Tujuan (Unit Kerja)')
                    ->options(fn() => \App\Models\UnitKerja::pluck('nama', 'id'))
                    ->searchable()
                    ->preload(),
                Forms\Components\Textarea::make('instruksi')
                    ->required()
                    ->label('Instruksi')
                    ->rows(3),
                Forms\Components\Textarea::make('catatan')
                    ->label('Catatan')
                    ->rows(2),
                Forms\Components\DatePicker::make('batas_waktu')
                    ->label('Batas Waktu'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('instruksi')
            ->columns([
                Tables\Columns\TextColumn::make('dariUser.name')
                    ->label('Dari'),
                Tables\Columns\TextColumn::make('keUser.name')
                    ->label('Kepada')
                    ->default('-'),
                Tables\Columns\TextColumn::make('keUnit.nama')
                    ->label('Unit Tujuan')
                    ->default('-'),
                Tables\Columns\TextColumn::make('instruksi')
                    ->label('Instruksi')
                    ->limit(50),
                Tables\Columns\TextColumn::make('batas_waktu')
                    ->label('Batas Waktu')
                    ->date('d M Y')
                    ->default('-'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'belum_diproses' => 'danger',
                        'sedang_diproses' => 'warning',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'belum_diproses' => 'Belum Diproses',
                        'sedang_diproses' => 'Sedang Diproses',
                        'selesai' => 'Selesai',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['dari_user_id'] = auth()->id();
                        $data['status'] = 'belum_diproses';
                        return $data;
                    }),
            ])
            ->actions([
                \Filament\Actions\Action::make('updateStatus')
                    ->label('Update Status')
                    ->icon('heroicon-o-arrow-path')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                'belum_diproses' => 'Belum Diproses',
                                'sedang_diproses' => 'Sedang Diproses',
                                'selesai' => 'Selesai',
                            ])
                            ->required()
                            ->label('Status Baru'),
                    ])
                    ->action(function (Disposisi $record, array $data) {
                        $record->update(['status' => $data['status']]);
                        Notification::make()
                            ->title('Status disposisi diperbarui')
                            ->success()
                            ->send();
                    }),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
