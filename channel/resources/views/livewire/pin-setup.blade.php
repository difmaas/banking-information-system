<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('Buat PIN Anda')"
        :description="__('PIN digunakan untuk mengunci sesi dan mengonfirmasi transaksi.')"
    />

    @if ($errors->any())
        <x-ui.alert tone="danger">
            <x-lucide-circle-alert />
            <x-ui.alert-description>
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert-description>
        </x-ui.alert>
    @endif

    <form wire:submit="save" class="flex flex-col gap-5">
        <x-ui.field>
            <x-ui.field-label for="current_password">{{ __('Kata Sandi Saat Ini') }}</x-ui.field-label>
            <x-ui.input id="current_password" wire:model="current_password" type="password" required autocomplete="current-password" />
            <x-ui.field-error :messages="$errors->get('current_password')" />
        </x-ui.field>

        <x-ui.field>
            <x-ui.field-label for="pin">{{ __('PIN (6 digit)') }}</x-ui.field-label>
            <x-ui.input id="pin" wire:model="pin" type="password" inputmode="numeric" maxlength="6" required autocomplete="new-password" />
            <x-ui.field-error :messages="$errors->get('pin')" />
        </x-ui.field>

        <x-ui.field>
            <x-ui.field-label for="pin_confirmation">{{ __('Ulangi PIN') }}</x-ui.field-label>
            <x-ui.input id="pin_confirmation" wire:model="pin_confirmation" type="password" inputmode="numeric" maxlength="6" required autocomplete="new-password" />
        </x-ui.field>

        <x-ui.button type="submit" class="w-full">
            {{ __('Simpan PIN') }}
        </x-ui.button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="w-full">
        @csrf
        <x-ui.button type="submit" variant="ghost" class="w-full">
            {{ __('Keluar') }}
        </x-ui.button>
    </form>
</div>
