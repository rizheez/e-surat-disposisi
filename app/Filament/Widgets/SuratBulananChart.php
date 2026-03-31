<?php

namespace App\Filament\Widgets;

use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SuratBulananChart extends ChartWidget
{
    protected ?string $heading = 'Statistik Surat per Bulan';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $months = collect();
        $masukData = collect();
        $keluarData = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->translatedFormat('M Y'));

            $masukData->push(
                SuratMasuk::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count()
            );

            $keluarData->push(
                SuratKeluar::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count()
            );
        }

        return [
            'datasets' => [
                [
                    'label' => 'Surat Masuk',
                    'data' => $masukData->toArray(),
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Surat Keluar',
                    'data' => $keluarData->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
