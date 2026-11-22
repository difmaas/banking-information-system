<?php

namespace App\Modules\Lns\Services;

use App\Models\SystemParameter;
use App\Modules\Cis\Services\CisService;
use App\Modules\Gls\Services\GlsService;
use App\Modules\Lns\Models\Akad;
use App\Modules\Lns\Models\Installment;
use App\Modules\Lns\Models\Loan;
use App\Modules\Lns\Models\LoanAnalysis;
use App\Modules\Lns\Models\LoanApproval;
use App\Modules\Lns\Models\LoanProduct;
use App\Modules\Lns\Models\Payment;
use App\Services\AuditLogger;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LnsService
{
    public function __construct(
        private readonly CisService $cis,
        private readonly GlsService $gls,
        private readonly AuditLogger $audit,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function products(): array
    {
        return LoanProduct::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get()
            ->map(fn (LoanProduct $product): array => [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'description' => $product->description,
                'min_amount' => $product->min_amount,
                'max_amount' => $product->max_amount,
                'min_tenor_months' => $product->min_tenor_months,
                'max_tenor_months' => $product->max_tenor_months,
                'interest_rate' => $product->interest_rate,
            ])
            ->all();
    }

    public function monthlyInstallment(float $amount, int $tenorMonths, float $interestRate): float
    {
        if ($tenorMonths <= 0) {
            throw new RuntimeException('Tenor must be greater than zero.');
        }

        $total = $amount + ($amount * $interestRate / 100 * $tenorMonths);

        return round($total / $tenorMonths, 2);
    }

    /**
     * @return array<int, string>
     */
    public function requiredDocuments(): array
    {
        $value = SystemParameter::value('required_documents', 'ktp,kk,slip_gaji');

        return array_values(array_filter(
            array_map('trim', explode(',', (string) $value)),
        ));
    }

    /**
     * @return array<string, mixed>
     */
    public function simulateInstallment(int $productId, float $amount, int $tenorMonths): array
    {
        $product = LoanProduct::query()->findOrFail($productId);

        if ($amount < (float) $product->min_amount || $amount > (float) $product->max_amount) {
            throw new RuntimeException('Amount is outside the product limit.');
        }

        if ($tenorMonths < $product->min_tenor_months || $tenorMonths > $product->max_tenor_months) {
            throw new RuntimeException('Tenor is outside the product limit.');
        }

        $monthly = $this->monthlyInstallment($amount, $tenorMonths, (float) $product->interest_rate);
        $total = round($monthly * $tenorMonths, 2);

        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'amount' => $amount,
            'tenor_months' => $tenorMonths,
            'interest_rate' => $product->interest_rate,
            'monthly_installment' => $monthly,
            'total_interest' => round($total - $amount, 2),
            'total_payment' => $total,
        ];
    }

    public function assertDocumentsComplete(int $channelUserId): void
    {
        $uploaded = collect($this->cis->documents($channelUserId))
            ->pluck('type')
            ->all();
        $missing = array_values(array_diff($this->requiredDocuments(), $uploaded));

        if ($missing !== []) {
            throw new RuntimeException('Dokumen wajib belum lengkap: '.implode(', ', $missing));
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createLoan(int $channelUserId, array $data): array
    {
        $customer = $this->cis->findByChannelUserId($channelUserId);
        $product = LoanProduct::query()->findOrFail((int) $data['product_id']);

        $amount = (float) $data['amount'];
        $tenor = (int) $data['tenor_months'];

        if ($amount < (float) $product->min_amount || $amount > (float) $product->max_amount) {
            throw new RuntimeException('Amount is outside the product limit.');
        }

        if ($tenor < $product->min_tenor_months || $tenor > $product->max_tenor_months) {
            throw new RuntimeException('Tenor is outside the product limit.');
        }

        $this->assertDocumentsComplete($channelUserId);

        $loan = DB::transaction(fn (): Loan => $customer->loans()->create([
            'loan_product_id' => $product->id,
            'amount' => $amount,
            'tenor_months' => $tenor,
            'interest_rate' => $product->interest_rate,
            'status' => 'Diajukan',
            'outstanding' => 0,
            'applied_at' => now(),
        ]));

        return $this->loanArray($loan->load('product'));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function loans(int $channelUserId): array
    {
        return Loan::query()
            ->whereHas('customer', fn ($query) => $query->where('channel_user_id', $channelUserId))
            ->with('product')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Loan $loan): array => $this->loanArray($loan))
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function loan(int $channelUserId, int $loanId): array
    {
        return $this->loanArray($this->loanForCustomer($channelUserId, $loanId)->load('product'));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function installments(int $channelUserId, int $loanId): array
    {
        $loan = $this->loanForCustomer($channelUserId, $loanId);

        return $loan->installments()
            ->orderBy('number')
            ->get()
            ->map(fn (Installment $installment): array => [
                'number' => $installment->number,
                'due_date' => $installment->due_date->toDateString(),
                'amount' => $installment->amount,
                'principal' => $installment->principal,
                'interest' => $installment->interest,
                'status' => $installment->status,
                'paid_at' => $installment->paid_at?->toISOString(),
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function payments(int $channelUserId, int $loanId): array
    {
        $loan = $this->loanForCustomer($channelUserId, $loanId);

        return $loan->payments()
            ->with('installment')
            ->orderByDesc('paid_at')
            ->get()
            ->map(fn (Payment $payment): array => [
                'id' => $payment->id,
                'installment_number' => $payment->installment?->number,
                'amount' => $payment->amount,
                'paid_at' => $payment->paid_at->toISOString(),
                'reference' => $payment->reference,
            ])
            ->all();
    }

    public function verify(int $loanId, int $officerId, string $decision, ?string $reason): void
    {
        $loan = Loan::query()->findOrFail($loanId);

        DB::transaction(function () use ($loan, $officerId, $decision, $reason): void {
            $loan->verifications()->create([
                'officer_id' => $officerId,
                'decision' => $decision,
                'reason' => $reason,
            ]);

            $loan->update(['status' => $decision === 'Diteruskan' ? 'Verifikasi' : 'Ditolak']);
        });

        $this->audit->log('loan.verified', Loan::class, $loan->id, [
            'decision' => $decision,
            'reason' => $reason,
        ], $officerId);
    }

    /**
     * Compute the four feasibility criteria without persisting an analysis.
     *
     * @return array<string, mixed>
     */
    public function analysisCriteria(int $loanId): array
    {
        $loan = Loan::query()->with(['customer.creditHistories'])->findOrFail($loanId);
        $customer = $loan->customer;
        $monthly = $this->monthlyInstallment(
            (float) $loan->amount,
            $loan->tenor_months,
            (float) $loan->interest_rate,
        );
        $maxRatio = (float) SystemParameter::value('installment_income_ratio_max', 0.3);
        $income = (float) ($customer->monthly_income ?? 0);

        return [
            'loan' => $loan,
            'monthly_installment' => $monthly,
            'collectibility_ok' => $customer->collectibility === 'Lancar',
            'employment_ok' => filled($customer->occupation),
            'income_ok' => $income > 0 && $monthly <= $income * $maxRatio,
            'credit_history_ok' => $customer->creditHistories->every(fn ($history): bool => $history->status === 'Lunas'),
        ];
    }

    /**
     * @param  array<string, mixed>  $survey
     * @return array<string, mixed>
     */
    public function analyze(int $loanId, int $analystId, array $survey): array
    {
        $criteria = $this->analysisCriteria($loanId);
        $loan = $criteria['loan'];
        $monthly = $criteria['monthly_installment'];
        $collectibilityOk = $criteria['collectibility_ok'];
        $employmentOk = $criteria['employment_ok'];
        $incomeOk = $criteria['income_ok'];
        $creditHistoryOk = $criteria['credit_history_ok'];
        $surveyFilled = filled($survey['survey_result'] ?? null);
        $recommendation = $collectibilityOk && $employmentOk && $incomeOk && $creditHistoryOk && $surveyFilled
            ? 'Layak'
            : 'Tidak Layak';

        $analysis = DB::transaction(function () use ($loan, $analystId, $survey, $collectibilityOk, $employmentOk, $incomeOk, $creditHistoryOk, $recommendation): LoanAnalysis {
            $analysis = $loan->analyses()->create([
                'analyst_id' => $analystId,
                'collectibility_ok' => $collectibilityOk,
                'employment_ok' => $employmentOk,
                'income_ok' => $incomeOk,
                'credit_history_ok' => $creditHistoryOk,
                'survey_result' => $survey['survey_result'] ?? null,
                'collateral' => $survey['collateral'] ?? null,
                'recommendation' => $recommendation,
            ]);

            $loan->update(['status' => 'Analisis']);

            return $analysis;
        });

        $this->audit->log('loan.analyzed', Loan::class, $loan->id, [
            'recommendation' => $recommendation,
            'collectibility_ok' => $collectibilityOk,
            'employment_ok' => $employmentOk,
            'income_ok' => $incomeOk,
            'credit_history_ok' => $creditHistoryOk,
        ], $analystId);

        return [
            'id' => $analysis->id,
            'collectibility_ok' => $analysis->collectibility_ok,
            'employment_ok' => $analysis->employment_ok,
            'income_ok' => $analysis->income_ok,
            'credit_history_ok' => $analysis->credit_history_ok,
            'monthly_installment' => $monthly,
            'recommendation' => $analysis->recommendation,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function approvalLevel(float $amount): array
    {
        $limit1 = (float) SystemParameter::value('approval_level_1_limit', 25000000);
        $limit2 = (float) SystemParameter::value('approval_level_2_limit', 50000000);

        if ($amount <= $limit1) {
            return ['level' => 1, 'required' => (int) SystemParameter::value('approval_level_1_approvals', 1), 'members' => 1];
        }

        if ($amount <= $limit2) {
            return ['level' => 2, 'required' => (int) SystemParameter::value('approval_level_2_approvals', 2), 'members' => 2];
        }

        return ['level' => 3, 'required' => (int) SystemParameter::value('approval_level_3_approvals', 2), 'members' => (int) SystemParameter::value('approval_level_3_members', 3)];
    }

    /**
     * @return array<string, mixed>
     */
    public function approve(int $loanId, int $approverId, string $decision): array
    {
        $loan = Loan::query()->findOrFail($loanId);
        $analysis = $loan->analyses()->latest('id')->first();

        if ($analysis?->analyst_id === $approverId) {
            throw new RuntimeException('Analis tidak boleh menjadi pemutus pengajuan yang dianalisisnya sendiri.');
        }

        $level = $this->approvalLevel((float) $loan->amount);

        if ($loan->approvals()->where('approver_id', $approverId)->exists()) {
            throw new RuntimeException('Pemutus sudah memberikan keputusan untuk pengajuan ini.');
        }

        $approval = DB::transaction(function () use ($loan, $approverId, $decision, $level): LoanApproval {
            $approval = $loan->approvals()->create([
                'approver_id' => $approverId,
                'level' => $level['level'],
                'decision' => $decision,
            ]);

            $approved = $loan->approvals()->where('decision', 'Setuju')->count();
            $rejected = $loan->approvals()->where('decision', 'Tolak')->count();

            if ($approved >= $level['required']) {
                $loan->update([
                    'status' => 'Disetujui',
                    'approved_at' => now(),
                ]);
            } elseif ($rejected > $level['members'] - $level['required']) {
                $loan->update(['status' => 'Ditolak']);
            }

            return $approval;
        });

        $this->audit->log('loan.approved', Loan::class, $loan->id, [
            'decision' => $decision,
            'level' => $approval->level,
            'status' => $loan->fresh()->status,
        ], $approverId);

        return [
            'id' => $approval->id,
            'level' => $approval->level,
            'decision' => $approval->decision,
            'status' => $loan->fresh()->status,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function recordAkad(int $loanId, array $data): array
    {
        $loan = Loan::query()->findOrFail($loanId);

        if ($loan->status !== 'Disetujui') {
            throw new RuntimeException('Akad hanya dapat dicatat untuk pengajuan yang disetujui.');
        }

        $akad = DB::transaction(function () use ($loan, $data): Akad {
            return $loan->akad()->updateOrCreate(
                ['loan_id' => $loan->id],
                [
                    'signer_name' => (string) $data['signer_name'],
                    'signed_at' => isset($data['signed_at']) ? Carbon::parse($data['signed_at']) : now(),
                    'signature_data' => $data['signature_data'] ?? null,
                    'note' => $data['note'] ?? null,
                ],
            );
        });

        $this->audit->log('loan.akad_recorded', Loan::class, $loan->id, [
            'akad_id' => $akad->id,
            'signer_name' => $akad->signer_name,
            'signed_at' => $akad->signed_at->toISOString(),
        ]);

        return [
            'id' => $akad->id,
            'loan_id' => $loan->id,
            'signer_name' => $akad->signer_name,
            'signed_at' => $akad->signed_at->toISOString(),
        ];
    }

    public function disburse(int $loanId): array
    {
        $loan = Loan::query()->with(['product', 'akad'])->findOrFail($loanId);

        if ($loan->status !== 'Disetujui') {
            throw new RuntimeException('Only approved loans can be disbursed.');
        }

        if ($loan->akad === null) {
            throw new RuntimeException('Akad belum dicatat. Catat akad terlebih dahulu sebelum pencairan.');
        }

        DB::transaction(function () use ($loan): void {
            $loan->update([
                'status' => 'Aktif',
                'outstanding' => $loan->amount,
                'disbursed_at' => now(),
            ]);

            $this->generateInstallments($loan);
            $this->gls->recordDisbursement($loan);
        });

        $this->audit->log('loan.disbursed', Loan::class, $loan->id, [
            'amount' => $loan->amount,
        ]);

        return $this->loanArray($loan->fresh()->load('product'));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function recordPayment(int $loanId, array $data): array
    {
        $loan = Loan::query()->with('product')->findOrFail($loanId);

        if ($loan->status !== 'Aktif') {
            throw new RuntimeException('Only active loans can receive payments.');
        }

        $installment = isset($data['installment_id'])
            ? $loan->installments()->findOrFail((int) $data['installment_id'])
            : $loan->installments()->where('status', 'Belum Lunas')->orderBy('number')->firstOrFail();

        if ($installment->status === 'Lunas') {
            throw new RuntimeException('Angsuran sudah lunas.');
        }

        $amount = (float) ($data['amount'] ?? $installment->amount);

        if ($amount < (float) $installment->amount) {
            throw new RuntimeException('Nominal pembayaran kurang dari tagihan angsuran.');
        }

        $payment = DB::transaction(function () use ($loan, $installment, $amount, $data): Payment {
            $reference = $data['reference'] ?? 'PAY-'.strtoupper(str()->random(10));

            $payment = $loan->payments()->create([
                'installment_id' => $installment->id,
                'amount' => $amount,
                'paid_at' => now(),
                'reference' => $reference,
            ]);

            $installment->update([
                'status' => 'Lunas',
                'paid_at' => now(),
            ]);

            $principal = max(0, $amount - (float) $installment->interest);
            $outstanding = max(0, (float) $loan->outstanding - $principal);
            $loan->update([
                'outstanding' => $outstanding,
                'status' => $outstanding <= 0 ? 'Lunas' : 'Aktif',
            ]);

            $this->gls->recordPayment($loan, $payment);

            return $payment;
        });

        $this->audit->log('loan.payment_recorded', Loan::class, $loan->id, [
            'payment_id' => $payment->id,
            'installment_id' => $installment->id,
            'amount' => $payment->amount,
            'reference' => $payment->reference,
        ]);

        return [
            'id' => $payment->id,
            'installment_number' => $installment->number,
            'amount' => $payment->amount,
            'paid_at' => $payment->paid_at->toISOString(),
            'reference' => $payment->reference,
        ];
    }

    private function loanForCustomer(int $channelUserId, int $loanId): Loan
    {
        return Loan::query()
            ->whereHas('customer', fn ($query) => $query->where('channel_user_id', $channelUserId))
            ->findOrFail($loanId);
    }

    /**
     * @return array<string, mixed>
     */
    private function loanArray(Loan $loan): array
    {
        return [
            'id' => $loan->id,
            'customer_id' => $loan->customer_id,
            'product' => $loan->product?->name,
            'product_id' => $loan->loan_product_id,
            'amount' => $loan->amount,
            'tenor_months' => $loan->tenor_months,
            'interest_rate' => $loan->interest_rate,
            'status' => $loan->status,
            'outstanding' => $loan->outstanding,
            'applied_at' => $loan->applied_at?->toDateString(),
            'approved_at' => $loan->approved_at?->toDateString(),
            'disbursed_at' => $loan->disbursed_at?->toDateString(),
        ];
    }

    private function generateInstallments(Loan $loan): void
    {
        if ($loan->installments()->exists()) {
            return;
        }

        $principal = round((float) $loan->amount / $loan->tenor_months, 2);
        $interest = round((float) $loan->amount * (float) $loan->interest_rate / 100, 2);

        for ($number = 1; $number <= $loan->tenor_months; $number++) {
            $loan->installments()->create([
                'number' => $number,
                'due_date' => now()->addMonths($number)->toDateString(),
                'amount' => $principal + $interest,
                'principal' => $principal,
                'interest' => $interest,
                'status' => 'Belum Lunas',
            ]);
        }
    }

    /**
     * @return array{module: string, name: string, status: string}
     */
    public function summary(): array
    {
        return [
            'module' => 'LNS',
            'name' => 'Loan Management System',
            'status' => 'active',
        ];
    }
}
