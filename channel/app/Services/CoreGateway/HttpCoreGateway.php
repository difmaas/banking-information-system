<?php

namespace App\Services\CoreGateway;

use App\Contracts\CoreGateway;
use App\Exceptions\CoreGatewayException;
use App\Models\CommunicationLog;
use App\Support\HmacSigner;
use Illuminate\Support\Facades\Http;

class HttpCoreGateway implements CoreGateway
{
    public function createCustomer(int $customerId, array $data): array
    {
        return $this->request('POST', '/api/internal/v1/customers', array_merge([
            'channel_user_id' => $customerId,
        ], $data));
    }

    public function getCustomerProfile(int $customerId): array
    {
        return $this->request('GET', '/api/internal/v1/customers/'.$customerId);
    }

    public function updateCustomerProfile(int $customerId, array $data): array
    {
        return $this->request('PUT', '/api/internal/v1/customers/'.$customerId, $data);
    }

    public function getCustomerDocuments(int $customerId): array
    {
        return $this->requestList('GET', '/api/internal/v1/customers/'.$customerId.'/documents');
    }

    public function storeCustomerDocument(int $customerId, array $data): array
    {
        return $this->request('POST', '/api/internal/v1/customers/'.$customerId.'/documents', $data);
    }

    public function getCustomerCreditHistory(int $customerId): array
    {
        return $this->requestList('GET', '/api/internal/v1/customers/'.$customerId.'/credit-history');
    }

    public function getLoanProducts(): array
    {
        return $this->requestList('GET', '/api/internal/v1/loan-products');
    }

    public function requiredDocuments(): array
    {
        $data = $this->request('GET', '/api/internal/v1/parameters/required-documents');

        return array_values(array_map('strval', $data));
    }

    public function simulateInstallment(int $productId, float $amount, int $tenorMonths): array
    {
        return $this->request('POST', '/api/internal/v1/loan-simulations', [
            'product_id' => $productId,
            'amount' => $amount,
            'tenor_months' => $tenorMonths,
        ]);
    }

    public function getCustomerLoans(int $customerId): array
    {
        return $this->requestList('GET', '/api/internal/v1/customers/'.$customerId.'/loans');
    }

    public function storeCustomerLoan(int $customerId, array $data): array
    {
        return $this->request('POST', '/api/internal/v1/customers/'.$customerId.'/loans', $data);
    }

    public function getCustomerLoan(int $customerId, int $loanId): array
    {
        return $this->request('GET', '/api/internal/v1/customers/'.$customerId.'/loans/'.$loanId);
    }

    public function getCustomerLoanInstallments(int $customerId, int $loanId): array
    {
        return $this->requestList('GET', '/api/internal/v1/customers/'.$customerId.'/loans/'.$loanId.'/installments');
    }

    public function getCustomerLoanPayments(int $customerId, int $loanId): array
    {
        return $this->requestList('GET', '/api/internal/v1/customers/'.$customerId.'/loans/'.$loanId.'/payments');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function request(string $method, string $path, array $payload = []): array
    {
        $body = $payload === [] ? '' : json_encode($payload, JSON_THROW_ON_ERROR);
        $token = (string) config('services.core.service_token');
        $secret = (string) config('services.core.hmac_secret');
        $headers = array_merge(
            ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
            HmacSigner::headers($method, $path, $body, $token, $secret),
        );

        $idempotencyKey = request()->header('Idempotency-Key');

        if (is_string($idempotencyKey) && $idempotencyKey !== '') {
            $headers['Idempotency-Key'] = $idempotencyKey;
        }

        $startedAt = microtime(true);
        $response = Http::withHeaders($headers)
            ->timeout((int) config('services.core.timeout', 10))
            ->send($method, (string) config('services.core.base_url').$path, ['body' => $body]);

        $durationMs = (int) ((microtime(true) - $startedAt) * 1000);

        CommunicationLog::create([
            'direction' => 'outbound',
            'service' => 'core',
            'method' => $method,
            'path' => $path,
            'status_code' => $response->status(),
            'duration_ms' => $durationMs,
            'user_id' => request()->user()?->id,
            'request' => $this->redact($payload),
            'response' => $this->redactResponse($response->json()),
        ]);

        if ($response->failed()) {
            $payload = $response->json();

            throw new CoreGatewayException(
                'Core request failed: '.$response->status(),
                $response->status(),
                is_array($payload) ? $payload : [],
            );
        }

        $data = $response->json();

        return is_array($data) ? $data : [];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int, array<string, mixed>>
     */
    private function requestList(string $method, string $path, array $payload = []): array
    {
        $data = $this->request($method, $path, $payload);

        return array_map(
            fn (mixed $item): array => $this->toArrayItem($item),
            array_values($data),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function toArrayItem(mixed $item): array
    {
        return is_array($item) ? $item : [];
    }

    /**
     * Remove personally identifiable information from logged payloads.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function redact(array $payload): array
    {
        $sensitive = ['nik', 'phone', 'password', 'password_confirmation', 'current_password', 'pin', 'pin_confirmation'];

        foreach ($payload as $key => $value) {
            if (in_array($key, $sensitive, true)) {
                $payload[$key] = '[redacted]';

                continue;
            }

            if (is_array($value)) {
                $payload[$key] = $this->redact($value);
            }
        }

        return $payload;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function redactResponse(mixed $response): ?array
    {
        return is_array($response) ? $this->redact($response) : null;
    }
}
