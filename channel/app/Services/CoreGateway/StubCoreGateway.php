<?php

namespace App\Services\CoreGateway;

use App\Contracts\CoreGateway;

class StubCoreGateway implements CoreGateway
{
    public function createCustomer(int $customerId, array $data): array
    {
        return $this->getCustomerProfile($customerId);
    }

    public function getCustomerProfile(int $customerId): array
    {
        return [
            'id' => $customerId,
            'nik' => '3204'.str_pad((string) $customerId, 12, '0', STR_PAD_LEFT),
            'name' => 'Nasabah Demo',
            'email' => 'nasabah@bis.test',
            'phone' => '081234567890',
            'address' => 'Jl. Contoh No. 1, Medan',
            'occupation' => 'Karyawan Swasta',
            'monthly_income' => 7500000,
            'collectibility' => 'Lancar',
        ];
    }

    public function updateCustomerProfile(int $customerId, array $data): array
    {
        return array_merge($this->getCustomerProfile($customerId), $data);
    }

    public function getCustomerDocuments(int $customerId): array
    {
        return [
            [
                'id' => 1,
                'type' => 'ktp',
                'name' => 'ktp.jpg',
                'size' => 245760,
                'uploaded_at' => now()->subDays(3)->toISOString(),
            ],
            [
                'id' => 2,
                'type' => 'kk',
                'name' => 'kk.pdf',
                'size' => 512000,
                'uploaded_at' => now()->subDays(2)->toISOString(),
            ],
            [
                'id' => 3,
                'type' => 'slip_gaji',
                'name' => 'slip-gaji.pdf',
                'size' => 180000,
                'uploaded_at' => now()->subDay()->toISOString(),
            ],
        ];
    }

    public function storeCustomerDocument(int $customerId, array $data): array
    {
        return array_merge([
            'id' => rand(100, 999),
            'uploaded_at' => now()->toISOString(),
        ], $data);
    }

    public function getCustomerCreditHistory(int $customerId): array
    {
        return [
            [
                'id' => 1,
                'institution' => 'Bank Contoh',
                'loan_type' => 'Kredit Multiguna',
                'amount' => 15000000,
                'status' => 'Lunas',
                'date' => now()->subMonths(18)->toDateString(),
            ],
            [
                'id' => 2,
                'institution' => 'Koperasi Contoh',
                'loan_type' => 'Pinjaman Konsumtif',
                'amount' => 5000000,
                'status' => 'Lunas',
                'date' => now()->subMonths(8)->toDateString(),
            ],
        ];
    }

    public function getLoanProducts(): array
    {
        return [
            [
                'id' => 1,
                'code' => 'KM-01',
                'name' => 'Kredit Multiguna',
                'description' => 'Kredit konsumtif dengan jaminan gaji atau deposito.',
                'min_amount' => 1000000,
                'max_amount' => 100000000,
                'min_tenor_months' => 6,
                'max_tenor_months' => 60,
                'interest_rate' => 1.25,
            ],
        ];
    }

    public function requiredDocuments(): array
    {
        return ['ktp', 'kk', 'slip_gaji'];
    }

    public function simulateInstallment(int $productId, float $amount, int $tenorMonths): array
    {
        $product = collect($this->getLoanProducts())->firstWhere('id', $productId);
        $rate = (float) ($product['interest_rate'] ?? 0);
        $monthly = round(($amount + ($amount * $rate / 100 * $tenorMonths)) / $tenorMonths, 2);
        $total = round($monthly * $tenorMonths, 2);

        return [
            'product_id' => $productId,
            'product_name' => $product['name'] ?? 'Produk Kredit',
            'amount' => $amount,
            'tenor_months' => $tenorMonths,
            'interest_rate' => $rate,
            'monthly_installment' => $monthly,
            'total_interest' => round($total - $amount, 2),
            'total_payment' => $total,
        ];
    }

    public function getCustomerLoans(int $customerId): array
    {
        return [
            [
                'id' => 1,
                'product' => 'Kredit Multiguna',
                'amount' => 25000000,
                'tenor_months' => 24,
                'status' => 'Aktif',
                'outstanding' => 18750000,
                'disbursed_at' => now()->subMonths(6)->toDateString(),
            ],
        ];
    }

    public function storeCustomerLoan(int $customerId, array $data): array
    {
        return array_merge([
            'id' => rand(100, 999),
            'status' => 'Diajukan',
            'customer_id' => $customerId,
            'created_at' => now()->toISOString(),
        ], $data);
    }

    public function getCustomerLoan(int $customerId, int $loanId): array
    {
        return [
            'id' => $loanId,
            'customer_id' => $customerId,
            'product' => 'Kredit Multiguna',
            'amount' => 25000000,
            'tenor_months' => 24,
            'status' => 'Aktif',
            'outstanding' => 18750000,
            'interest_rate' => 1.25,
            'disbursed_at' => now()->subMonths(6)->toDateString(),
        ];
    }

    public function getCustomerLoanInstallments(int $customerId, int $loanId): array
    {
        return collect(range(1, 6))->map(fn (int $number): array => [
            'number' => $number,
            'due_date' => now()->addMonths($number - 6)->toDateString(),
            'amount' => 1250000,
            'status' => $number <= 5 ? 'Lunas' : 'Belum Lunas',
        ])->all();
    }

    public function getCustomerLoanPayments(int $customerId, int $loanId): array
    {
        return collect(range(1, 5))->map(fn (int $number): array => [
            'id' => $number,
            'installment_number' => $number,
            'amount' => 1250000,
            'paid_at' => now()->subMonths(6 - $number)->toDateString(),
            'reference' => 'PAY-'.str_pad((string) $number, 6, '0', STR_PAD_LEFT),
        ])->all();
    }
}
