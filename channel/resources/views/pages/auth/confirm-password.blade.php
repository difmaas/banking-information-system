<x-layouts::auth :title="__('Konfirmasi kata sandi')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Konfirmasi kata sandi')"
            :description="__('Ini adalah area aman. Harap konfirmasi kata sandi Anda sebelum melanjutkan.')"
        />

        <x-auth-session-status :status="session('status')" />

        @if ($errors->any())
            <x-ui.alert tone="danger">
                <x-lucide-circle-alert />
                <x-ui.alert-description>{{ __('Kata sandi yang Anda masukkan tidak sesuai.') }}</x-ui.alert-description>
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ route('password.confirm.store') }}" class="flex flex-col gap-5">
            @csrf

            <x-ui.field>
                <x-ui.field-label for="password">{{ __('Kata Sandi') }}</x-ui.field-label>
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

            <x-ui.button type="submit" class="w-full" data-test="confirm-password-button">
                {{ __('Konfirmasi') }}
            </x-ui.button>
        </form>
    </div>
</x-layouts::auth>
