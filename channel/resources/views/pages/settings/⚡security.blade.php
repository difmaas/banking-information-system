<?php

use App\Concerns\PasswordValidationRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Keamanan Akun')] class extends Component
{
    use PasswordValidationRules;

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => $this->currentPasswordRules(),
                'password' => $this->passwordRules(),
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('toast', type: 'success', description: __('Kata sandi diperbarui.'));
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Ubah Kata Sandi')" :subheading="__('Gunakan kata sandi yang panjang dan unik agar akun tetap aman.')">
        <form wire:submit="updatePassword" class="w-full space-y-6">
            <x-ui.field>
                <x-ui.field-label for="current_password">{{ __('Kata sandi saat ini') }}</x-ui.field-label>
                <x-ui.input id="current_password" wire:model="current_password" type="password" required autocomplete="current-password" />
                <x-ui.field-error :messages="$errors->get('current_password')" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.field-label for="password">{{ __('Kata sandi baru') }}</x-ui.field-label>
                <x-ui.input id="password" wire:model="password" type="password" required minlength="8" autocomplete="new-password" />
                <x-ui.field-error :messages="$errors->get('password')" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.field-label for="password_confirmation">{{ __('Konfirmasi kata sandi baru') }}</x-ui.field-label>
                <x-ui.input id="password_confirmation" wire:model="password_confirmation" type="password" required minlength="8" autocomplete="new-password" />
                <x-ui.field-error :messages="$errors->get('password_confirmation')" />
            </x-ui.field>

            <x-ui.button type="submit" data-test="update-password-button">
                {{ __('Simpan Kata Sandi') }}
            </x-ui.button>
        </form>
    </x-pages::settings.layout>
</section>
