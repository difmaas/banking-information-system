<?php

use App\Contracts\CoreGateway;
use App\Services\PinGate;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Ajukan Kredit')] class extends Component
{
    public array $products = [];

    public array $requiredDocuments = [];

    public array $documents = [];

    public int $product_id = 1;

    public string $amount = '';

    public string $tenor_months = '12';

    public array $simulation = [];

    public bool $needsPin = false;

    public string $pin = '';

    public function mount(): void
    {
        $core = app(CoreGateway::class);
        $this->products = $core->getLoanProducts();
        $this->requiredDocuments = $core->requiredDocuments();
        $this->documents = $core->getCustomerDocuments(auth()->id());

        if ($this->products !== []) {
            $this->product_id = (int) $this->products[0]['id'];
        }
    }

    public function simulate(): void
    {
        $this->validate([
            'product_id' => ['required', 'integer', 'min:1'],
            'amount' => ['required', 'numeric', 'min:1'],
            'tenor_months' => ['required', 'integer', 'min:1'],
        ]);

        $this->simulation = app(CoreGateway::class)->simulateInstallment(
            $this->product_id,
            (float) $this->amount,
            (int) $this->tenor_months,
        );
    }

    public function submit()
    {
        $this->validate([
            'product_id' => ['required', 'integer', 'min:1'],
            'amount' => ['required', 'numeric', 'min:1'],
            'tenor_months' => ['required', 'integer', 'min:1'],
        ]);

        if ($this->missingDocuments() !== []) {
            $this->dispatch('toast', type: 'warning', description: 'Lengkapi dokumen wajib terlebih dahulu.');

            return null;
        }

        if (! app(PinGate::class)->isVerified()) {
            $this->needsPin = true;
            $this->dispatch('toast', type: 'info', description: 'Masukkan PIN untuk mengonfirmasi pengajuan.');

            return null;
        }

        return $this->submitApplication();
    }

    public function confirmAndSubmit()
    {
        $this->validate([
            'pin' => ['required', 'digits:6'],
        ]);

        if (! app(PinGate::class)->verify(auth()->user(), $this->pin)) {
            $this->addError('pin', 'PIN tidak valid.');

            return null;
        }

        $this->needsPin = false;
        $this->reset('pin');

        return $this->submitApplication();
    }

    private function submitApplication()
    {
        app(CoreGateway::class)->storeCustomerLoan(auth()->id(), [
            'product_id' => $this->product_id,
            'amount' => (float) $this->amount,
            'tenor_months' => (int) $this->tenor_months,
        ]);

        $this->dispatch('toast', type: 'success', description: 'Pengajuan kredit berhasil dibuat.');

        return redirect()->route('customer.loans');
    }

    /**
     * @return array<int, string>
     */
    public function missingDocuments(): array
    {
        $uploaded = collect($this->documents)->pluck('type')->all();

        return array_values(array_diff($this->requiredDocuments, $uploaded));
    }
}; ?>

