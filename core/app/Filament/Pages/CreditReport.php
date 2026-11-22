<?php

namespace App\Filament\Pages;

use App\Services\ReportService;
use BackedEnum;
use Carbon\CarbonInterface;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CreditReport extends Page
{
    protected string $view = 'filament.pages.credit-report';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Kredit';

    public string $from = '';

    public string $to = '';

    public string $status = '';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('reports.view') ?? false;
    }

    /**
     * @return array<string, float|int>
     */
    public function totals(): array
    {
        $portfolio = $this->portfolio();
        $received = $this->installments();

        return [
            'applications' => $portfolio->count(),
            'active' => $portfolio->where('status', 'Aktif')->count(),
            'plafond' => (float) $portfolio->sum('amount'),
            'outstanding' => (float) $portfolio->sum('outstanding'),
            'received' => (float) $received->sum('amount'),
        ];
    }

    /**
     * @return Collection<int, object>
     */
    public function applications(): Collection
    {
        return app(ReportService::class)->applicationsByStatus($this->dateFrom(), $this->dateTo());
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function portfolio(): Collection
    {
        $rows = app(ReportService::class)->portfolio($this->dateFrom(), $this->dateTo());

        if ($this->status !== '') {
            $rows = $rows->filter(fn (array $row): bool => ($row['status'] ?? '') === $this->status);
        }

        return $rows;
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function installments(): Collection
    {
        $rows = app(ReportService::class)->installmentsReceived($this->dateFrom(), $this->dateTo());

        if ($this->status !== '') {
            $rows = $rows->filter(fn (array $row): bool => ($row['status'] ?? '') === $this->status);
        }

        return $rows;
    }

    private function dateFrom(): ?CarbonInterface
    {
        return $this->from === '' ? null : Carbon::parse($this->from)->startOfDay();
    }

    private function dateTo(): ?CarbonInterface
    {
        return $this->to === '' ? null : Carbon::parse($this->to)->endOfDay();
    }
}
