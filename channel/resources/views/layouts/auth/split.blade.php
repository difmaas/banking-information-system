<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-background text-foreground antialiased">
        <div class="grid min-h-svh lg:grid-cols-2">
            <div class="relative hidden flex-col justify-between overflow-hidden bg-primary p-10 text-primary-foreground lg:flex">
                <div class="absolute -right-24 -top-24 size-72 rounded-full bg-white/10 blur-3xl"></div>

                <a href="{{ route('home') }}" wire:navigate class="relative z-10 flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-lg bg-primary-foreground text-primary">
                        <x-app-logo-icon class="size-6 fill-current" />
                    </span>
                    <span class="font-semibold">{{ config('app.name', 'BIS') }}</span>
                </a>

                <div class="relative z-10 mt-auto max-w-lg">
                    <span class="inline-flex rounded-full bg-primary-foreground/10 px-3 py-1 text-xs font-medium ring-1 ring-primary-foreground/20">
                        {{ __('Banking Information System') }}
                    </span>
                    <h1 class="mt-6 text-4xl font-semibold leading-tight tracking-tight">
                        {{ __('Kelola kredit Anda dalam satu portal.') }}
                    </h1>
                    <p class="mt-3 text-sm leading-6 text-primary-foreground/80">
                        {{ __('Registrasi, pengajuan, status pinjaman, dan jadwal angsuran—semuanya bisa dipantau di sini.') }}
                    </p>
                </div>

                <p class="relative z-10 mt-10 text-xs text-primary-foreground/70">© {{ now()->year }} BIS</p>
            </div>

            <div class="relative flex min-h-svh flex-col items-center justify-center p-6 sm:p-10">
                <button
                    type="button"
                    class="absolute right-5 top-5 inline-flex size-10 items-center justify-center rounded-lg text-muted-foreground transition hover:bg-accent hover:text-foreground"
                    x-on:click="document.documentElement.classList.toggle('dark')"
                    aria-label="{{ __('Ubah tema') }}"
                >
                    <x-lucide-moon class="size-5 dark:hidden" />
                    <x-lucide-sun class="hidden size-5 dark:block" />
                </button>

                <div class="w-full max-w-md">
                    <a href="{{ route('home') }}" wire:navigate class="mb-6 flex items-center justify-center gap-2 lg:hidden">
                        <span class="flex size-9 items-center justify-center rounded-md bg-primary text-primary-foreground">
                            <x-app-logo-icon class="size-5 fill-current" />
                        </span>
                        <span class="font-semibold">{{ config('app.name', 'BIS') }}</span>
                    </a>

                    <x-ui.card class="sm:p-2">
                        {{ $slot }}
                    </x-ui.card>
                </div>
            </div>
        </div>

        <x-ui.sonner />
<x-ui.sonner-flash />
    </body>
</html>