<section class="space-y-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">{{ __('Kredit') }}</p>
        <h2 class="mt-1 text-2xl font-semibold tracking-tight">{{ __('Ajukan Kredit') }}</h2>
        <p class="mt-1 text-sm text-muted-foreground">{{ __('Pilih produk, nominal, dan tenor, lalu lihat simulasi angsuran sebelum mengirim pengajuan.') }}</p>
    </div>

    <div class="grid gap-5 lg:grid-cols-5">
        <x-ui.card class="p-5 sm:p-6 lg:col-span-3">
            <h3 class="text-base font-semibold">{{ __('Detail Pengajuan') }}</h3>

            <form wire:submit="submit" class="mt-5 space-y-4">
                <x-ui.field>
                    <x-ui.field-label for="product_id">{{ __('Produk Kredit') }}</x-ui.field-label>
                    <x-ui.select native id="product_id" wire:model="product_id">
                        @foreach ($products as $product)
                            <option value="{{ $product['id'] }}">{{ $product['name'] }}</option>
                        @endforeach
                    </x-ui.select>
                    <x-ui.field-error :messages="$errors->get('product_id')" />
                </x-ui.field>

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-ui.field>
                        <x-ui.field-label for="amount">{{ __('Nominal Pinjaman') }}</x-ui.field-label>
                        <x-ui.input id="amount" wire:model="amount" type="number" min="1" placeholder="10000000" />
                        <x-ui.field-error :messages="$errors->get('amount')" />
                    </x-ui.field>

                    <x-ui.field>
                        <x-ui.field-label for="tenor_months">{{ __('Tenor (bulan)') }}</x-ui.field-label>
                        <x-ui.input id="tenor_months" wire:model="tenor_months" type="number" min="1" placeholder="12" />
                        <x-ui.field-error :messages="$errors->get('tenor_months')" />
                    </x-ui.field>
                </div>

                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <x-ui.button type="button" wire:click="simulate" variant="outline">
                        <x-lucide-calculator class="size-4" />
                        {{ __('Hitung Simulasi') }}
                    </x-ui.button>
                    <x-ui.button type="submit" :disabled="$this->missingDocuments() !== []">
                        {{ __('Kirim Pengajuan') }}
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <div class="space-y-5 lg:col-span-2">
            <x-ui.card class="p-5 sm:p-6">
                <h3 class="text-base font-semibold">{{ __('Simulasi Angsuran') }}</h3>

                @if ($simulation !== [])
                    <p class="mt-4 text-2xl font-semibold">
                        Rp {{ number_format((float) $simulation['monthly_installment']) }}
                        <span class="text-sm font-normal text-muted-foreground">/ {{ __('bulan') }}</span>
                    </p>

                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-muted-foreground">{{ __('Total bunga') }}</dt>
                            <dd class="font-medium">Rp {{ number_format((float) $simulation['total_interest']) }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-muted-foreground">{{ __('Total pembayaran') }}</dt>
                            <dd class="font-medium">Rp {{ number_format((float) $simulation['total_payment']) }}</dd>
                        </div>
                    </dl>
                @else
                    <x-ui.empty class="mt-4">
                        <x-ui.empty-header>
                            <x-lucide-calculator class="size-6 text-muted-foreground" />
                            <x-ui.empty-title>{{ __('Belum ada simulasi') }}</x-ui.empty-title>
                            <x-ui.empty-description>{{ __('Isi nominal dan tenor, lalu klik Hitung Simulasi.') }}</x-ui.empty-description>
                        </x-ui.empty-header>
                    </x-ui.empty>
                @endif
            </x-ui.card>

            @if ($this->missingDocuments() !== [])
                <x-ui.alert tone="warning">
                    <x-lucide-circle-alert />
                    <x-ui.alert-description>
                        <p class="font-semibold">{{ __('Dokumen wajib belum lengkap') }}</p>
                        <p class="mt-1">{{ __('Kurang: :docs', ['docs' => implode(', ', $this->missingDocuments())]) }}</p>
                        <x-ui.button href="{{ route('customer.documents') }}" variant="link" size="sm" class="mt-2" wire:navigate>
                            {{ __('Unggah dokumen sekarang') }}
                        </x-ui.button>
                    </x-ui.alert-description>
                </x-ui.alert>
            @else
                <x-ui.alert tone="success">
                    <x-lucide-shield-check />
                    <x-ui.alert-description>
                        <p class="font-semibold">{{ __('Dokumen lengkap') }}</p>
                        <p class="mt-1">{{ __('Semua dokumen wajib sudah tersedia. Anda dapat mengirim pengajuan.') }}</p>
                    </x-ui.alert-description>
                </x-ui.alert>
            @endif
        </div>
    </div>

    @if ($needsPin)
        <div class="fixed inset-0 z-[150] flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
            <x-ui.card class="w-full max-w-sm p-6">
                <div class="flex items-center gap-3">
                    <span class="flex size-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                        <x-lucide-lock class="size-5" />
                    </span>
                    <div>
                        <h3 class="text-lg font-semibold">{{ __('Konfirmasi PIN') }}</h3>
                        <p class="text-sm text-muted-foreground">{{ __('Masukkan PIN untuk mengirim pengajuan.') }}</p>
                    </div>
                </div>

                <form wire:submit="confirmAndSubmit" class="mt-5 space-y-4">
                    <x-ui.field>
                        <x-ui.field-label for="transaction_pin">{{ __('PIN Transaksi') }}</x-ui.field-label>
                        <x-ui.input id="transaction_pin" wire:model="pin" type="password" inputmode="numeric" maxlength="6" required autofocus />
                        <x-ui.field-error :messages="$errors->get('pin')" />
                    </x-ui.field>

                    <div class="flex gap-2">
                        <x-ui.button type="button" variant="ghost" class="flex-1" wire:click="$set('needsPin', false)">
                            {{ __('Batal') }}
                        </x-ui.button>
                        <x-ui.button type="submit" class="flex-1">
                            {{ __('Kirim') }}
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    @endif
</section>
