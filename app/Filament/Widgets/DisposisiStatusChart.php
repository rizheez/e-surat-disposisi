<?php

namespace App\Filament\Widgets;

use App\Models\Disposisi;
use Filament\Widgets\ChartWidget;

class DisposisiStatusChart extends ChartWidget
{
    protected ?string $heading = 'Status Disposisi';

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $belum = Disposisi::where('status', 'belum_diproses')->count();
        $sedang = Disposisi::where('status', 'sedang_diproses')->count();
        $selesai = Disposisi::where('status', 'selesai')->count();

        return [
            'datasets' => [
                [
                    'data' => [$belum, $sedang, $selesai],
                    'backgroundColor' => ['#ef4444', '#f59e0b', '#10b981'],
                    'borderColor' => ['#dc2626', '#d97706', '#059669'],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Belum Diproses', 'Sedang Diproses', 'Selesai'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
