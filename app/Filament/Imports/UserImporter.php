<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Nama')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->example('Ahmad Sutanto')
                ->guess(['nama', 'nama_lengkap']),

            ImportColumn::make('email')
                ->label('Email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255'])
                ->example('ahmad@example.test'),

            ImportColumn::make('password')
                ->label('Password')
                ->requiredMappingForNewRecordsOnly()
                ->rules(['nullable', 'string', 'min:8'])
                ->sensitive()
                ->ignoreBlankState()
                ->fillRecordUsing(function (?string $state, User $record): void {
                    if (blank($state)) {
                        return;
                    }

                    $record->password = Hash::make($state);
                })
                ->example('password123'),

            ImportColumn::make('jabatan')
                ->label('Jabatan')
                ->rules(['nullable', 'string', 'max:255'])
                ->ignoreBlankState()
                ->example('Kepala Biro'),

            ImportColumn::make('unit_kerja')
                ->label('Unit Kerja')
                ->relationship('unitKerja', ['nama', 'kode'])
                ->rules(['nullable', 'string', 'max:255'])
                ->guess(['unit', 'unit kerja', 'kode_unit', 'kode unit'])
                ->example('BAAK'),

            ImportColumn::make('role')
                ->label('Role')
                ->requiredMapping()
                ->rules([
                    'required',
                    'string',
                    'exists:roles,name',
                ])
                ->fillRecordUsing(fn (): null => null)
                ->guess(['roles', 'peran', 'role utama'])
                ->example('staf'),
        ];
    }

    protected function afterSave(): void
    {
        if (blank($this->data['role'] ?? null)) {
            return;
        }

        $this->record->syncRoles([(string) $this->data['role']]);
    }

    public function getValidationMessages(): array
    {
        return [
            '*.exists' => ':attribute tidak ditemukan.',
        ];
    }

    public function getValidationAttributes(): array
    {
        return array_merge(parent::getValidationAttributes(), [
            'role' => 'Role',
        ]);
    }

    public function resolveRecord(): ?User
    {
        $email = Str::lower((string) ($this->data['email'] ?? ''));

        return User::firstOrNew(['email' => $email]);
    }

    protected function beforeValidate(): void
    {
        if ($this->record?->exists || filled($this->data['password'] ?? null)) {
            return;
        }

        Validator::validate(
            ['password' => null],
            ['password' => ['required']],
            ['password.required' => 'Password wajib diisi untuk user baru.'],
        );
    }

    protected function beforeFill(): void
    {
        $this->data['email'] = Str::lower((string) ($this->data['email'] ?? ''));
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import user selesai: '.$import->successful_rows.' baris berhasil.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.$failedRowsCount.' baris gagal dan bisa diunduh untuk diperbaiki.';
        }

        return $body;
    }
}
