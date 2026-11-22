<?php

namespace App\Filament\Pages;

use App\Modules\Lns\Models\Loan;
use App\Modules\Lns\Services\LnsService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Payment extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.payment';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static string|\UnitEnum|null $navigationGroup = 'LNS';

    protected static ?string $navigationLabel = 'Pembayaran Angsuran';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('lns.payment') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Loan::query()->where('status', 'Aktif')->with('customer'))
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('customer.name')->label('Nasabah')->searchable(),
                TextColumn::make('amount')->label('Nominal')->money('IDR'),
                TextColumn::make('outstanding')->label('Sisa')->money('IDR'),
                TextColumn::make('status')->badge(),
            ])
            ->recordActions([
                Action::make('pay')
                    ->label('Bayar Angsuran')
                    ->form([
                        TextInput::make('installment_number')
                            ->label('Angsuran Ke')
                            ->numeric()
                            ->required(),
                        TextInput::make('amount')
                            ->label('Nominal')
                            ->numeric()
                            ->required(),
                        TextInput::make('reference')
                            ->label('Referensi')
                            ->required(),
                    ])
                    ->action(function (Loan $record, array $data): void {
                        $installment = $record->installments()
                            ->where('number', (int) $data['installment_number'])
                            ->firstOrFail();

                        app(LnsService::class)->recordPayment($record->id, [
                            'installment_id' => $installment->id,
                            'amount' => $data['amount'],
                            'reference' => $data['reference'],
                        ]);

                        Notification::make()->success()->title('Pembayaran tercatat.')->send();
                    }),
            ]);
    }
}
