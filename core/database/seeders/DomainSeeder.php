<?php

namespace Database\Seeders;

use App\Models\SystemParameter;
use App\Modules\Cis\Models\CreditHistory;
use App\Modules\Cis\Models\Customer;
use App\Modules\Cis\Models\CustomerDocument;
use App\Modules\Gls\Models\ChartOfAccount;
use App\Modules\Gls\Models\Journal;
use App\Modules\Gls\Models\JournalLine;
use App\Modules\Lns\Models\Akad;
use App\Modules\Lns\Models\Installment;
use App\Modules\Lns\Models\Loan;
use App\Modules\Lns\Models\LoanProduct;
use App\Modules\Lns\Models\Payment;
use Illuminate\Database\Seeder;

class DomainSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedParameters();
        $this->seedChartOfAccounts();
        $product = $this->seedLoanProduct();
        $this->seedCustomer($product);
    }

    private function seedParameters(): void
    {
        $parameters = [
            'approval_level_1_limit' => ['25000000', 'Batas plafon persetujuan tingkat 1.'],
            'approval_level_2_limit' => ['50000000', 'Batas plafon persetujuan tingkat 2.'],
            'approval_level_1_approvals' => ['1', 'Jumlah setuju minimal tingkat 1.'],
            'approval_level_2_approvals' => ['2', 'Jumlah setuju minimal tingkat 2.'],
            'approval_level_3_approvals' => ['2', 'Jumlah setuju minimal tingkat 3.'],
            'approval_level_3_members' => ['3', 'Jumlah anggota komite tingkat 3.'],
            'installment_income_ratio_max' => ['0.3', 'Rasio angsuran maksimal terhadap penghasilan.'],
            'required_documents' => ['ktp,kk,slip_gaji', 'Dokumen wajib sebelum pengajuan kredit dikirim.'],
        ];

        foreach ($parameters as $key => [$value, $description]) {
            SystemParameter::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'description' => $description],
            );
        }
    }

    private function seedChartOfAccounts(): void
    {
        $accounts = [
            ['1000', 'Kas', 'Asset', 'Debit'],
            ['1300', 'Piutang Kredit', 'Asset', 'Debit'],
            ['4000', 'Pendapatan Bunga', 'Revenue', 'Kredit'],
        ];

        foreach ($accounts as [$code, $name, $type, $normalBalance]) {
            ChartOfAccount::query()->firstOrCreate(
                ['code' => $code],
                ['name' => $name, 'type' => $type, 'normal_balance' => $normalBalance],
            );
        }
    }

    private function seedLoanProduct(): LoanProduct
    {
        return LoanProduct::query()->firstOrCreate(
            ['code' => 'KM-01'],
            [
                'name' => 'Kredit Multiguna',
                'description' => 'Kredit konsumtif dengan bunga flat.',
                'min_amount' => 1000000,
                'max_amount' => 100000000,
                'min_tenor_months' => 6,
                'max_tenor_months' => 60,
                'interest_rate' => 1.25,
                'is_active' => true,
            ],
        );
    }

    private function seedCustomer(LoanProduct $product): void
    {
        $customer = Customer::query()->firstOrCreate(
            ['channel_user_id' => 1],
            [
                'nik' => '3204012345678901',
                'name' => 'Nasabah Demo',
                'email' => 'nasabah@bis.test',
                'phone' => '081234567890',
                'address' => 'Jl. Contoh No. 1, Medan',
                'occupation' => 'Karyawan Swasta',
                'monthly_income' => 7500000,
                'collectibility' => 'Lancar',
            ],
        );

        foreach ([
            ['ktp', 'ktp.jpg', 'documents/1/ktp.jpg', 245760, 'image/jpeg'],
            ['kk', 'kk.pdf', 'documents/1/kk.pdf', 512000, 'application/pdf'],
            ['slip_gaji', 'slip-gaji.pdf', 'documents/1/slip-gaji.pdf', 180000, 'application/pdf'],
        ] as [$type, $name, $path, $size, $mime]) {
            CustomerDocument::query()->firstOrCreate(
                ['customer_id' => $customer->id, 'name' => $name],
                ['type' => $type, 'path' => $path, 'size' => $size, 'mime' => $mime, 'uploaded_at' => now()->subDays(2)],
            );
        }

        foreach ([
            ['Bank Contoh', 'Kredit Multiguna', 15000000, 'Lunas', now()->subMonths(18)],
            ['Koperasi Contoh', 'Pinjaman Konsumtif', 5000000, 'Lunas', now()->subMonths(8)],
        ] as [$institution, $loanType, $amount, $status, $date]) {
            CreditHistory::query()->firstOrCreate(
                ['customer_id' => $customer->id, 'institution' => $institution, 'date' => $date->toDateString()],
                ['loan_type' => $loanType, 'amount' => $amount, 'status' => $status],
            );
        }

        $loan = Loan::query()->firstOrCreate(
            ['customer_id' => $customer->id, 'loan_product_id' => $product->id, 'amount' => 25000000],
            [
                'tenor_months' => 24,
                'interest_rate' => 1.25,
                'status' => 'Aktif',
                'outstanding' => 18750000,
                'applied_at' => now()->subMonths(7),
                'approved_at' => now()->subMonths(6),
                'disbursed_at' => now()->subMonths(6),
            ],
        );

        $principal = round(25000000 / 24, 2);
        $interest = round(25000000 * 1.25 / 100, 2);

        for ($number = 1; $number <= 6; $number++) {
            $installment = Installment::query()->firstOrCreate(
                ['loan_id' => $loan->id, 'number' => $number],
                [
                    'due_date' => now()->subMonths(6)->addMonths($number)->toDateString(),
                    'amount' => $principal + $interest,
                    'principal' => $principal,
                    'interest' => $interest,
                    'status' => $number < 6 ? 'Lunas' : 'Belum Lunas',
                    'paid_at' => $number < 6 ? now()->subMonths(6)->addMonths($number) : null,
                ],
            );

            if ($number < 6) {
                Payment::query()->firstOrCreate(
                    ['installment_id' => $installment->id],
                    [
                        'loan_id' => $loan->id,
                        'amount' => $principal + $interest,
                        'paid_at' => now()->subMonths(6)->addMonths($number),
                        'reference' => 'PAY-'.str_pad((string) $number, 6, '0', STR_PAD_LEFT),
                    ],
                );
            }
        }

        Akad::query()->firstOrCreate(
            ['loan_id' => $loan->id],
            [
                'signer_name' => $customer->name,
                'signed_at' => now()->subMonths(6),
                'note' => 'Akad demo nasabah.',
            ],
        );

        $journal = Journal::query()->firstOrCreate(
            ['reference' => 'DISB-'.$loan->id],
            [
                'date' => now()->subMonths(6)->toDateString(),
                'description' => 'Pencairan kredit '.$product->name,
                'source_type' => Loan::class,
                'source_id' => $loan->id,
            ],
        );

        foreach ([
            ['1000', 0, $loan->amount],
            ['1300', $loan->amount, 0],
        ] as [$code, $debit, $credit]) {
            JournalLine::query()->firstOrCreate(
                [
                    'journal_id' => $journal->id,
                    'account_id' => ChartOfAccount::query()->where('code', $code)->firstOrFail()->id,
                ],
                ['debit' => $debit, 'credit' => $credit],
            );
        }
    }
}
