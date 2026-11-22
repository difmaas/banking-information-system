<div
    x-data="{
        timer: null,
        arm() {
            clearTimeout(this.timer);
            this.timer = setTimeout(() => $wire.lockSession(), {{ (int) config('pin.idle_minutes', 5) * 60000 }});
        }
    }"
    x-init="
        arm();
        ['click', 'keydown', 'scroll', 'mousemove', 'touchstart'].forEach((event) => {
            window.addEventListener(event, () => arm(), { passive: true });
        });
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) $wire.lockSession();
        });
        window.addEventListener('livewire:navigated', () => arm());
    "
>
    @if ($locked)
        <div class="fixed inset-0 z-[200] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
            <x-ui.card class="w-full max-w-sm p-6">
                <div class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                        <x-lucide-lock class="size-5" />
                    </span>
                    <div>
                        <h2 class="text-lg font-semibold">{{ __('Sesi Terkunci') }}</h2>
                        <p class="text-sm text-muted-foreground">{{ __('Masukkan PIN untuk melanjutkan.') }}</p>
                    </div>
                </div>

                @if ($remaining > 0)
                    <x-ui.alert tone="warning" class="mt-4">
                        <x-lucide-clock />
                        <x-ui.alert-description>
                            {{ __('Terlalu banyak percobaan. Coba lagi dalam :seconds detik.', ['seconds' => $remaining]) }}
                        </x-ui.alert-description>
                    </x-ui.alert>
                @endif

                <form wire:submit="unlock" class="mt-5 space-y-4">
                    <x-ui.field>
                        <x-ui.field-label for="unlock_pin">{{ __('PIN') }}</x-ui.field-label>
                        <x-ui.input id="unlock_pin" wire:model="pin" type="password" inputmode="numeric" maxlength="6" required autofocus />
                        <x-ui.field-error :messages="$errors->get('pin')" />
                    </x-ui.field>

                    <x-ui.button type="submit" class="w-full">
                        {{ __('Buka Sesi') }}
                    </x-ui.button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <x-ui.button type="submit" variant="ghost" class="w-full">
                        {{ __('Keluar') }}
                    </x-ui.button>
                </form>
            </x-ui.card>
        </div>
    @endif
</div>
