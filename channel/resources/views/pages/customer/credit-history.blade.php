<?php

use App\Contracts\CoreGateway;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Riwayat Kredit')] class extends Component
{
    public array $history = [];

    public function mount(): void
    {
        $this->history = app(CoreGateway::class)->getCustomerCreditHistory(auth()->id());
    }
}; ?>

<section class="space-y-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">{{ __('Akun') }}</p>
        <h2 class="mt-1 text-2xl font-semibold tracking-tight">{{ __('Riwayat Kredit') }}</h2>
        <p class="mt-1 text-sm text-muted-foreground">{{ __('Rekam jejak pinjaman Anda di lembaga keuangan.') }}</p>
    </div>

    <x-ui.card class="overflow-hidden">
        <x-ui.table>
            <x-ui.table-header>
                <x-ui.table-row class="bg-muted/50">
                    <x-ui.table-head>{{ __('Institusi') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Jenis') }}</x-ui.table-head>
                    <x-ui.table-head class="text-end">{{ __('Nominal') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Status') }}</x-ui.table-head>
                    <x-ui.table-head>{{ __('Tanggal') }}</x-ui.table-head>
                </x-ui.table-row>
            </x-ui.table-header>
            <x-ui.table-body>
                @forelse ($history as $item)
                    @php($tone = match ($item['status'] ?? '') { 'Lancar' => 'success', 'Macet' => 'danger', default => 'warning' })
                    <x-ui.table-row>
                        <x-ui.table-cell class="font-medium">{{ $item['institution'] }}</x-ui.table-cell>
                        <x-ui.table-cell>{{ $item['loan_type'] }}</x-ui.table-cell>
                        <x-ui.table-cell class="text-end font-semibold">Rp {{ number_format((float) $item['amount']) }}</x-ui.table-cell>
                        <x-ui.table-cell><x-ui.badge :tone="$tone" size="sm">{{ $item['status'] }}</x-ui.badge></x-ui.table-cell>
                        <x-ui.table-cell>{{ $item['date'] }}</x-ui.table-cell>
                    </x-ui.table-row>
                @empty
                    <x-ui.table-row>
                        <x-ui.table-cell colspan="5" class="py-10">
                            <x-ui.empty>
                                <x-ui.empty-header>
                                    <x-lucide-book-open class="size-6 text-muted-foreground" />
                                    <x-ui.empty-title>{{ __('Belum ada riwayat kredit') }}</x-ui.empty-title>
                                    <x-ui.empty-description>{{ __('Riwayat akan muncul setelah Anda memiliki pinjaman.') }}</x-ui.empty-description>
                                </x-ui.empty-header>
                            </x-ui.empty>
                        </x-ui.table-cell>
                    </x-ui.table-row>
                @endforelse
            </x-ui.table-body>
        </x-ui.table>
    </x-ui.card>
</section>
