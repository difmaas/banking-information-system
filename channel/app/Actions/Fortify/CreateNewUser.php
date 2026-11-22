<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\RegistrationValidationRules;
use App\Contracts\CoreGateway;
use App\Exceptions\CoreGatewayException;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Permission\Models\Role;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, RegistrationValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $validated = Validator::make($input, [
            ...$this->registrationRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        try {
            return DB::transaction(function () use ($validated): User {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'nik' => $validated['nik'],
                    'phone' => $validated['phone'],
                    'password' => $validated['password'],
                ]);

                $user->assignRole(Role::findOrCreate('Nasabah', 'web'));

                app(CoreGateway::class)->createCustomer($user->id, [
                    'nik' => $validated['nik'],
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'occupation' => $validated['occupation'],
                    'monthly_income' => $validated['monthly_income'],
                ]);

                return $user;
            });
        } catch (CoreGatewayException $exception) {
            $errors = $exception->payload['errors'] ?? [];
            $field = is_array($errors) && $errors !== [] ? (string) array_key_first($errors) : 'nik';
            $message = is_array($errors) && isset($errors[$field][0])
                ? (string) $errors[$field][0]
                : 'Data nasabah gagal disinkronkan ke core banking. Silakan coba lagi.';

            throw ValidationException::withMessages([$field => $message]);
        }
    }
}
