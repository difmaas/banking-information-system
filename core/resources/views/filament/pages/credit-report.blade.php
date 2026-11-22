<x-filament-panels::page>
    @php($totals = $this->totals())

    <div class="mb-6 flex flex-col gap-4 rounded-xl border p-4 md:flex-row md:items-end">
        <div class="flex-1">
            <label class="text-xs font-medium text-gray-500">{{ __('Dari') }}</label>
            <input type="date" wire:model.live="from" class="mt-1 w-full rounded-lg border-gray-300" />
        </div>
        <div class="flex-1">
            <label class="text-xs font-medium text-gray-500">{{ __('Sampai') }}</label>
            <input type="date" wire:model.live="to" class="mt-1 w-full rounded-lg border-gray-300" />
        </div>
        <div class="flex-1">
            <label class="text-xs font-medium text-gray-500">{{ __('Status') }}</label>
            <select wire:model.live="status" class="mt-1 w-full rounded-lg border-gray-300">
                <option value="">{{ __('Semua status') }}</option>
                @foreach (['Diajukan', 'Verifikasi', 'Analisis', 'Disetujui', 'Aktif', 'Lunas', 'Ditolak'] as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-xl border p-4">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Total pengajuan') }}</p>
            <p class="mt-2 text-2xl font-semibold">{{ $totals['applications'] }}</p>
        </div>
        <div class="rounded-xl border p-4">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Pinjaman aktif') }}</p>
            <p class="mt-2 text-2xl font-semibold">{{ $totals['active'] }}</p>
        </div>
        <div class="rounded-xl border p-4">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Total plafond') }}</p>
            <p class="mt-2 text-2xl font-semibold">Rp {{ number_format($totals['plafond']) }}</p>
        </div>
        <div class="rounded-xl border p-4">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Total outstanding') }}</p>
            <p class="mt-2 text-2xl font-semibold">Rp {{ number_format($totals['outstanding']) }}</p>
        </div>
        <div class="rounded-xl border p-4">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Angsuran masuk') }}</p>
            <p class="mt-2 text-2xl font-semibold">Rp {{ number_format($totals['received']) }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border p-4">
            <h3 class="font-semibold">{{ __('Pengajuan per Status') }}</h3>
            <table class="mt-3 w-full text-sm">
                <thead class="text-xs uppercase text-gray-500">
                    <tr>
                        <th class="py-2 text-start">{{ __('Status') }}</th>
                        <th class="py-2 text-end">{{ __('Jumlah') }}</th>
                        <th class="py-2 text-end">{{ __('Nominal') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->applications() as $row)
                        <tr class="border-t">
                            <td class="py-2">{{ $row->status }}</td>
                            <td class="py-2 text-end">{{ $row->total }}</td>
                            <td class="py-2 text-end">Rp {{ number_format((float) $row->nominal) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="py-6 text-center text-gray-500">{{ __('Tidak ada data.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="rounded-xl border p-4">
            <h3 class="font-semibold">{{ __('Portofolio Kredit') }}</h3>
            <div class="mt-3 max-h-96 overflow-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase text-gray-500">
                        <tr>
                            <th class="py-2 text-start">{{ __('Nasabah') }}</th>
                            <th class="py-2 text-start">{{ __('Produk') }}</th>
                            <th class="py-2 text-end">{{ __('Nominal') }}</th>
                            <th class="py-2 text-end">{{ __('Sisa') }}</th>
                            <th class="py-2 text-start">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->portfolio() as $row)
                            <tr class="border-t">
                                <td class="py-2">{{ $row['customer'] }}</td>
                                <td class="py-2">{{ $row['product'] }}</td>
                                <td class="py-2 text-end">Rp {{ number_format($row['amount']) }}</td>
                                <td class="py-2 text-end">Rp {{ number_format($row['outstanding']) }}</td>
                                <td class="py-2">{{ $row['status'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-gray-500">{{ __('Tidak ada data.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-xl border p-4">
        <h3 class="font-semibold">{{ __('Angsuran Masuk') }}</h3>
        <div class="mt-3 max-h-96 overflow-auto">
            <table class="w-full text-sm">
                <thead class="text-xs uppercase text-gray-500">
                    <tr>
                        <th class="py-2 text-start">{{ __('Referensi') }}</th>
                        <th class="py-2 text-start">{{ __('Nasabah') }}</th>
                        <th class="py-2 text-start">{{ __('Angsuran') }}</th>
                        <th class="py-2 text-end">{{ __('Nominal') }}</th>
                        <th class="py-2 text-start">{{ __('Tanggal') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->installments() as $row)
                        <tr class="border-t">
                            <td class="py-2 font-mono text-xs">{{ $row['reference'] }}</td>
                            <td class="py-2">{{ $row['customer'] }}</td>
                            <td class="py-2">{{ $row['installment'] }}</td>
                            <td class="py-2 text-end">Rp {{ number_format($row['amount']) }}</td>
                            <td class="py-2">{{ $row['paid_at'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-gray-500">{{ __('Tidak ada data.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
