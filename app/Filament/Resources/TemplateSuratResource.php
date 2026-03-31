<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateSuratResource\Pages;
use App\Models\TemplateSurat;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use UnitEnum;

class TemplateSuratResource extends Resource
{
    protected static ?string $model = TemplateSurat::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Template Surat';

    protected static ?string $modelLabel = 'Template Surat';

    protected static ?string $pluralModelLabel = 'Template Surat';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'sekretaris']);
    }

    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Template Surat')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Template')
                            ->placeholder('Contoh: Surat Undangan Rapat'),
                        Forms\Components\Select::make('unit_kerja_id')
                            ->relationship('unitKerja', 'nama')
                            ->searchable()
                            ->preload()
                            ->label('Unit Kerja')
                            ->placeholder('Semua unit kerja (opsional)'),
                    ])->columns(2)->columnSpanFull(),

                Section::make('Konten Template')
                    ->description('Tulis isi template surat. Isi ini akan otomatis ditambahkan saat memilih template di Surat Keluar.')
                    ->schema([
                        Forms\Components\RichEditor::make('isi_template')
                            ->required()
                            ->label('Isi Template'),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                Forms\Components\Hidden::make('created_by')
                    ->default(auth()->id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Template')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unitKerja.nama')
                    ->label('Unit Kerja')
                    ->default('Semua')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Dibuat Oleh')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('unit_kerja_id')
                    ->relationship('unitKerja', 'nama')
                    ->label('Unit Kerja')
                    ->preload(),
            ])
            ->actions([
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTemplateSurats::route('/'),
            'create' => Pages\CreateTemplateSurat::route('/create'),
            'view' => Pages\ViewTemplateSurat::route('/{record}'),
            'edit' => Pages\EditTemplateSurat::route('/{record}/edit'),
        ];
    }
}
