<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ProfileRules
{
    /**
     * @return array<string, array<int, Unique|ValidationRule|array<mixed>|string>>
     */
    public static function rules(?int $userId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => self::emailRules($userId),
        ];
    }

    /**
     * @return array<int, Unique|ValidationRule|array<mixed>|string>
     */
    private static function emailRules(?int $userId = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            $userId === null
                ? Rule::unique(User::class)
                : Rule::unique(User::class)->ignore($userId),
        ];
    }
}
