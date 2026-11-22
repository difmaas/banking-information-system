<?php

namespace App\Actions;

use App\Contracts\CoreGateway;
use App\Models\User;

class EnsureCoreCustomer
{
    /**
     * Make sure the channel user has a matching customer record in core.
     *
     * @return array<string, mixed>
     */
    public function handle(User $user): array
    {
        if (blank($user->nik)) {
            return [];
        }

        return app(CoreGateway::class)->createCustomer($user->id, [
            'nik' => $user->nik,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
        ]);
    }
}
