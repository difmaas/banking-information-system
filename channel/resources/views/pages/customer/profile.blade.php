<?php

use App\Contracts\CoreGateway;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Profil Saya')] class extends Component
{
    public array $profile = [];

    public string $phone = '';

    public string $address = '';

    public string $occupation = '';

    public string $monthly_income = '';

    public function mount(): void
    {
        $this->profile = app(CoreGateway::class)->getCustomerProfile(auth()->id());
        $this->phone = (string) ($this->profile['phone'] ?? auth()->user()->phone ?? '');
        $this->address = (string) ($this->profile['address'] ?? '');
        $this->occupation = (string) ($this->profile['occupation'] ?? '');
        $this->monthly_income = (string) ($this->profile['monthly_income'] ?? '');
    }

    public function updateProfile(): void
    {
        $validated = $this->validate([
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'occupation' => ['required', 'string', 'max:255'],
            'monthly_income' => ['required', 'numeric', 'min:0'],
        ]);

        $this->profile = app(CoreGateway::class)->updateCustomerProfile(auth()->id(), $validated);

        $this->dispatch('toast', type: 'success', description: 'Profil nasabah diperbarui.');
    }
}; ?>

<section class="space-y-6">
    <div class="relative overflow-hidden rounded-xl bg-primary p-6 text-primary-foreground sm:p-8">
        <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <x-ui.avatar class="size-16 text-lg ring-4 ring-primary-foreground/15">
                    <x-ui.avatar-fallback>{{ auth()->user()->initials() }}</x-ui.avatar-fallback>
                </x-ui.avatar>

                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-primary-foreground/70">{{ __('Profil nasabah') }}</p>
                    <h2 class="mt-1 truncate text-2xl font-semibold tracking-tight">{{ $profile['name'] ?? auth()->user()->name }}</h2>
                    <p class="mt-1 text-sm text-primary-foreground/75">{{ $profile['email'] ?? auth()->user()->email }}</p>
                </div>
            </div>

            <x-ui.badge variant="secondary" class="w-fit bg-primary-foreground/10 text-primary-foreground">
                {{ __('Akun aktif') }}
            </x-ui.badge>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-5">
        <div class="space-y-5 lg:col-span-2">
            <x-ui.card class="p-5 sm:p-6">
                <h3 class="text-base font-semibold">{{ __('Identitas') }}</h3>

                <dl class="mt-5 space-y-4 text-sm">
                    <div>
                        <dt class="text-muted-foreground">{{ __('NIK') }}</dt>
                        <dd class="mt-0.5 font-semibold tracking-wide">{{ auth()->user()->maskedNik() }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">{{ __('Nama lengkap') }}</dt>
                        <dd class="mt-0.5 font-medium">{{ $profile['name'] ?? auth()->user()->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted-foreground">{{ __('Email') }}</dt>
                        <dd class="mt-0.5 break-all font-medium">{{ $profile['email'] ?? auth()->user()->email }}</dd>
                    </div>
                </dl>

                <x-ui.alert tone="info" class="mt-5">
                    <x-lucide-shield-check />
                    <x-ui.alert-description>
                        {{ __('NIK dan nama tersimpan. Verifikasi berkas dilakukan oleh Staf Kredit.') }}
                    </x-ui.alert-description>
                </x-ui.alert>
            </x-ui.card>

            <x-ui.card class="p-5 sm:p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">{{ __('Status kredit') }}</p>
                <div class="mt-3 flex items-center justify-between">
                    <span class="text-sm text-muted-foreground">{{ __('Kolektibilitas') }}</span>
                    <x-ui.badge :tone="($profile['collectibility'] ?? '') === 'Lancar' ? 'success' : 'warning'" size="sm">
                        {{ $profile['collectibility'] ?? '-' }}
                    </x-ui.badge>
                </div>
                <p class="mt-3 text-xs text-muted-foreground">{{ __('Status ini dikelola oleh sistem core banking dan hanya dapat dilihat.') }}</p>
            </x-ui.card>
        </div>

        <x-ui.card class="p-5 sm:p-6 lg:col-span-3">
            <h3 class="text-base font-semibold">{{ __('Data kontak & pekerjaan') }}</h3>
            <p class="mt-1 text-sm text-muted-foreground">{{ __('Perbarui informasi agar kami dapat menghubungi Anda dengan tepat.') }}</p>

            <form wire:submit="updateProfile" class="mt-5 grid gap-4 sm:grid-cols-2">
                <x-ui.field class="sm:col-span-2">
                    <x-ui.field-label for="phone">{{ __('Nomor Telepon') }}</x-ui.field-label>
                    <x-ui.input id="phone" wire:model="phone" placeholder="08xxxxxxxxxx" />
                    <x-ui.field-error :messages="$errors->get('phone')" />
                </x-ui.field>

                <x-ui.field class="sm:col-span-2">
                    <x-ui.field-label for="occupation">{{ __('Pekerjaan') }}</x-ui.field-label>
                    <x-ui.input id="occupation" wire:model="occupation" placeholder="Contoh: Karyawan swasta" />
                    <x-ui.field-error :messages="$errors->get('occupation')" />
                </x-ui.field>

                <x-ui.field class="sm:col-span-2">
                    <x-ui.field-label for="monthly_income">{{ __('Penghasilan Bulanan') }}</x-ui.field-label>
                    <x-ui.input id="monthly_income" wire:model="monthly_income" type="number" min="0" placeholder="0" />
                    <x-ui.field-error :messages="$errors->get('monthly_income')" />
                </x-ui.field>

                <x-ui.field class="sm:col-span-2">
                    <x-ui.field-label for="address">{{ __('Alamat') }}</x-ui.field-label>
                    <x-ui.textarea id="address" wire:model="address" rows="3" placeholder="Alamat domisili saat ini" />
                    <x-ui.field-error :messages="$errors->get('address')" />
                </x-ui.field>

                <div class="flex justify-end sm:col-span-2">
                    <x-ui.button type="submit">{{ __('Simpan Perubahan') }}</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</section>
