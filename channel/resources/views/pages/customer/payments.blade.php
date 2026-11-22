<?php

use App\Contracts\CoreGateway;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Pembayaran')] class extends Component
{
    public array $loans = [];

    public array $payments = [];

    public int $loanId = 0;

    public function mount(): void
    {
        $core = app(CoreGateway::class);
        $this->loans = $core->getCustomerLoans(auth()->id());
        $requested = request()->query('loan');

        $this->loanId = $requested !== null
            ? (int) $requested
            : (int) ($this->loans[0]['id'] ?? 0);

        $this->loadPayments();
    }

    public function updatedLoanId(): void
    {
        $this->loadPayments();
    }

    private function loadPayments(): void
    {
        $this->payments = $this->loanId > 0
            ? app(CoreGateway::class)->getCustomerLoanPayments(auth()->id(), $this->loanId)
            : [];
    }
}; ?>

<section class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">{{ __('Kredit') }}</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight">{{ __('Riwayat Pembayaran') }}</h2>
            <p class="mt-1 text-sm text-muted-foreground">{{ __('Bukti pembayaran angsuran yang telah tercatat.') }}</p>
        </div>

        @if ($loans !== [])
            <div class="w-full sm:w-72">
                <x-ui.field>
                    <x-ui.field-label for="loan_id">{{ __('Pilih Pinjaman') }}</x-ui.field-label>
                    <x-ui.select native id="loan_id" wire:model.live="loanId">
                        @foreach ($loans as $loan)
                            <option value="{{ $loan['id'] }}">
                                {{ $loan['product'] ?? __('Pinjaman #:id', ['id' => $loan['id']]) }} · Rp {{ number_format((float) ($loan['amount'] ?? 0)) }}
                            </option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>
            </div>
        @endif
    </div>

    <x-ui.card class="overflow-hidden">
        <x-ui.table>
            <x-ui.table-header>
                <x-ui.table-row class="bg-muted/50">
                    <x-ui.table-head>{{ __('Referensi') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Angsuran') }}</x-ui.table-head>
                    <x-ui.table-head class="text-end">{{ __('Nominal') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Tanggal Bayar') }}</x-ui.table-head>
                </x-ui.table-row>
            </x-ui.table-header>
            <x-ui.table-body>
                @forelse ($payments as $payment)
                    <x-ui.table-row>
                        <x-ui.table-cell class="font-mono text-xs">
                            <x-ui.button
                                variant="link"
                                size="sm"
                                href="{{ route('customer.payments.receipt', ['ref' => $payment['reference']]) }}"
                                wire:navigate
                            >
                                {{ $payment['reference'] }}
                            </x-ui.button>
                        </x-ui.table-cell>
                        <x-ui.table-cell>{{ __('Ke-:number', ['number' => $payment['installment_number']]) }}</x-ui.table-cell>
                        <x-ui.table-cell class="text-end font-semibold">Rp {{ number_format((float) $payment['amount']) }}</x-ui.table-cell>
                        <x-ui.table-cell>{{ $payment['paid_at'] }}</x-ui.table-cell>
                    </x-ui.table-row>
                @empty
                    <x-ui.table-row>
                        <x-ui.table-cell colspan="4" class="py-10">
                            <x-ui.empty>
                                <x-ui.empty-header>
                                    <x-lucide-receipt class="size-6 text-muted-foreground" />
                                    <x-ui.empty-title>{{ __('Belum ada pembayaran') }}</x-ui.empty-title>
                                    <x-ui.empty-description>{{ __('Pembayaran angsuran akan muncul di sini.') }}</x-ui.empty-description>
                                </x-ui.empty-header>
                            </x-ui.empty>
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @endforelse
            </x-ui.table-body>
        </x-ui.table>
    </x-ui.card>
</section>
