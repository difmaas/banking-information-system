<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Validation\Rule;

trait RegistrationValidationRules
{
    /**
     * @return array<string, array<int, mixed>>
     */
    protected function registrationRules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'nik' => [
                'required',
                'string',
                'digits:16',
                Rule::unique(User::class, 'nik'),
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^(?:\+62|62|0)8[1-9][0-9]{6,11}$/',
                Rule::unique(User::class, 'phone'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class, 'email'),
            ],
            'occupation' => ['required', 'string', 'max:255'],
            'monthly_income' => ['required', 'numeric', 'min:0'],
            'terms' => ['accepted'],
        ];
    }
}
