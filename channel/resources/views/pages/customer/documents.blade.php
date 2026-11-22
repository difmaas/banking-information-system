<?php

use App\Contracts\CoreGateway;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Dokumen')] class extends Component
{
    use WithFileUploads;

    public array $documents = [];

    public string $type = 'ktp';

    public $file;

    public function mount(): void
    {
        $this->documents = app(CoreGateway::class)->getCustomerDocuments(auth()->id());
    }

    public function storeDocument(): void
    {
        $validated = $this->validate([
            'type' => ['required', 'string', 'max:50'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $file = $this->file;
        $path = $file->store('documents/'.auth()->id(), 'local');

        $this->documents[] = app(CoreGateway::class)->storeCustomerDocument(auth()->id(), [
            'type' => $validated['type'],
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
        ]);

        $this->reset('file');

        $this->dispatch('toast', type: 'success', description: 'Dokumen berhasil diunggah.');
    }
}; ?>

<section class="space-y-6">
    <div>
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-muted-foreground">{{ __('Akun') }}</p>
        <h2 class="mt-1 text-2xl font-semibold tracking-tight">{{ __('Dokumen Saya') }}</h2>
        <p class="mt-1 text-sm text-muted-foreground">{{ __('Unggah dan kelola dokumen persyaratan kredit Anda.') }}</p>
    </div>

    <div class="grid gap-5 lg:grid-cols-5">
        <x-ui.card class="p-5 sm:p-6 lg:col-span-2">
            <h3 class="text-base font-semibold">{{ __('Unggah Dokumen') }}</h3>

            <form wire:submit="storeDocument" class="mt-5 space-y-4">
                <x-ui.field>
                    <x-ui.field-label for="type">{{ __('Jenis Dokumen') }}</x-ui.field-label>
                    <x-ui.select native id="type" wire:model="type">
                        <option value="ktp">KTP</option>
                        <option value="kk">Kartu Keluarga</option>
                        <option value="slip_gaji">Slip Gaji</option>
                        <option value="lainnya">Lainnya</option>
                    </x-ui.select>
                    <x-ui.field-error :messages="$errors->get('type')" />
                </x-ui.field>

                <x-ui.field>
                    <x-ui.field-label for="file">{{ __('Berkas') }}</x-ui.field-label>
                    <x-ui.input id="file" wire:model="file" type="file" accept=".pdf,.jpg,.jpeg,.png" />
                    <x-ui.field-description>{{ __('Format PDF, JPG, atau PNG. Maksimal 2 MB.') }}</x-ui.field-description>
                    <x-ui.field-error :messages="$errors->get('file')" />
                </x-ui.field>

                <x-ui.button type="submit" class="w-full">
                    {{ __('Unggah Dokumen') }}
                </x-ui.button>
            </form>
        </x-ui.card>

        <x-ui.card class="p-5 sm:p-6 lg:col-span-3">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold">{{ __('Dokumen Tersimpan') }}</h3>
                    <p class="mt-1 text-sm text-muted-foreground">{{ __(':count dokumen terdaftar', ['count' => count($documents)]) }}</p>
                </div>
                <x-ui.badge variant="secondary" size="sm">{{ count($documents) }}</x-ui.badge>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($documents as $document)
                    <div class="flex items-center gap-3 rounded-lg border p-4">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"><x-lucide-file-text class="size-5" /></span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold">{{ $document['name'] }}</p>
                            <p class="mt-0.5 text-xs text-muted-foreground">
                                {{ strtoupper((string) $document['type']) }} · {{ number_format((float) $document['size'] / 1024, 1) }} KB · {{ $document['uploaded_at'] }}
                            </p>
                        </div>
                        <x-ui.badge tone="neutral" size="sm">{{ __('Tersimpan') }}</x-ui.badge>
                    </div>
                @empty
                    <x-ui.empty>
                        <x-ui.empty-header>
                            <x-lucide-folder class="size-6 text-muted-foreground" />
                            <x-ui.empty-title>{{ __('Belum ada dokumen') }}</x-ui.empty-title>
                            <x-ui.empty-description>{{ __('Unggah dokumen pertama Anda melalui formulir di samping.') }}</x-ui.empty-description>
                        </x-ui.empty-header>
                    </x-ui.empty>
                @endforelse
            </div>
        </x-ui.card>
    </div>
</section>
