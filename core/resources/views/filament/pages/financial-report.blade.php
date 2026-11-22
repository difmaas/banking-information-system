<x-filament-panels::page>
    @php($trial = $this->trialBalance())
    @php($income = $this->incomeStatement())

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
            <label class="text-xs font-medium text-gray-500">{{ __('Akun') }}</label>
            <select wire:model.live="accountId" class="mt-1 w-full rounded-lg border-gray-300">
                @foreach ($this->accounts() as $account)
                    <option value="{{ $account['id'] }}">{{ $account['name'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border p-4">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Total debit') }}</p>
            <p class="mt-2 text-2xl font-semibold">Rp {{ number_format($trial['total_debit']) }}</p>
        </div>
        <div class="rounded-xl border p-4">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Total kredit') }}</p>
            <p class="mt-2 text-2xl font-semibold">Rp {{ number_format($trial['total_credit']) }}</p>
        </div>
        <div class="rounded-xl border p-4">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Status neraca') }}</p>
            <p class="mt-2 text-lg font-semibold {{ $trial['balanced'] ? 'text-green-600' : 'text-red-600' }}">
                {{ $trial['balanced'] ? __('Seimbang') : __('Tidak seimbang') }}
            </p>
        </div>
        <div class="rounded-xl border p-4">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ __('Laba bersih') }}</p>
            <p class="mt-2 text-2xl font-semibold">Rp {{ number_format($income['net_income']) }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border p-4">
            <h3 class="font-semibold">{{ __('Buku Besar') }}</h3>
            <div class="mt-3 max-h-96 overflow-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase text-gray-500">
                        <tr>
                            <th class="py-2 text-start">{{ __('Tanggal') }}</th>
                            <th class="py-2 text-start">{{ __('Referensi') }}</th>
                            <th class="py-2 text-end">{{ __('Debit') }}</th>
                            <th class="py-2 text-end">{{ __('Kredit') }}</th>
                            <th class="py-2 text-end">{{ __('Saldo') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->ledger() as $row)
                            <tr class="border-t">
                                <td class="py-2">{{ $row['date'] }}</td>
                                <td class="py-2 font-mono text-xs">{{ $row['reference'] }}</td>
                                <td class="py-2 text-end">{{ $row['debit'] > 0 ? number_format($row['debit']) : '-' }}</td>
                                <td class="py-2 text-end">{{ $row['credit'] > 0 ? number_format($row['credit']) : '-' }}</td>
                                <td class="py-2 text-end font-medium">Rp {{ number_format($row['balance']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-6 text-center text-gray-500">{{ __('Tidak ada jurnal untuk akun ini.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border p-4">
            <h3 class="font-semibold">{{ __('Neraca Saldo') }}</h3>
            <div class="mt-3 max-h-96 overflow-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase text-gray-500">
                        <tr>
                            <th class="py-2 text-start">{{ __('Kode') }}</th>
                            <th class="py-2 text-start">{{ __('Akun') }}</th>
                            <th class="py-2 text-end">{{ __('Debit') }}</th>
                            <th class="py-2 text-end">{{ __('Kredit') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trial['rows'] as $row)
                            <tr class="border-t">
                                <td class="py-2 font-mono text-xs">{{ $row['code'] }}</td>
                                <td class="py-2">{{ $row['name'] }}</td>
                                <td class="py-2 text-end">{{ $row['debit'] > 0 ? number_format($row['debit']) : '-' }}</td>
                                <td class="py-2 text-end">{{ $row['credit'] > 0 ? number_format($row['credit']) : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t font-semibold">
                        <tr>
                            <td colspan="2" class="py-2">{{ __('Total') }}</td>
                            <td class="py-2 text-end">{{ number_format($trial['total_debit']) }}</td>
                            <td class="py-2 text-end">{{ number_format($trial['total_credit']) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-xl border p-4">
        <h3 class="font-semibold">{{ __('Ringkasan Keuangan') }}</h3>
        <div class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <p class="text-xs text-gray-500">{{ __('Pendapatan bunga') }}</p>
                <p class="font-semibold">Rp {{ number_format($income['revenue']) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ __('Beban') }}</p>
                <p class="font-semibold">Rp {{ number_format($income['expense']) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ __('Kas') }}</p>
                <p class="font-semibold">Rp {{ number_format($income['cash']) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">{{ __('Piutang kredit') }}</p>
                <p class="font-semibold">Rp {{ number_format($income['receivable']) }}</p>
            </div>
        </div>
    </div>
</x-filament-panels::page>
