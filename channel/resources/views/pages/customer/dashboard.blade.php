<?php

use App\Contracts\CoreGateway;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Beranda')] class extends Component
{
    public array $profile = [];

    public array $loans = [];

    public array $installments = [];

    public function mount(): void
    {
        $core = app(CoreGateway::class);
        $this->profile = $core->getCustomerProfile(auth()->id());
        $this->loans = $core->getCustomerLoans(auth()->id());
        $this->installments = [];

        foreach ($this->loans as $loan) {
            $loanId = (int) ($loan['id'] ?? 0);

            if ($loanId === 0) {
                continue;
            }

            foreach ($core->getCustomerLoanInstallments(auth()->id(), $loanId) as $installment) {
                if (($installment['status'] ?? '') === 'Lunas') {
                    continue;
                }

                $this->installments[] = array_merge($installment, [
                    'loan_id' => $loanId,
                    'product' => $loan['product'] ?? '-',
                ]);
            }
        }

        usort(
            $this->installments,
            fn (array $a, array $b): int => strcmp((string) ($a['due_date'] ?? ''), (string) ($b['due_date'] ?? '')),
        );
    }

    public function totalOutstanding(): float
    {
        return array_sum(array_map(
            fn (array $loan): float => (float) ($loan['outstanding'] ?? 0),
            $this->loans,
        ));
    }

    public function totalPlafond(): float
    {
        return array_sum(array_map(
            fn (array $loan): float => (float) ($loan['amount'] ?? 0),
            $this->loans,
        ));
    }

    public function activeLoanCount(): int
    {
        return count(array_filter(
            $this->loans,
            fn (array $loan): bool => ($loan['status'] ?? '') === 'Aktif',
        ));
    }

    /**
     * @return array<string, mixed>|null
     */
    public function nextInstallment(): ?array
    {
        return collect($this->installments)
            ->first(fn (array $installment): bool => ($installment['status'] ?? '') !== 'Lunas');
    }
}; ?>

@php
    $next = $this->nextInstallment();
    $firstName = explode(' ', (string) ($profile['name'] ?? auth()->user()->name))[0];
    $collectibility = $profile['collectibility'] ?? '-';
    $tone = $collectibility === 'Lancar' ? 'success' : 'warning';
@endphp

