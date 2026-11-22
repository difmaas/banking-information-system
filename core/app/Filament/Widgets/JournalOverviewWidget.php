<?php

namespace App\Filament\Widgets;

use App\Services\ReportService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class JournalOverviewWidget extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        return auth()->user()?->can('gls.view') ?? false;
    }

    protected function getStats(): array
    {
        $trial = app(ReportService::class)->trialBalance();

        return [
            Stat::make(__('Total debit'), 'Rp '.number_format($trial['total_debit'])),
            Stat::make(__('Total kredit'), 'Rp '.number_format($trial['total_credit'])),
            Stat::make(__('Status neraca'), $trial['balanced'] ? __('Seimbang') : __('Tidak seimbang')),
        ];
    }
}
