<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Fitur Segera Hadir')] class extends Component
{
    public string $feature = 'transfer';

    /** @var array<string, string> */
    public array $page = [];

    public function mount(string $feature): void
    {
        $this->feature = $feature;

        $pages = [
            'transfer' => [
                'title' => 'Transfer',
                'description' => 'Rencana: kirim dana ke sesama bank maupun bank lain dari portal.',
                'icon' => 'arrow-left-right',
            ],
            'qris' => [
                'title' => 'QRIS',
                'description' => 'Rencana: pembayaran merchant melalui kode QR.',
                'icon' => 'qr-code',
            ],
            'top-up' => [
                'title' => 'Top Up',
                'description' => 'Rencana: isi saldo dan pulsa langsung dari portal.',
                'icon' => 'plus-circle',
            ],
            'tagihan' => [
                'title' => 'Tagihan',
                'description' => 'Rencana: bayar tagihan listrik, air, dan internet dari portal.',
                'icon' => 'file-text',
            ],
            'kartu' => [
                'title' => 'Kartu',
                'description' => 'Rencana: kelola kartu dan limit nasabah.',
                'icon' => 'credit-card',
            ],
            'promo' => [
                'title' => 'Promo',
                'description' => 'Rencana: penawaran khusus nasabah BIS.',
                'icon' => 'gift',
            ],
            'bantuan' => [
                'title' => 'Bantuan',
                'description' => 'Pusat bantuan dan kanal komunikasi resmi BIS.',
                'icon' => 'circle-help',
            ],
        ];

        $this->page = $pages[$feature] ?? $pages['transfer'];
    }
}; ?>

<section class="space-y-6">
    <div class="relative overflow-hidden rounded-xl bg-primary p-6 text-primary-foreground sm:p-8">
        <div class="relative max-w-2xl">
            <span class="inline-flex size-14 items-center justify-center rounded-xl bg-primary-foreground/15 ring-1 ring-primary-foreground/20">
                <x-ui.icon :name="$page['icon']" class="size-7" />
            </span>

            <p class="mt-6 text-xs font-semibold uppercase tracking-[0.2em] text-primary-foreground/70">{{ __('Segera Hadir') }}</p>
            <h2 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">{{ $page['title'] }}</h2>
            <p class="mt-3 max-w-xl text-sm leading-6 text-primary-foreground/80 sm:text-base">{{ $page['description'] }}</p>

            <div class="mt-7 flex flex-wrap gap-3">
                <x-ui.button href="{{ route('dashboard') }}" variant="secondary" wire:navigate>
                    {{ __('Kembali ke Beranda') }}
                </x-ui.button>
                <x-ui.button href="{{ route('customer.coming-soon', ['feature' => 'bantuan']) }}" variant="ghost" class="text-primary-foreground hover:bg-primary-foreground/10 hover:text-primary-foreground" wire:navigate>
                    {{ __('Hubungi Bantuan') }}
                </x-ui.button>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <x-ui.card class="p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">{{ __('Status') }}</p>
            <p class="mt-2 text-lg font-semibold">{{ __('Dalam pengembangan') }}</p>
            <p class="mt-1 text-sm text-muted-foreground">{{ __('Kami sedang menyiapkan pengalaman terbaik untuk fitur ini.') }}</p>
        </x-ui.card>
        <x-ui.card class="p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">{{ __('Saat ini') }}</p>
            <p class="mt-2 text-lg font-semibold">{{ __('Status prototype') }}</p>
            <p class="mt-1 text-sm text-muted-foreground">{{ __('Fitur ini belum tersedia dalam prototype BIS. Fokus saat ini adalah pengelolaan kredit.') }}</p>
        </x-ui.card>
        <x-ui.card class="p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">{{ __('Sementara ini') }}</p>
            <p class="mt-2 text-lg font-semibold">{{ __('Gunakan menu lain') }}</p>
            <p class="mt-1 text-sm text-muted-foreground">{{ __('Anda tetap dapat mengelola pinjaman, angsuran, dan dokumen dari portal.') }}</p>
        </x-ui.card>
    </div>
</section>