<section class="space-y-6">
    <div class="relative overflow-hidden rounded-xl bg-primary p-6 text-primary-foreground sm:p-8">
        <div class="relative flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <x-ui.badge variant="secondary" class="bg-primary-foreground/10 text-primary-foreground ring-1 ring-primary-foreground/20">
                    <x-lucide-shield-check />
                    {{ __('Profil terdaftar') }}
                </x-ui.badge>

                <h2 class="mt-5 text-3xl font-semibold tracking-tight sm:text-4xl">
                    {{ __('Halo, :name', ['name' => $firstName]) }}
                </h2>

                <div class="mt-7 flex flex-wrap gap-3">
                    <x-ui.button href="{{ route('customer.loans.create') }}" variant="secondary" wire:navigate>
                        {{ __('Ajukan Kredit') }}
                    </x-ui.button>
                </div>
            </div>

            <div class="w-full rounded-xl bg-primary-foreground/10 p-5 ring-1 ring-primary-foreground/15 lg:w-72">
                <p class="text-xs font-medium uppercase tracking-[0.16em] text-primary-foreground/70">{{ __('Total sisa pinjaman') }}</p>
                <p class="mt-2 text-2xl font-semibold tracking-tight">Rp {{ number_format($this->totalOutstanding()) }}</p>

                <div class="mt-4 flex items-center justify-between border-t border-primary-foreground/15 pt-3 text-sm">
                    <span class="text-primary-foreground/70">{{ __('Kolektibilitas') }}</span>
                    <x-ui.badge :tone="$tone">{{ $collectibility }}</x-ui.badge>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-ui.card class="p-5">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">{{ __('Pinjaman aktif') }}</p>
                <span class="flex size-9 items-center justify-center rounded-lg bg-primary/10 text-primary"><x-lucide-banknote class="size-5" /></span>
            </div>
            <p class="mt-3 text-2xl font-semibold">{{ $this->activeLoanCount() }}</p>
            <p class="mt-1 text-xs text-muted-foreground">{{ __('dari :count total pinjaman', ['count' => count($loans)]) }}</p>
        </x-ui.card>

        <x-ui.card class="p-5">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">{{ __('Total plafond') }}</p>
                <span class="flex size-9 items-center justify-center rounded-lg bg-info/10 text-info"><x-lucide-chart-bar class="size-5" /></span>
            </div>
            <p class="mt-3 text-2xl font-semibold">Rp {{ number_format($this->totalPlafond()) }}</p>
            <p class="mt-1 text-xs text-muted-foreground">{{ __('Akumulasi seluruh pinjaman') }}</p>
        </x-ui.card>

        <x-ui.card class="p-5">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">{{ __('Penghasilan bulanan') }}</p>
                <span class="flex size-9 items-center justify-center rounded-lg bg-warning/10 text-warning"><x-lucide-wallet class="size-5" /></span>
            </div>
            <p class="mt-3 text-2xl font-semibold">Rp {{ number_format((float) ($profile['monthly_income'] ?? 0)) }}</p>
            <p class="mt-1 text-xs text-muted-foreground">{{ __('Sesuai data profil Anda') }}</p>
        </x-ui.card>
    </div>

    <div class="grid gap-5 lg:grid-cols-5">
        <x-ui.card class="p-5 sm:p-6 lg:col-span-3">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-semibold">{{ __('Pinjaman terbaru') }}</h3>
                    <p class="mt-1 text-sm text-muted-foreground">{{ __('Pantau status pengajuan dan sisa kewajiban.') }}</p>
                </div>
                <x-ui.button href="{{ route('customer.loans') }}" variant="ghost" size="sm" wire:navigate>
                    {{ __('Lihat semua') }}
                </x-ui.button>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($loans as $loan)
                    @php($loanTone = match ($loan['status'] ?? '') { 'Aktif' => 'success', 'Lunas' => 'info', 'Ditolak' => 'danger', default => 'warning' })
                    <a
                        href="{{ route('customer.installments', ['loan' => $loan['id']]) }}"
                        wire:navigate
                        class="flex items-center justify-between gap-3 rounded-lg border p-4 transition hover:bg-accent/50"
                    >
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold">{{ $loan['product'] }}</p>
                            <p class="mt-0.5 text-xs text-muted-foreground">{{ __('Tenor :tenor bulan', ['tenor' => $loan['tenor_months']]) }} · Rp {{ number_format((float) $loan['amount']) }}</p>
                        </div>
                        <div class="text-end">
                            <p class="text-sm font-semibold">Rp {{ number_format((float) $loan['outstanding']) }}</p>
                            <x-ui.badge :tone="$loanTone" size="sm" class="mt-1">{{ $loan['status'] }}</x-ui.badge>
                        </div>
                    </a>
                @empty
                    <x-ui.empty>
                        <x-ui.empty-header>
                            <x-lucide-banknote class="size-6 text-muted-foreground" />
                            <x-ui.empty-title>{{ __('Belum ada pinjaman') }}</x-ui.empty-title>
                            <x-ui.empty-description>{{ __('Ajukan kredit pertama Anda untuk mulai membangun riwayat.') }}</x-ui.empty-description>
                        </x-ui.empty-header>
                        <x-ui.empty-content>
                            <x-ui.button href="{{ route('customer.loans.create') }}" size="sm" wire:navigate>
                                {{ __('Ajukan Kredit') }}
                            </x-ui.button>
                        </x-ui.empty-content>
                    </x-ui.empty>
                @endforelse
            </div>
        </x-ui.card>

        <div class="space-y-5 lg:col-span-2">
            <x-ui.card class="p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold">{{ __('Angsuran berikutnya') }}</h3>
                    <span class="flex size-9 items-center justify-center rounded-lg bg-primary/10 text-primary"><x-lucide-calendar-days class="size-5" /></span>
                </div>

                @if ($next)
                    <p class="mt-4 text-xs font-medium uppercase tracking-[0.16em] text-primary">{{ $next['product'] ?? '-' }}</p>
                    <p class="mt-1 text-2xl font-semibold">Rp {{ number_format((float) ($next['amount'] ?? 0)) }}</p>
                    <p class="mt-1 text-sm text-muted-foreground">{{ __('Jatuh tempo :date', ['date' => $next['due_date'] ?? '-']) }}</p>
                    <div class="mt-4 flex items-center justify-between rounded-lg bg-muted px-3 py-2 text-sm">
                        <span class="text-muted-foreground">{{ __('Angsuran ke-:number', ['number' => $next['number'] ?? '-']) }}</span>
                        <x-ui.badge tone="warning" size="sm">{{ $next['status'] ?? '-' }}</x-ui.badge>
                    </div>
                @else
                    <x-ui.empty class="mt-4">
                        <x-ui.empty-header>
                            <x-ui.empty-title>{{ __('Tidak ada tagihan mendatang') }}</x-ui.empty-title>
                            <x-ui.empty-description>{{ __('Belum ada tagihan yang perlu dibayar.') }}</x-ui.empty-description>
                        </x-ui.empty-header>
                    </x-ui.empty>
                @endif

                <x-ui.button href="{{ route('customer.installments') }}" variant="ghost" size="sm" class="mt-3 w-full" wire:navigate>
                    {{ __('Lihat jadwal angsuran') }}
                </x-ui.button>
            </x-ui.card>
        </div>
    </div>
</section>
