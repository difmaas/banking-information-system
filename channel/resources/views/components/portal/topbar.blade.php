@props(['title' => null])

@php
    $user = auth()->user();

    $routeTitle = match (true) {
        request()->routeIs('dashboard') => __('Beranda'),
        request()->routeIs('customer.profile') => __('Profil Saya'),
        request()->routeIs('customer.documents') => __('Dokumen'),
        request()->routeIs('customer.credit-history') => __('Riwayat Kredit'),
        request()->routeIs('customer.loans') => __('Pinjaman'),
        request()->routeIs('customer.installments') => __('Angsuran'),
        request()->routeIs('customer.payments') => __('Pembayaran'),
        request()->routeIs('customer.coming-soon') => ucfirst(str_replace('-', ' ', (string) request()->route('feature'))),
        request()->routeIs('profile.edit') => __('Pengaturan'),
        default => __('Portal Nasabah'),
    };
@endphp

<header class="sticky top-0 z-30 w-full border-b bg-background/85 backdrop-blur-xl">
    <div class="mx-auto flex h-16 w-full max-w-7xl items-center gap-3 px-4 sm:px-6 lg:px-8">
        <x-ui.sidebar-trigger class="lg:hidden" />

        <div class="min-w-0 flex-1">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-primary">
                {{ __('Portal Nasabah') }}
            </p>
            <h1 class="truncate text-base font-semibold text-foreground">
                {{ $title ?? $routeTitle }}
            </h1>
        </div>

        <div class="hidden items-center gap-1 sm:flex">
            <x-ui.button variant="ghost" size="icon" :aria-label="__('Cari')">
                <x-lucide-search />
            </x-ui.button>
            <x-ui.button variant="ghost" size="icon" :aria-label="__('Notifikasi')">
                <x-lucide-bell />
            </x-ui.button>

            <x-ui.button
                variant="ghost"
                size="icon"
                x-on:click="document.documentElement.classList.toggle('dark')"
                :aria-label="__('Ubah tema')"
            >
                <x-lucide-moon class="dark:hidden" />
                <x-lucide-sun class="hidden dark:block" />
            </x-ui.button>
        </div>

        <x-ui.dropdown-menu>
            <x-ui.dropdown-menu-trigger>
                <x-ui.button variant="ghost" size="icon" :aria-label="__('Menu akun')">
                    <x-ui.avatar>
                        <x-ui.avatar-fallback>{{ $user->initials() }}</x-ui.avatar-fallback>
                    </x-ui.avatar>
                </x-ui.button>
            </x-ui.dropdown-menu-trigger>

            <x-ui.dropdown-menu-content align="end" class="min-w-64">
                <div class="flex items-center gap-3 px-2 py-2">
                    <x-ui.avatar>
                        <x-ui.avatar-fallback>{{ $user->initials() }}</x-ui.avatar-fallback>
                    </x-ui.avatar>

                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium">{{ $user->name }}</p>
                        <p class="truncate text-xs text-muted-foreground">{{ $user->email }}</p>
                    </div>
                </div>

                <x-ui.dropdown-menu-separator />

                <x-ui.dropdown-menu-item href="{{ route('customer.profile') }}" wire:navigate>
                    {{ __('Profil Saya') }}
                </x-ui.dropdown-menu-item>
                <x-ui.dropdown-menu-item href="{{ route('profile.edit') }}" wire:navigate>
                    {{ __('Pengaturan') }}
                </x-ui.dropdown-menu-item>
                <x-ui.dropdown-menu-item href="{{ route('customer.coming-soon', ['feature' => 'bantuan']) }}" wire:navigate>
                    {{ __('Pusat Bantuan') }}
                </x-ui.dropdown-menu-item>

                <x-ui.dropdown-menu-separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <x-ui.dropdown-menu-item type="submit" data-test="logout-button">
                        {{ __('Keluar') }}
                    </x-ui.dropdown-menu-item>
                </form>
            </x-ui.dropdown-menu-content>
        </x-ui.dropdown-menu>
    </div>
</header>
