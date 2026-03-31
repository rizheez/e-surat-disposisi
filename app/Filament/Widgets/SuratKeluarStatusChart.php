<?php

namespace App\Filament\Widgets;

use App\Models\SuratKeluar;
use Filament\Widgets\ChartWidget;

class SuratKeluarStatusChart extends ChartWidget
{
    protected ?string $heading = 'Status Surat Keluar';

    protected static ?int $sort = 3;

    protected ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $draft = SuratKeluar::where('status', 'draft')->count();
        $review = SuratKeluar::where('status', 'review')->count();
        $approved = SuratKeluar::where('status', 'approved')->count();
        $sent = SuratKeluar::where('status', 'sent')->count();

        return [
            'datasets' => [
                [
                    'data' => [$draft, $review, $approved, $sent],
                    'backgroundColor' => ['#94a3b8', '#f59e0b', '#10b981', '#6366f1'],
                    'borderColor' => ['#64748b', '#d97706', '#059669', '#4f46e5'],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Draft', 'Review', 'Disetujui', 'Terkirim'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
