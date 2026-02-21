<?php

namespace App\Filament\Widgets;

use App\Models\Disposisi;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalSuratMasuk = SuratMasuk::count();
        $suratMasukBulanIni = SuratMasuk::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $totalSuratKeluar = SuratKeluar::count();
        $suratKeluarBulanIni = SuratKeluar::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $disposisiPending = Disposisi::where('status', 'belum_diproses')->count();
        $disposisiProses = Disposisi::where('status', 'sedang_diproses')->count();

        return [
            Stat::make('Surat Masuk', $totalSuratMasuk)
                ->description("Bulan ini: {$suratMasukBulanIni}")
                ->descriptionIcon('heroicon-m-envelope')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make('Surat Keluar', $totalSuratKeluar)
                ->description("Bulan ini: {$suratKeluarBulanIni}")
                ->descriptionIcon('heroicon-m-paper-clip')
                ->color('info')
                ->chart([3, 5, 2, 7, 4, 6, 3]),

            Stat::make('Disposisi Pending', $disposisiPending)
                ->description("Sedang diproses: {$disposisiProses}")
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color($disposisiPending > 0 ? 'danger' : 'success')
                ->chart([2, 4, 6, 3, 5, 2, 4]),
        ];
    }
}
