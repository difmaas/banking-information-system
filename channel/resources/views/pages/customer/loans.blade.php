<?php

use App\Contracts\CoreGateway;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Pinjaman')] class extends Component
{
    public array $loans = [];

    public array $products = [];

    public function mount(): void
    {
        $core = app(CoreGateway::class);
        $this->loans = $core->getCustomerLoans(auth()->id());
        $this->products = $core->getLoanProducts();
    }

    public function totalOutstanding(): float
    {
        return array_sum(array_map(
            fn (array $loan): float => (float) ($loan['outstanding'] ?? 0),
            $this->loans,
        ));
    }
}; ?>

<section class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">{{ __('Kredit') }}</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight">{{ __('Pinjaman Saya') }}</h2>
            <p class="mt-1 text-sm text-muted-foreground">{{ __('Status pengajuan dan kewajiban pinjaman Anda.') }}</p>
        </div>

        <x-ui.button href="{{ route('customer.loans.create') }}" icon="plus" wire:navigate>
            {{ __('Ajukan Kredit') }}
        </x-ui.button>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <x-ui.card class="flex items-center justify-between p-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">{{ __('Total pinjaman') }}</p>
                <p class="mt-2 text-2xl font-semibold">{{ count($loans) }}</p>
            </div>
            <span class="flex size-11 items-center justify-center rounded-xl bg-primary/10 text-primary"><x-lucide-banknote class="size-5" /></span>
        </x-ui.card>

        <x-ui.card class="flex items-center justify-between p-5">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">{{ __('Total sisa') }}</p>
                <p class="mt-2 text-2xl font-semibold">Rp {{ number_format($this->totalOutstanding()) }}</p>
            </div>
            <span class="flex size-11 items-center justify-center rounded-xl bg-info/10 text-info"><x-lucide-chart-bar class="size-5" /></span>
        </x-ui.card>
    </div>

    <x-ui.card class="overflow-hidden">
        <div class="border-b px-5 py-4">
            <h3 class="text-base font-semibold">{{ __('Daftar Pinjaman') }}</h3>
        </div>

        <x-ui.table>
            <x-ui.table-header>
                <x-ui.table-row class="bg-muted/50">
                    <x-ui.table-head>{{ __('Produk') }}</x-ui.table-head>
                    <x-ui.table-head class="text-end">{{ __('Nominal') }}</x-ui.table-head>
                    <x-ui.table-head class="text-end">{{ __('Sisa') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Tenor') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Status') }}</x-ui.table-head>
                    <x-ui.table-head class="text-end">{{ __('Aksi') }}</x-ui.table-head>
                </x-ui.table-row>
            </x-ui.table-header>
            <x-ui.table-body>
                @forelse ($loans as $loan)
                    @php($tone = match ($loan['status'] ?? '') { 'Aktif' => 'success', 'Lunas' => 'info', 'Ditolak' => 'danger', default => 'warning' })
                    <x-ui.table-row>
                        <x-ui.table-cell class="font-medium">{{ $loan['product'] }}</x-ui.table-cell>
                        <x-ui.table-cell class="text-end">Rp {{ number_format((float) $loan['amount']) }}</x-ui.table-cell>
                        <x-ui.table-cell class="text-end font-semibold">Rp {{ number_format((float) $loan['outstanding']) }}</x-ui.table-cell>
                        <x-ui.table-cell>{{ $loan['tenor_months'] }} {{ __('bulan') }}</x-ui.table-cell>
                        <x-ui.table-cell><x-ui.badge :tone="$tone" size="sm">{{ $loan['status'] }}</x-ui.badge></x-ui.table-cell>
                        <x-ui.table-cell class="text-end">
                            <x-ui.button href="{{ route('customer.installments', ['loan' => $loan['id']]) }}" variant="ghost" size="sm" wire:navigate>
                                {{ __('Angsuran') }}
                            </x-ui.button>
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @empty
                    <x-ui.table-row>
                        <x-ui.table-cell colspan="6" class="py-10">
                            <x-ui.empty>
                                <x-ui.empty-header>
                                    <x-lucide-banknote class="size-6 text-muted-foreground" />
                                    <x-ui.empty-title>{{ __('Belum ada pinjaman') }}</x-ui.empty-title>
                                    <x-ui.empty-description>{{ __('Ajukan kredit pertama Anda untuk memulai.') }}</x-ui.empty-description>
                                </x-ui.empty-header>
                            </x-ui.empty>
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @endforelse
            </x-ui.table-body>
        </x-ui.table>
    </x-ui.card>

    <div>
        <h3 class="text-base font-semibold">{{ __('Produk Tersedia') }}</h3>
        <p class="mt-1 text-sm text-muted-foreground">{{ __('Pilih produk yang sesuai dengan kebutuhan Anda.') }}</p>

        <div class="mt-4 grid gap-4 md:grid-cols-2">
            @forelse ($products as $product)
                <x-ui.card class="flex flex-col justify-between p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h4 class="text-base font-semibold">{{ $product['name'] }}</h4>
                            <p class="mt-1 text-sm text-muted-foreground">{{ $product['description'] }}</p>
                        </div>
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"><x-lucide-banknote class="size-5" /></span>
                    </div>
                    <x-ui.badge tone="success" size="sm" class="mt-4">
                        {{ __('Bunga :rate% / bulan', ['rate' => $product['interest_rate']]) }}
                    </x-ui.badge>
                    <x-ui.button href="{{ route('customer.loans.create') }}" variant="outline" size="sm" class="mt-4 w-full" wire:navigate>
                        {{ __('Ajukan Produk Ini') }}
                    </x-ui.button>
                </x-ui.card>
            @empty
                <x-ui.card class="p-6 text-center md:col-span-2">
                    <p class="text-sm text-muted-foreground">{{ __('Belum ada produk pinjaman yang tersedia.') }}</p>
                </x-ui.card>
            @endforelse
        </div>
    </div>
</section>
