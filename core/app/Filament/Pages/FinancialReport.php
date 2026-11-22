<?php

namespace App\Filament\Pages;

use App\Services\ReportService;
use BackedEnum;
use Carbon\CarbonInterface;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class FinancialReport extends Page
{
    protected string $view = 'filament.pages.financial-report';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Keuangan';

    public string $from = '';

    public string $to = '';

    public int $accountId = 0;

    public function mount(): void
    {
        $this->accountId = (int) ($this->accounts()->first()['id'] ?? 0);
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user?->can('reports.view') || $user?->can('gls.view') ?? false;
    }

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function accounts(): Collection
    {
        return app(ReportService::class)->accountOptions();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function ledger(): Collection
    {
        if ($this->accountId === 0) {
            return collect();
        }

        return app(ReportService::class)->generalLedger($this->accountId, $this->dateFrom(), $this->dateTo());
    }

    /**
     * @return array{rows: Collection<int, array<string, mixed>>, total_debit: float, total_credit: float, balanced: bool}
     */
    public function trialBalance(): array
    {
        return app(ReportService::class)->trialBalance($this->dateTo());
    }

    /**
     * @return array<string, float>
     */
    public function incomeStatement(): array
    {
        return app(ReportService::class)->incomeStatement($this->dateFrom(), $this->dateTo());
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
