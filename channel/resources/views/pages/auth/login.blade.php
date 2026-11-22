<x-layouts::auth :title="__('Masuk')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Masuk ke akun Anda')"
            :description="__('Gunakan email dan kata sandi yang terdaftar.')"
        />

        <x-auth-session-status :status="session('status')" />

        @if ($errors->any())
            <x-ui.alert tone="danger">
                <x-lucide-circle-alert />
                <x-ui.alert-description>
                    {{ __('Periksa kembali data yang Anda masukkan.') }}
                </x-ui.alert-description>
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
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
                    autocomplete="email"
                    placeholder="nama@email.com"
                />
                <x-ui.field-error :messages="$errors->get('email')" />
            </x-ui.field>

            <x-ui.field>
                <div class="flex items-center justify-between">
                    <x-ui.field-label for="password">{{ __('Kata Sandi') }}</x-ui.field-label>
                    @if (Route::has('password.request'))
                        <x-ui.button variant="link" size="sm" href="{{ route('password.request') }}" wire:navigate>
                            {{ __('Lupa kata sandi?') }}
                        </x-ui.button>
                    @endif
                </div>
                <x-ui.input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan kata sandi"
                />
                <x-ui.field-error :messages="$errors->get('password')" />
            </x-ui.field>

            <x-ui.field orientation="horizontal">
                <x-ui.checkbox native id="remember" name="remember" value="1" :checked="old('remember')" />
                <x-ui.label for="remember">{{ __('Ingat saya') }}</x-ui.label>
            </x-ui.field>

            <x-ui.button type="submit" class="w-full" data-test="login-button">
                {{ __('Masuk') }}
            </x-ui.button>
        </form>

        <x-ui.alert tone="info">
            <x-lucide-shield-check />
            <x-ui.alert-description>
                {{ __('Jangan bagikan kode OTP atau kata sandi kepada siapa pun, termasuk petugas kami.') }}
            </x-ui.alert-description>
        </x-ui.alert>

        <div class="text-center text-sm text-muted-foreground">
            {{ __('Belum punya akun?') }}
            <x-ui.button variant="link" href="{{ route('register') }}" wire:navigate>
                {{ __('Daftar sekarang') }}
            </x-ui.button>
        </div>
    </div>
</x-layouts::auth>
