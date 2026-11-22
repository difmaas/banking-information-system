<?php

namespace App\Modules\Cis\Services;

use App\Modules\Cis\Models\Customer;
use App\Modules\Cis\Models\CustomerDocument;
use Illuminate\Support\Facades\DB;

class CisService
{
    public function createCustomer(int $channelUserId, array $data): array
    {
        if (blank($data['nik'] ?? null)) {
            throw new \RuntimeException('NIK wajib diisi.');
        }

        $customer = Customer::query()->firstOrCreate(
            ['channel_user_id' => $channelUserId],
            [
                'nik' => $data['nik'],
                'name' => $data['name'] ?? 'Nasabah',
                'email' => $data['email'] ?? 'nasabah'.$channelUserId.'@bis.test',
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'occupation' => $data['occupation'] ?? null,
                'monthly_income' => $data['monthly_income'] ?? null,
            ],
        );

        return $this->profile($channelUserId);
    }

    public function findByChannelUserId(int $channelUserId): Customer
    {
        return Customer::query()->where('channel_user_id', $channelUserId)->firstOrFail();
    }

    public function profile(int $channelUserId): array
    {
        $customer = $this->findByChannelUserId($channelUserId);

        return [
            'id' => $customer->id,
            'channel_user_id' => $customer->channel_user_id,
            'nik' => $customer->nik,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'occupation' => $customer->occupation,
            'monthly_income' => $customer->monthly_income,
            'collectibility' => $customer->collectibility,
        ];
    }

    public function updateProfile(int $channelUserId, array $data): array
    {
        unset($data['collectibility']);

        $customer = $this->findByChannelUserId($channelUserId);
        $customer->fill($data);
        $customer->save();

        return $this->profile($channelUserId);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function documents(int $channelUserId): array
    {
        $customer = $this->findByChannelUserId($channelUserId);

        return $customer->documents()
            ->orderByDesc('id')
            ->get()
            ->map(fn (CustomerDocument $document): array => [
                'id' => $document->id,
                'type' => $document->type,
                'name' => $document->name,
                'path' => $document->path,
                'size' => $document->size,
                'mime' => $document->mime,
                'uploaded_at' => $document->uploaded_at?->toISOString(),
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function storeDocument(int $channelUserId, array $data): array
    {
        $customer = $this->findByChannelUserId($channelUserId);
        $document = DB::transaction(fn (): CustomerDocument => $customer->documents()->create([
            'type' => $data['type'],
            'name' => $data['name'],
            'path' => $data['path'],
            'size' => $data['size'] ?? null,
            'mime' => $data['mime'] ?? null,
            'uploaded_at' => now(),
        ]));

        return [
            'id' => $document->id,
            'type' => $document->type,
            'name' => $document->name,
            'path' => $document->path,
            'size' => $document->size,
            'mime' => $document->mime,
            'uploaded_at' => $document->uploaded_at?->toISOString(),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function creditHistory(int $channelUserId): array
    {
        $customer = $this->findByChannelUserId($channelUserId);

        return $customer->creditHistories()
            ->orderByDesc('date')
            ->get()
            ->map(fn ($history): array => [
                'id' => $history->id,
                'institution' => $history->institution,
                'loan_type' => $history->loan_type,
                'amount' => $history->amount,
                'status' => $history->status,
                'date' => $history->date->toDateString(),
            ])
            ->all();
    }

    /**
     * @return array{module: string, name: string, status: string}
     */
    public function summary(): array
    {
        return [
            'module' => 'CIS',
            'name' => 'Customer Information System',
            'status' => 'active',
        ];
    }
}
