<?php

namespace App\Filament\Exports;

use App\Models\SuratKeluar;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SuratKeluarExporter extends Exporter
{
    protected static ?string $model = SuratKeluar::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('nomor_surat')
                ->label('No. Surat'),
            ExportColumn::make('nomor_agenda')
                ->label('No. Agenda'),
            ExportColumn::make('tanggal_surat')
                ->label('Tanggal Surat'),
            ExportColumn::make('tujuan')
                ->label('Tujuan'),
            ExportColumn::make('alamat_tujuan')
                ->label('Alamat Tujuan'),
            ExportColumn::make('perihal')
                ->label('Perihal'),
            ExportColumn::make('sifat_surat')
                ->label('Sifat Surat'),
            ExportColumn::make('status')
                ->label('Status'),
            ExportColumn::make('pembuat.name')
                ->label('Pembuat'),
            ExportColumn::make('penandatangan.name')
                ->label('Penandatangan'),
            ExportColumn::make('tanggal_kirim')
                ->label('Tanggal Kirim'),
            ExportColumn::make('created_at')
                ->label('Dibuat'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export surat keluar selesai. ' . number_format($export->successful_rows) . ' baris berhasil di-export.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal.';
        }

        return $body;
    }
}
