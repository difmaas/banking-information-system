<?php

use App\Contracts\CoreGateway;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Angsuran')] class extends Component
{
    public array $loans = [];

    public array $installments = [];

    public int $loanId = 0;

    public function mount(): void
    {
        $core = app(CoreGateway::class);
        $this->loans = $core->getCustomerLoans(auth()->id());
        $requested = request()->query('loan');

        $this->loanId = $requested !== null
            ? (int) $requested
            : (int) ($this->loans[0]['id'] ?? 0);

        $this->loadInstallments();
    }

    public function updatedLoanId(): void
    {
        $this->loadInstallments();
    }

    private function loadInstallments(): void
    {
        $this->installments = $this->loanId > 0
            ? app(CoreGateway::class)->getCustomerLoanInstallments(auth()->id(), $this->loanId)
            : [];
    }
}; ?>

<section class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">{{ __('Kredit') }}</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight">{{ __('Jadwal Angsuran') }}</h2>
            <p class="mt-1 text-sm text-muted-foreground">{{ __('Pantau jatuh tempo dan status pembayaran angsuran.') }}</p>
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
                    <x-ui.table-head>{{ __('Angsuran') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Jatuh Tempo') }}</x-ui.table-head>
                    <x-ui.table-head class="text-end">{{ __('Nominal') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Status') }}</x-ui.table-head>
                </x-ui.table-row>
            </x-ui.table-header>
            <x-ui.table-body>
                @forelse ($installments as $installment)
                    <x-ui.table-row>
                        <x-ui.table-cell class="font-medium">{{ __('Ke-:number', ['number' => $installment['number']]) }}</x-ui.table-cell>
                        <x-ui.table-cell>{{ $installment['due_date'] }}</x-ui.table-cell>
                        <x-ui.table-cell class="text-end font-semibold">Rp {{ number_format((float) $installment['amount']) }}</x-ui.table-cell>
                        <x-ui.table-cell>
                            <x-ui.badge :tone="($installment['status'] ?? '') === 'Lunas' ? 'success' : 'warning'" size="sm">
                                {{ $installment['status'] }}
                            </x-ui.badge>
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @empty
                    <x-ui.table-row>
                        <x-ui.table-cell colspan="4" class="py-10">
                            <x-ui.empty>
                                <x-ui.empty-header>
                                    <x-lucide-calendar-days class="size-6 text-muted-foreground" />
                                    <x-ui.empty-title>{{ __('Belum ada jadwal angsuran') }}</x-ui.empty-title>
                                    <x-ui.empty-description>{{ __('Jadwal akan muncul setelah pinjaman Anda disetujui.') }}</x-ui.empty-description>
                                </x-ui.empty-header>
                            </x-ui.empty>
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @endforelse
            </x-ui.table-body>
        </x-ui.table>
    </x-ui.card>
</section>
