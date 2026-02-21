<?php

namespace App\Filament\Resources\DisposisiResource\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class BalasansRelationManager extends RelationManager
{
    protected static string $relationship = 'balasans';

    protected static ?string $title = 'Balasan Disposisi';

    protected static ?string $modelLabel = 'Balasan';

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('isi_balasan')
                    ->required()
                    ->label('Isi Balasan')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('isi_balasan')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengirim'),
                Tables\Columns\TextColumn::make('isi_balasan')
                    ->label('Balasan')
                    ->limit(60),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([
                \Filament\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
