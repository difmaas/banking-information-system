<?php

use App\Contracts\CoreGateway;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Bukti Pembayaran')] class extends Component
{
    public string $reference = '';

    public array $payment = [];

    public array $loan = [];

    public function mount(): void
    {
        $this->reference = (string) request()->query('ref', '');
        $core = app(CoreGateway::class);

        foreach ($core->getCustomerLoans(auth()->id()) as $loan) {
            foreach ($core->getCustomerLoanPayments(auth()->id(), (int) $loan['id']) as $payment) {
                if (($payment['reference'] ?? '') === $this->reference) {
                    $this->payment = $payment;
                    $this->loan = $loan;

                    return;
                }
            }
        }
    }
}; ?>

<section class="space-y-6">
    <div class="flex items-center justify-between print:hidden">
        <x-ui.button href="{{ route('customer.payments') }}" variant="ghost" wire:navigate>
            {{ __('Kembali') }}
        </x-ui.button>
        <x-ui.button x-on:click="window.print()" variant="primary">
            {{ __('Cetak Bukti') }}
        </x-ui.button>
    </div>

    @if ($payment === [])
        <x-ui.alert tone="warning">
            <x-lucide-circle-alert />
            <x-ui.alert-description>{{ __('Bukti pembayaran tidak ditemukan.') }}</x-ui.alert-description>
        </x-ui.alert>
    @else
        <x-ui.card class="mx-auto max-w-2xl p-8">
            <div class="flex items-start justify-between gap-6 border-b pb-6">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">BIS</p>
                    <h2 class="mt-1 text-xl font-semibold">{{ __('Bukti Pembayaran Angsuran') }}</h2>
                </div>
                <x-ui.badge tone="success">{{ __('Lunas') }}</x-ui.badge>
            </div>

            <dl class="mt-6 grid gap-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-muted-foreground">{{ __('Nomor Referensi') }}</dt>
                    <dd class="mt-1 font-mono font-semibold">{{ $payment['reference'] }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">{{ __('Tanggal Bayar') }}</dt>
                    <dd class="mt-1 font-semibold">{{ $payment['paid_at'] }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">{{ __('Produk') }}</dt>
                    <dd class="mt-1 font-semibold">{{ $loan['product'] ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-muted-foreground">{{ __('Angsuran Ke') }}</dt>
                    <dd class="mt-1 font-semibold">{{ $payment['installment_number'] }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-muted-foreground">{{ __('Nominal Dibayar') }}</dt>
                    <dd class="mt-1 text-2xl font-semibold">Rp {{ number_format((float) $payment['amount']) }}</dd>
                </div>
            </dl>

            <p class="mt-10 border-t pt-6 text-xs text-muted-foreground">
                {{ __('Bukti ini dicatat otomatis oleh sistem dan sah tanpa tanda tangan basah.') }}
            </p>
        </x-ui.card>
    @endif
</section>
