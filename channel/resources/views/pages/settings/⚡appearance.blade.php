<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Tampilan')] class extends Component
{
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Tampilan')" :subheading="__('Pilih tema yang paling nyaman untuk Anda.')">
        <div
            x-data="{
                mode: localStorage.getItem('bis-theme') || 'system',
                apply() {
                    const dark = this.mode === 'dark'
                        || (this.mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                    document.documentElement.classList.toggle('dark', dark);
                },
                set(value) {
                    this.mode = value;
                    if (value === 'system') localStorage.removeItem('bis-theme');
                    else localStorage.setItem('bis-theme', value);
                    this.apply();
                }
            }"
            x-init="apply()"
            class="flex flex-wrap gap-2"
        >
            <x-ui.button x-on:click="set('light')" :variant="null" class="border" x-bind:class="mode === 'light' ? 'bg-primary text-primary-foreground' : ''">
                <x-lucide-sun class="size-4" />
                {{ __('Terang') }}
            </x-ui.button>

            <x-ui.button x-on:click="set('dark')" :variant="null" class="border" x-bind:class="mode === 'dark' ? 'bg-primary text-primary-foreground' : ''">
                <x-lucide-moon class="size-4" />
                {{ __('Gelap') }}
            </x-ui.button>

            <x-ui.button x-on:click="set('system')" :variant="null" class="border" x-bind:class="mode === 'system' ? 'bg-primary text-primary-foreground' : ''">
                <x-lucide-monitor class="size-4" />
                {{ __('Sistem') }}
            </x-ui.button>
        </div>
    </x-pages::settings.layout>
</section>
