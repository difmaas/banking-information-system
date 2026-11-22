<div class="flex items-start gap-6 max-md:flex-col">
    <div class="w-full md:w-56">
        <x-ui.card class="p-2">
            <nav class="flex flex-col gap-1" aria-label="{{ __('Pengaturan') }}">
                <x-ui.button href="{{ route('profile.edit') }}" variant="ghost" class="w-full justify-start" wire:navigate>
                    <x-lucide-user class="size-4" />
                    {{ __('Profil') }}
                </x-ui.button>
                <x-ui.button href="{{ route('security.edit') }}" variant="ghost" class="w-full justify-start" wire:navigate>
                    <x-lucide-lock class="size-4" />
                    {{ __('Keamanan') }}
                </x-ui.button>
                <x-ui.button href="{{ route('appearance.edit') }}" variant="ghost" class="w-full justify-start" wire:navigate>
                    <x-lucide-paintbrush class="size-4" />
                    {{ __('Tampilan') }}
                </x-ui.button>
            </nav>
        </x-ui.card>
    </div>

    <x-ui.card class="flex-1 self-stretch p-5 sm:p-6">
        <h2 class="text-lg font-semibold">{{ $heading ?? '' }}</h2>
        <p class="mt-1 text-sm text-muted-foreground">{{ $subheading ?? '' }}</p>

        <div class="mt-5 w-full max-w-xl">
            {{ $slot }}
        </div>
    </x-ui.card>
</div>
