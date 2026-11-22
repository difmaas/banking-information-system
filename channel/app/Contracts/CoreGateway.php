<?php

namespace App\Contracts;

interface CoreGateway
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createCustomer(int $channelUserId, array $data): array;

    /**
     * @return array<string, mixed>
     */
    public function getCustomerProfile(int $customerId): array;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function updateCustomerProfile(int $customerId, array $data): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getCustomerDocuments(int $customerId): array;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function storeCustomerDocument(int $customerId, array $data): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getCustomerCreditHistory(int $customerId): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getLoanProducts(): array;

    /**
     * @return array<int, string>
     */
    public function requiredDocuments(): array;

    /**
     * @return array<string, mixed>
     */
    public function simulateInstallment(int $productId, float $amount, int $tenorMonths): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getCustomerLoans(int $customerId): array;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function storeCustomerLoan(int $customerId, array $data): array;

    /**
     * @return array<string, mixed>
     */
    public function getCustomerLoan(int $customerId, int $loanId): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getCustomerLoanInstallments(int $customerId, int $loanId): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getCustomerLoanPayments(int $customerId, int $loanId): array;
}
