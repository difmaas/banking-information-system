<?php

use Livewire\Component;

new class extends Component
{
    public function requestDelete(): void
    {
        $this->dispatch('open-delete-user');
    }
}; ?>

<section class="mt-10 space-y-4">
    <div>
        <h3 class="text-base font-semibold">{{ __('Hapus akun') }}</h3>
        <p class="mt-1 text-sm text-muted-foreground">{{ __('Hapus akun dan seluruh data terkait secara permanen.') }}</p>
    </div>

    <x-ui.button variant="destructive" wire:click="requestDelete" data-test="delete-user-button">
        {{ __('Hapus akun') }}
    </x-ui.button>
</section>
