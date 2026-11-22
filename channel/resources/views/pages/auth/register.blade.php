<x-layouts::auth :title="__('Daftar')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('Buat akun nasabah')"
            :description="__('Lengkapi data diri dan amankan akun Anda dalam dua langkah.')"
        />

        <x-auth-session-status :status="session('status')" />

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

        <form
            method="POST"
            action="{{ route('register.store') }}"
            class="flex flex-col gap-6"
            x-data="{ step: {{ $errors->hasAny(['password', 'password_confirmation', 'terms']) ? 2 : 1 }} }"
        >
            @csrf

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <span
                        class="flex size-7 items-center justify-center rounded-full text-xs font-semibold"
                        :class="step >= 1 ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'"
                    >1</span>
                    <span class="text-xs font-medium" :class="step >= 1 ? 'text-foreground' : 'text-muted-foreground'">
                        {{ __('Data diri') }}
                    </span>
                </div>

                <div class="h-px flex-1" :class="step === 2 ? 'bg-primary' : 'bg-border'"></div>

                <div class="flex items-center gap-2">
                    <span
                        class="flex size-7 items-center justify-center rounded-full text-xs font-semibold"
                        :class="step === 2 ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'"
                    >2</span>
                    <span class="text-xs font-medium" :class="step === 2 ? 'text-foreground' : 'text-muted-foreground'">
                        {{ __('Keamanan') }}
                    </span>
                </div>
            </div>

            <div x-show="step === 1" x-transition.opacity class="flex flex-col gap-5">
                <x-ui.field>
                    <x-ui.field-label for="name">{{ __('Nama Lengkap') }}</x-ui.field-label>
                    <x-ui.input
                        id="name"
                        name="name"
                        type="text"
                        :value="old('name')"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Sesuai KTP"
                    />
                    <x-ui.field-error :messages="$errors->get('name')" />
                </x-ui.field>

                <x-ui.field>
                    <x-ui.field-label for="nik">{{ __('NIK') }}</x-ui.field-label>
                    <x-ui.input
                        id="nik"
                        name="nik"
                        type="text"
                        :value="old('nik')"
                        required
                        inputmode="numeric"
                        maxlength="16"
                        pattern="[0-9]{16}"
                        autocomplete="off"
                        placeholder="16 digit NIK"
                        x-on:input="$el.value = $el.value.replace(/\D/g, '').slice(0, 16)"
                    />
                    <x-ui.field-error :messages="$errors->get('nik')" />
                </x-ui.field>

                <x-ui.field>
                    <x-ui.field-label for="phone">{{ __('Nomor Telepon') }}</x-ui.field-label>
                    <x-ui.input
                        id="phone"
                        name="phone"
                        type="tel"
                        :value="old('phone')"
                        required
                        inputmode="tel"
                        autocomplete="tel"
                        pattern="(?:\+62|62|0)8[1-9][0-9]{6,11}"
                        placeholder="08xxxxxxxxxx"
                    />
                    <x-ui.field-error :messages="$errors->get('phone')" />
                </x-ui.field>

                <x-ui.field>
                    <x-ui.field-label for="email">{{ __('Email') }}</x-ui.field-label>
                    <x-ui.input
                        id="email"
                        name="email"
                        type="email"
                        :value="old('email')"
                        required
                        autocomplete="email"
                        placeholder="nama@email.com"
                    />
                    <x-ui.field-error :messages="$errors->get('email')" />
                </x-ui.field>

                <x-ui.field>
                    <x-ui.field-label for="occupation">{{ __('Pekerjaan') }}</x-ui.field-label>
                    <x-ui.input
                        id="occupation"
                        name="occupation"
                        type="text"
                        :value="old('occupation')"
                        required
                        autocomplete="organization-title"
                        placeholder="Contoh: Karyawan swasta"
                    />
                    <x-ui.field-error :messages="$errors->get('occupation')" />
                </x-ui.field>

                <x-ui.field>
                    <x-ui.field-label for="monthly_income">{{ __('Penghasilan Bulanan') }}</x-ui.field-label>
                    <x-ui.input
                        id="monthly_income"
                        name="monthly_income"
                        type="number"
                        min="0"
                        :value="old('monthly_income')"
                        required
                        placeholder="0"
                    />
                    <x-ui.field-error :messages="$errors->get('monthly_income')" />
                </x-ui.field>

                <x-ui.alert tone="info">
                    <x-lucide-id-card />
                    <x-ui.alert-description>
                        {{ __('Pastikan NIK dan nama sesuai KTP. NIK akan diverifikasi saat proses pengajuan kredit.') }}
                    </x-ui.alert-description>
                </x-ui.alert>

                <x-ui.button
                    type="button"
                    class="w-full"
                    x-on:click="
                        let valid = true;
                        ['name', 'nik', 'phone', 'email', 'occupation', 'monthly_income'].forEach((field) => {
                            const input = $root.querySelector('[name=' + field + ']');
                            if (input && !input.reportValidity()) valid = false;
                        });
                        if (valid) step = 2;
                    "
                >
                    {{ __('Lanjutkan') }}
                </x-ui.button>
            </div>

            <div x-show="step === 2" x-transition.opacity class="flex flex-col gap-5" x-cloak>
                <x-ui.field>
                    <x-ui.field-label for="password">{{ __('Kata Sandi') }}</x-ui.field-label>
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

                <x-ui.field>
                    <div class="flex items-start gap-2">
                        <x-ui.checkbox native id="terms" name="terms" value="1" :checked="old('terms')" />
                        <x-ui.label for="terms" class="leading-snug">
                            {{ __('Saya menyetujui syarat dan ketentuan serta kebijakan privasi BIS.') }}
                        </x-ui.label>
                    </div>
                    <x-ui.field-error :messages="$errors->get('terms')" />
                </x-ui.field>

                <div class="flex gap-3">
                    <x-ui.button type="button" variant="ghost" class="flex-1" x-on:click="step = 1">
                        {{ __('Kembali') }}
                    </x-ui.button>
                    <x-ui.button type="submit" class="flex-1" data-test="register-user-button">
                        {{ __('Daftar') }}
                    </x-ui.button>
                </div>
            </div>
        </form>

        <div class="text-center text-sm text-muted-foreground">
            {{ __('Sudah punya akun?') }}
            <x-ui.button variant="link" href="{{ route('login') }}" wire:navigate>
                {{ __('Masuk') }}
            </x-ui.button>
        </div>
    </div>
</x-layouts::auth>
