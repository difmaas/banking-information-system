<?php

namespace App\Services;

use App\Modules\Gls\Models\ChartOfAccount;
use App\Modules\Gls\Models\JournalLine;
use App\Modules\Lns\Models\Loan;
use App\Modules\Lns\Models\Payment;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * @return Collection<int, object>
     */
    public function applicationsByStatus(?CarbonInterface $from = null, ?CarbonInterface $to = null): Collection
    {
        return Loan::query()
            ->when($from, fn ($query) => $query->whereDate('applied_at', '>=', $from->toDateString()))
            ->when($to, fn ($query) => $query->whereDate('applied_at', '<=', $to->toDateString()))
            ->selectRaw('status, COUNT(*) as total, SUM(amount) as nominal')
            ->groupBy('status')
            ->orderBy('status')
            ->get();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function portfolio(?CarbonInterface $from = null, ?CarbonInterface $to = null): Collection
    {
        return Loan::query()
            ->with(['customer', 'product'])
            ->when($from, fn ($query) => $query->whereDate('applied_at', '>=', $from->toDateString()))
            ->when($to, fn ($query) => $query->whereDate('applied_at', '<=', $to->toDateString()))
            ->orderByDesc('id')
            ->get()
            ->map(fn (Loan $loan): array => [
                'id' => $loan->id,
                'customer' => $loan->customer?->name ?? '-',
                'product' => $loan->product?->name ?? '-',
                'amount' => (float) $loan->amount,
                'outstanding' => (float) $loan->outstanding,
                'status' => $loan->status,
                'collectibility' => $loan->customer?->collectibility ?? '-',
                'applied_at' => $loan->applied_at?->toDateString() ?? '-',
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function installmentsReceived(?CarbonInterface $from = null, ?CarbonInterface $to = null): Collection
    {
        return Payment::query()
            ->with(['loan.customer', 'installment'])
            ->when($from, fn ($query) => $query->whereDate('paid_at', '>=', $from->toDateString()))
            ->when($to, fn ($query) => $query->whereDate('paid_at', '<=', $to->toDateString()))
            ->orderByDesc('paid_at')
            ->get()
            ->map(fn (Payment $payment): array => [
                'reference' => $payment->reference,
                'customer' => $payment->loan?->customer?->name ?? '-',
                'product' => $payment->loan?->product?->name ?? '-',
                'installment' => $payment->installment?->number ?? '-',
                'amount' => (float) $payment->amount,
                'paid_at' => $payment->paid_at->toDateString(),
            ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function generalLedger(int $accountId, ?CarbonInterface $from = null, ?CarbonInterface $to = null): Collection
    {
        $account = ChartOfAccount::query()->findOrFail($accountId);
        $balance = 0.0;

        return JournalLine::query()
            ->select('journal_lines.*')
            ->join('journals', 'journals.id', '=', 'journal_lines.journal_id')
            ->where('journal_lines.account_id', $accountId)
            ->when($from, fn ($query) => $query->whereDate('journals.date', '>=', $from->toDateString()))
            ->when($to, fn ($query) => $query->whereDate('journals.date', '<=', $to->toDateString()))
            ->orderBy('journals.date')
            ->orderBy('journals.id')
            ->orderBy('journal_lines.id')
            ->with('journal')
            ->get()
            ->map(function (JournalLine $line) use ($account, &$balance): array {
                $debit = (float) $line->debit;
                $credit = (float) $line->credit;
                $balance += $account->normal_balance === 'Debit'
                    ? $debit - $credit
                    : $credit - $debit;

                return [
                    'date' => $line->journal?->date?->toDateString() ?? '-',
                    'reference' => $line->journal?->reference ?? '-',
                    'description' => $line->journal?->description ?? '-',
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $balance,
                ];
            });
    }

    /**
     * @return array{rows: Collection<int, array<string, mixed>>, total_debit: float, total_credit: float, balanced: bool}
     */
    public function trialBalance(?CarbonInterface $asOf = null): array
    {
        $rows = ChartOfAccount::query()
            ->orderBy('code')
            ->get()
            ->map(function (ChartOfAccount $account) use ($asOf): array {
                $query = JournalLine::query()->where('account_id', $account->id);

                if ($asOf !== null) {
                    $query->whereHas('journal', fn ($q) => $q->whereDate('date', '<=', $asOf->toDateString()));
                }

                $debit = (float) (clone $query)->sum('debit');
                $credit = (float) (clone $query)->sum('credit');
                $net = $debit - $credit;

                return [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'debit' => $debit > $credit ? $net : 0.0,
                    'credit' => $credit > $debit ? -$net : 0.0,
                ];
            });

        $totalDebit = (float) $rows->sum('debit');
        $totalCredit = (float) $rows->sum('credit');

        return [
            'rows' => $rows,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'balanced' => abs($totalDebit - $totalCredit) < 0.01,
        ];
    }

    /**
     * @return array<string, float>
     */
    public function incomeStatement(?CarbonInterface $from = null, ?CarbonInterface $to = null): array
    {
        $query = JournalLine::query()
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'journal_lines.account_id')
            ->join('journals', 'journals.id', '=', 'journal_lines.journal_id')
            ->when($from, fn ($q) => $q->whereDate('journals.date', '>=', $from->toDateString()))
            ->when($to, fn ($q) => $q->whereDate('journals.date', '<=', $to->toDateString()));

        $revenue = (float) (clone $query)->where('chart_of_accounts.type', 'Revenue')->sum('journal_lines.credit')
            - (float) (clone $query)->where('chart_of_accounts.type', 'Revenue')->sum('journal_lines.debit');

        $expense = (float) (clone $query)->where('chart_of_accounts.type', 'Expense')->sum('journal_lines.debit')
            - (float) (clone $query)->where('chart_of_accounts.type', 'Expense')->sum('journal_lines.credit');

        $balances = $this->trialBalance($to);

        $cashRow = $balances['rows']->firstWhere('code', '1000');
        $receivableRow = $balances['rows']->firstWhere('code', '1300');

        return [
            'revenue' => $revenue,
            'expense' => $expense,
            'net_income' => $revenue - $expense,
            'cash' => ((float) ($cashRow['debit'] ?? 0)) - ((float) ($cashRow['credit'] ?? 0)),
            'receivable' => ((float) ($receivableRow['debit'] ?? 0)) - ((float) ($receivableRow['credit'] ?? 0)),
        ];
    }

    /**
     * @return Collection<int, array{id: int, name: string}>
     */
    public function accountOptions(): Collection
    {
        return ChartOfAccount::query()
            ->orderBy('code')
            ->get()
            ->map(fn (ChartOfAccount $account): array => [
                'id' => $account->id,
                'name' => $account->code.' - '.$account->name,
            ]);
    }
}
