<?php

namespace App\Modules\Gls\Services;

use App\Modules\Gls\Models\ChartOfAccount;
use App\Modules\Gls\Models\Journal;
use App\Modules\Lns\Models\Loan;
use App\Modules\Lns\Models\Payment;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GlsService
{
    /**
     * @param  array<int, array{account_code: string, debit: float|int|string, credit: float|int|string}>  $lines
     */
    public function postJournal(string $reference, string $description, string $sourceType, int $sourceId, array $lines): Journal
    {
        $totalDebit = 0.0;
        $totalCredit = 0.0;

        foreach ($lines as $line) {
            $totalDebit += (float) $line['debit'];
            $totalCredit += (float) $line['credit'];
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw new RuntimeException('Journal is not balanced.');
        }

        return DB::transaction(function () use ($reference, $description, $sourceType, $sourceId, $lines): Journal {
            $journal = Journal::query()->create([
                'date' => now()->toDateString(),
                'reference' => $reference,
                'description' => $description,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
            ]);

            foreach ($lines as $line) {
                $account = ChartOfAccount::query()->where('code', $line['account_code'])->firstOrFail();
                $journal->lines()->create([
                    'account_id' => $account->id,
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                ]);
            }

            return $journal;
        });
    }

    public function recordDisbursement(Loan $loan): void
    {
        $this->postJournal(
            'DISB-'.$loan->id,
            'Pencairan kredit '.$loan->product->name,
            Loan::class,
            $loan->id,
            [
                ['account_code' => '1300', 'debit' => $loan->amount, 'credit' => 0],
                ['account_code' => '1000', 'debit' => 0, 'credit' => $loan->amount],
            ],
        );
    }

    public function recordPayment(Loan $loan, Payment $payment): void
    {
        $interest = $payment->installment?->interest ?? 0;
        $principal = (float) $payment->amount - (float) $interest;

        $this->postJournal(
            'PAY-'.$payment->id,
            'Pembayaran angsuran kredit '.$loan->id,
            Payment::class,
            $payment->id,
            [
                ['account_code' => '1000', 'debit' => $payment->amount, 'credit' => 0],
                ['account_code' => '1300', 'debit' => 0, 'credit' => $principal],
                ['account_code' => '4000', 'debit' => 0, 'credit' => $interest],
            ],
        );
    }

    /**
     * @return array{module: string, name: string, status: string}
     */
    public function summary(): array
    {
        return [
            'module' => 'GLS',
            'name' => 'General Ledger System',
            'status' => 'active',
        ];
    }
}
