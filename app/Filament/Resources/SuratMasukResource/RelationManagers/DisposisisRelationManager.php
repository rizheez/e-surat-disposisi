<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuratMasukResource\RelationManagers;

use App\Models\Disposisi;
use App\Models\User;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

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
                    ->label('Batas Waktu'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('instruksi')
            ->modifyQueryUsing(function (Builder $query): Builder {
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
            })
            ->columns([
                Tables\Columns\TextColumn::make('dariUser.name')
                    ->label('Dari'),
                Tables\Columns\TextColumn::make('keUser.name')
                    ->label('Kepada')
                    ->default('-'),
                Tables\Columns\TextColumn::make('instruksi')
                    ->label('Instruksi')
                    ->limit(50),
                Tables\Columns\IconColumn::make('is_tembusan')
                    ->label('Tembusan')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('batas_waktu')
                    ->label('Batas Waktu')
                    ->date('d M Y')
                    ->default('-'),
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
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make()
                    ->visible(function (): bool {
                        $user = \Illuminate\Support\Facades\Auth::user();
                        if (! $user) {
                            return false;
                        }

                        // Admin bebas membuat disposisi.
                        if ($user->isAdminRole()) {
                            return true;
                        }

                        if (! $user->canManageDisposisi()) {
                            return false;
                        }

                        $owner = $this->getOwnerRecord(); // SuratMasuk
                        if (! $owner) {
                            return false;
                        }

                        // Hanya eksekutor (tindak lanjut, bukan tembusan) yang boleh membuat disposisi lanjut.
                        return $owner->disposisis()
                            ->where('is_tembusan', false)
                            ->where('status', '!=', 'selesai')
                            ->where('ke_user_id', $user->id)
                            ->exists();
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['dari_user_id'] = \Illuminate\Support\Facades\Auth::id();
                        $data['status'] = 'belum_diproses';

                        return $data;
                    })
                    ->after(function (\App\Models\Disposisi $record, array $data) {
                        if ($record->ke_user_id) {
                            $targetUser = User::find($record->ke_user_id);
                            if ($targetUser) {
                                Notification::make()
                                    ->title('Disposisi Baru')
                                    ->body("Anda menerima disposisi untuk surat: {$record->suratMasuk->perihal}")
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
                                    'parent_id' => $record->id,
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
                    }),
            ])
            ->actions([
                \Filament\Actions\Action::make('updateStatus')
                    ->label('Update Status')
                    ->icon('heroicon-o-arrow-path')
                    ->authorize('updateStatus')
                    ->visible(fn (Disposisi $record): bool => Auth::user()?->can('updateStatus', $record) ?? false)
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                'belum_diproses' => 'Belum Diproses',
                                'sedang_diproses' => 'Sedang Diproses',
                                'selesai' => 'Selesai',
                            ])
                            ->required()
                            ->live()
                            ->label('Status Baru'),
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan Penyelesaian')
                            ->placeholder('Tuliskan hasil tindak lanjut atau alasan disposisi dinyatakan selesai...')
                            ->visible(fn (Get $get): bool => $get('status') === 'selesai')
                            ->required(fn (Get $get): bool => $get('status') === 'selesai')
                            ->rows(4)
                            ->maxLength(1000),
                    ])
                    ->action(function (Disposisi $record, array $data) {
                        $record->update([
                            'status' => $data['status'],
                            'catatan' => $data['status'] === 'selesai'
                                ? $data['catatan']
                                : $record->catatan,
                        ]);
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
