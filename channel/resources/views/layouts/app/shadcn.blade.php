<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-background text-foreground antialiased">
        <x-ui.sidebar-provider persist="bis_sidebar" mobile-breakpoint="1023">
    <x-ui.sidebar collapsible="icon" class="border-r bg-sidebar">
        <x-ui.sidebar-header>
            <a href="{{ route('dashboard') }}" wire:navigate class="flex min-w-0 items-center gap-2 rounded-md px-2 py-1.5">
                <span class="flex size-8 shrink-0 items-center justify-center rounded-md bg-primary text-primary-foreground">
                    <x-app-logo-icon class="size-5 fill-current" />
                </span>
                <span class="truncate text-sm font-semibold text-sidebar-foreground">
                    {{ config('app.name', 'BIS') }}
                </span>
            </a>
            <x-ui.sidebar-trigger />
        </x-ui.sidebar-header>

        <x-ui.sidebar-content>
            @php($activeFeature = request()->route('feature'))

            <x-ui.sidebar-group>
                <x-ui.sidebar-group-label>{{ __('Menu Utama') }}</x-ui.sidebar-group-label>
                <x-ui.sidebar-menu>
                    <x-ui.sidebar-menu-item>
                        <x-ui.sidebar-menu-button
                            href="{{ route('dashboard') }}"
                            :is-active="request()->routeIs('dashboard')"
                            tooltip="{{ __('Beranda') }}"
                        >
                            <x-lucide-home />
                            <span>{{ __('Beranda') }}</span>
                        </x-ui.sidebar-menu-button>
                    </x-ui.sidebar-menu-item>
                    <x-ui.sidebar-menu-item>
                        <x-ui.sidebar-menu-button
                            href="{{ route('customer.coming-soon', ['feature' => 'transfer']) }}"
                            :is-active="request()->routeIs('customer.coming-soon') && $activeFeature === 'transfer'"
                            tooltip="{{ __('Transfer') }}"
                        >
                            <x-lucide-arrow-left-right />
                            <span>{{ __('Transfer') }}</span>
                        </x-ui.sidebar-menu-button>
                    </x-ui.sidebar-menu-item>
                    <x-ui.sidebar-menu-item>
                        <x-ui.sidebar-menu-button
                            href="{{ route('customer.coming-soon', ['feature' => 'qris']) }}"
                            :is-active="request()->routeIs('customer.coming-soon') && $activeFeature === 'qris'"
                            tooltip="{{ __('QRIS') }}"
                        >
                            <x-lucide-qr-code />
                            <span>{{ __('QRIS') }}</span>
                        </x-ui.sidebar-menu-button>
                    </x-ui.sidebar-menu-item>
                    <x-ui.sidebar-menu-item>
                        <x-ui.sidebar-menu-button
                            href="{{ route('customer.coming-soon', ['feature' => 'top-up']) }}"
                            :is-active="request()->routeIs('customer.coming-soon') && $activeFeature === 'top-up'"
                            tooltip="{{ __('Top Up') }}"
                        >
                            <x-lucide-plus-circle />
                            <span>{{ __('Top Up') }}</span>
                        </x-ui.sidebar-menu-button>
                    </x-ui.sidebar-menu-item>
                </x-ui.sidebar-menu>
            </x-ui.sidebar-group>

            <x-ui.sidebar-group>
                <x-ui.sidebar-group-label>{{ __('Keuangan') }}</x-ui.sidebar-group-label>
                <x-ui.sidebar-menu>
                    <x-ui.sidebar-menu-item>
                        <x-ui.sidebar-menu-button
                            href="{{ route('customer.coming-soon', ['feature' => 'tagihan']) }}"
                            :is-active="request()->routeIs('customer.coming-soon') && $activeFeature === 'tagihan'"
                            tooltip="{{ __('Tagihan') }}"
                        >
                            <x-lucide-file-text />
                            <span>{{ __('Tagihan') }}</span>
                        </x-ui.sidebar-menu-button>
                    </x-ui.sidebar-menu-item>
                    <x-ui.sidebar-menu-item>
                        <x-ui.sidebar-menu-button
                            href="{{ route('customer.coming-soon', ['feature' => 'kartu']) }}"
                            :is-active="request()->routeIs('customer.coming-soon') && $activeFeature === 'kartu'"
                            tooltip="{{ __('Kartu') }}"
                        >
                            <x-lucide-credit-card />
                            <span>{{ __('Kartu') }}</span>
                        </x-ui.sidebar-menu-button>
                    </x-ui.sidebar-menu-item>
                    <x-ui.sidebar-menu-item>
                        <x-ui.sidebar-menu-button
                            href="{{ route('customer.loans') }}"
                            :is-active="request()->routeIs('customer.loans')"
                            tooltip="{{ __('Pinjaman') }}"
                        >
                            <x-lucide-banknote />
                            <span>{{ __('Pinjaman') }}</span>
                        </x-ui.sidebar-menu-button>
                    </x-ui.sidebar-menu-item>
                    <x-ui.sidebar-menu-item>
                        <x-ui.sidebar-menu-button
                            href="{{ route('customer.installments') }}"
                            :is-active="request()->routeIs('customer.installments')"
                            tooltip="{{ __('Angsuran') }}"
                        >
                            <x-lucide-calendar-days />
                            <span>{{ __('Angsuran') }}</span>
                        </x-ui.sidebar-menu-button>
                    </x-ui.sidebar-menu-item>
                    <x-ui.sidebar-menu-item>
                        <x-ui.sidebar-menu-button
                            href="{{ route('customer.payments') }}"
                            :is-active="request()->routeIs('customer.payments')"
                            tooltip="{{ __('Pembayaran') }}"
                        >
                            <x-lucide-receipt />
                            <span>{{ __('Pembayaran') }}</span>
                        </x-ui.sidebar-menu-button>
                    </x-ui.sidebar-menu-item>
                </x-ui.sidebar-menu>
            </x-ui.sidebar-group>

            <x-ui.sidebar-group>
                <x-ui.sidebar-group-label>{{ __('Akun & Informasi') }}</x-ui.sidebar-group-label>
                <x-ui.sidebar-menu>
                    <x-ui.sidebar-menu-item>
                        <x-ui.sidebar-menu-button
                            href="{{ route('customer.profile') }}"
                            :is-active="request()->routeIs('customer.profile')"
                            tooltip="{{ __('Profil') }}"
                        >
                            <x-lucide-user />
                            <span>{{ __('Profil') }}</span>
                        </x-ui.sidebar-menu-button>
                    </x-ui.sidebar-menu-item>
                    <x-ui.sidebar-menu-item>
                        <x-ui.sidebar-menu-button
                            href="{{ route('customer.documents') }}"
                            :is-active="request()->routeIs('customer.documents')"
                            tooltip="{{ __('Dokumen') }}"
                        >
                            <x-lucide-folder />
                            <span>{{ __('Dokumen') }}</span>
                        </x-ui.sidebar-menu-button>
                    </x-ui.sidebar-menu-item>
                    <x-ui.sidebar-menu-item>
                        <x-ui.sidebar-menu-button
                            href="{{ route('customer.credit-history') }}"
                            :is-active="request()->routeIs('customer.credit-history')"
                            tooltip="{{ __('Riwayat Kredit') }}"
                        >
                            <x-lucide-book-open />
                            <span>{{ __('Riwayat Kredit') }}</span>
                        </x-ui.sidebar-menu-button>
                    </x-ui.sidebar-menu-item>
                    <x-ui.sidebar-menu-item>
                        <x-ui.sidebar-menu-button
                            href="{{ route('customer.coming-soon', ['feature' => 'promo']) }}"
                            :is-active="request()->routeIs('customer.coming-soon') && $activeFeature === 'promo'"
                            tooltip="{{ __('Promo') }}"
                        >
                            <x-lucide-gift />
                            <span>{{ __('Promo') }}</span>
                        </x-ui.sidebar-menu-button>
                    </x-ui.sidebar-menu-item>
                    <x-ui.sidebar-menu-item>
                        <x-ui.sidebar-menu-button
                            href="{{ route('customer.coming-soon', ['feature' => 'bantuan']) }}"
                            :is-active="request()->routeIs('customer.coming-soon') && $activeFeature === 'bantuan'"
                            tooltip="{{ __('Bantuan') }}"
                        >
                            <x-lucide-circle-help />
                            <span>{{ __('Bantuan') }}</span>
                        </x-ui.sidebar-menu-button>
                    </x-ui.sidebar-menu-item>
                </x-ui.sidebar-menu>
            </x-ui.sidebar-group>
        </x-ui.sidebar-content>

        <x-ui.sidebar-footer>
            <div class="rounded-lg bg-primary/10 p-3 text-sm text-foreground">
                <p class="font-medium">{{ __('Butuh bantuan?') }}</p>
                <p class="mt-1 text-xs text-muted-foreground">{{ __('Tim kami siap membantu setiap hari, 08.00–22.00 WIB.') }}</p>
                <x-ui.button
                    href="{{ route('customer.coming-soon', ['feature' => 'bantuan']) }}"
                    variant="outline"
                    size="sm"
                    class="mt-3 w-full"
                    wire:navigate
                >
                    {{ __('Hubungi Kami') }}
                </x-ui.button>
            </div>
            <x-ui.sidebar-menu>
                <x-ui.sidebar-menu-item>
                    <x-ui.sidebar-menu-button
                        href="{{ route('profile.edit') }}"
                        :is-active="request()->routeIs('profile.edit')"
                        tooltip="{{ __('Pengaturan') }}"
                    >
                        <x-lucide-settings />
                        <span>{{ __('Pengaturan') }}</span>
                    </x-ui.sidebar-menu-button>
                </x-ui.sidebar-menu-item>
            </x-ui.sidebar-menu>
        </x-ui.sidebar-footer>
    </x-ui.sidebar>

    <x-ui.sidebar-inset>
        <x-portal.topbar :title="$title ?? null" />

        <div class="mx-auto w-full max-w-7xl px-4 pb-12 pt-6 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    </x-ui.sidebar-inset>
</x-ui.sidebar-provider>

        <livewire:pin-lock />

        <x-ui.sonner />
        <x-ui.sonner-flash />
    </body>
</html>
