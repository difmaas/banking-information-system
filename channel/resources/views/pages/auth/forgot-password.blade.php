<x-layouts::auth :title="__('Lupa kata sandi')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Lupa kata sandi')"
            :description="__('Masukkan email Anda untuk menerima tautan pengaturan ulang kata sandi.')"
        />

        <x-auth-session-status :status="session('status')" />

        @if ($errors->any())
            <x-ui.alert tone="danger">
                <x-lucide-circle-alert />
                <x-ui.alert-description>{{ __('Periksa kembali alamat email Anda.') }}</x-ui.alert-description>
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5">
            @csrf

            <x-ui.field>
                <x-ui.field-label for="email">{{ __('Email') }}</x-ui.field-label>
                <x-ui.input
                    id="email"
                    name="email"
                    type="email"
                    :value="old('email')"
                    required
                    autofocus
                    placeholder="nama@email.com"
                />
                <x-ui.field-error :messages="$errors->get('email')" />
            </x-ui.field>

            <x-ui.button type="submit" class="w-full" data-test="email-password-reset-link-button">
                {{ __('Kirim tautan pengaturan ulang') }}
            </x-ui.button>
        </form>

        <div class="text-center text-sm text-muted-foreground">
            {{ __('Atau kembali ke') }}
            <x-ui.button variant="link" href="{{ route('login') }}" wire:navigate>
                {{ __('halaman masuk') }}
            </x-ui.button>
        </div>
    </div>
</x-layouts::auth>
