<x-layouts::auth :title="__('Atur ulang kata sandi')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Atur ulang kata sandi')"
            :description="__('Masukkan kata sandi baru Anda di bawah ini.')"
        />

        <x-auth-session-status :status="session('status')" />

        @if ($errors->any())
            <x-ui.alert tone="danger">
                <x-lucide-circle-alert />
                <x-ui.alert-description>{{ __('Periksa kembali kata sandi Anda.') }}</x-ui.alert-description>
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-5">
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <x-ui.field>
                <x-ui.field-label for="email">{{ __('Email') }}</x-ui.field-label>
                <x-ui.input
                    id="email"
                    name="email"
                    type="email"
                    :value="request('email')"
                    required
                    autocomplete="email"
                />
                <x-ui.field-error :messages="$errors->get('email')" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.field-label for="password">{{ __('Kata Sandi Baru') }}</x-ui.field-label>
                <x-ui.input
                    id="password"
                    name="password"
                    type="password"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                />
                <x-ui.field-error :messages="$errors->get('password')" />
            </x-ui.field>

            <x-ui.field>
                <x-ui.field-label for="password_confirmation">{{ __('Konfirmasi Kata Sandi') }}</x-ui.field-label>
                <x-ui.input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    placeholder="Ulangi kata sandi"
                />
                <x-ui.field-error :messages="$errors->get('password_confirmation')" />
            </x-ui.field>

            <x-ui.button type="submit" class="w-full" data-test="reset-password-button">
                {{ __('Atur ulang kata sandi') }}
            </x-ui.button>
        </form>
    </div>
</x-layouts::auth>
