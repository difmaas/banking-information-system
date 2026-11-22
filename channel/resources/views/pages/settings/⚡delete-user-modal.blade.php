<?php

use App\Concerns\PasswordValidationRules;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    use PasswordValidationRules;

    public string $password = '';

    public bool $confirmingDelete = false;

    #[On('open-delete-user')]
    public function open(): void
    {
        $this->confirmingDelete = true;
    }

    public function cancel(): void
    {
        $this->confirmingDelete = false;
        $this->reset('password');
        $this->resetValidation();
    }

    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => $this->currentPasswordRules(),
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    @if ($confirmingDelete)
        <x-ui.card class="mt-6 max-w-lg p-5 sm:p-6">
        <h3 class="text-lg font-semibold">{{ __('Apakah Anda yakin ingin menghapus akun?') }}</h3>
        <p class="mt-2 text-sm text-muted-foreground">
            {{ __('Seluruh data dan riwayat Anda akan dihapus secara permanen. Masukkan kata sandi untuk mengonfirmasi.') }}
        </p>

        <form wire:submit="deleteUser" class="mt-5 space-y-4">
            <x-ui.field>
                <x-ui.field-label for="delete_password">{{ __('Kata Sandi') }}</x-ui.field-label>
                <x-ui.input id="delete_password" wire:model="password" type="password" required autocomplete="current-password" />
                <x-ui.field-error :messages="$errors->get('password')" />
            </x-ui.field>

            <div class="flex justify-end gap-2">
                <x-ui.button type="button" variant="ghost" wire:click="cancel">
                    {{ __('Batal') }}
                </x-ui.button>
                <x-ui.button type="submit" variant="destructive" data-test="confirm-delete-user-button">
                    {{ __('Hapus akun') }}
                </x-ui.button>
            </div>
        </form>
        </x-ui.card>
    @endif
</div>
