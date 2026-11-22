<?php

namespace App\Filament\Widgets;

use App\Services\ReportService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CreditOverviewWidget extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->can('lns.view') ?? false;
    }

    protected function getStats(): array
    {
        $portfolio = app(ReportService::class)->portfolio();

        return [
            Stat::make(__('Total pengajuan'), (string) $portfolio->count()),
            Stat::make(__('Pinjaman aktif'), (string) $portfolio->where('status', 'Aktif')->count()),
            Stat::make(__('Total outstanding'), 'Rp '.number_format((float) $portfolio->sum('outstanding'))),
        ];
    }
}
