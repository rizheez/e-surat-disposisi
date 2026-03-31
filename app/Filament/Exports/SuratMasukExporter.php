<?php

namespace App\Filament\Exports;

use App\Models\SuratMasuk;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SuratMasukExporter extends Exporter
{
    protected static ?string $model = SuratMasuk::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nomor_agenda')
                ->label('No. Agenda'),
            ExportColumn::make('nomor_surat')
                ->label('No. Surat'),
            ExportColumn::make('tanggal_surat')
                ->label('Tanggal Surat'),
            ExportColumn::make('tanggal_terima')
                ->label('Tanggal Terima'),
            ExportColumn::make('pengirim')
                ->label('Pengirim'),
            ExportColumn::make('alamat_pengirim')
                ->label('Alamat Pengirim'),
            ExportColumn::make('perihal')
                ->label('Perihal'),
            ExportColumn::make('sifat_surat')
                ->label('Sifat Surat'),
            ExportColumn::make('status')
                ->label('Status'),
            ExportColumn::make('penerima.name')
                ->label('Penerima'),
            ExportColumn::make('created_at')
                ->label('Dibuat'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export surat masuk selesai. ' . number_format($export->successful_rows) . ' baris berhasil di-export.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal.';
        }

        return $body;
    }
}
