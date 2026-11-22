<?php

use App\Support\ProfileRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Pengaturan Profil')] class extends Component
{
    public string $name = '';

    public string $email = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        if (config('fortify.lowercase_usernames')) {
            $this->email = Str::lower($this->email);
        }

        $validated = $this->validate(ProfileRules::rules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('toast', type: 'success', description: __('Profil diperbarui.'));
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Profil')" :subheading="__('Perbarui nama dan alamat email Anda.')">
        <form wire:submit="updateProfileInformation" class="w-full space-y-6">
            <x-ui.field>
                <x-ui.field-label for="name">{{ __('Nama') }}</x-ui.field-label>
                <x-ui.input id="name" wire:model="name" type="text" required autofocus autocomplete="name" />
                <x-ui.field-error :messages="$errors->get('name')" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.field-label for="email">{{ __('Email') }}</x-ui.field-label>
                <x-ui.input id="email" wire:model="email" type="email" required autocomplete="email" />
                <x-ui.field-error :messages="$errors->get('email')" />
            </x-ui.field>

            <x-ui.button type="submit" data-test="update-profile-button">
                {{ __('Simpan') }}
            </x-ui.button>
        </form>

        <livewire:pages::settings.delete-user-form />
    </x-pages::settings.layout>
</section>
