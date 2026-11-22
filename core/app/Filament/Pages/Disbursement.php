<?php

namespace App\Filament\Pages;

use App\Modules\Lns\Models\Loan;
use App\Modules\Lns\Services\LnsService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Disbursement extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.disbursement';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup = 'LNS';

    protected static ?string $navigationLabel = 'Akad & Pencairan';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('lns.disburse') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Loan::query()->where('status', 'Disetujui')->with(['customer', 'akad']))
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('customer.name')->label('Nasabah')->searchable(),
                TextColumn::make('amount')->label('Nominal')->money('IDR'),
                TextColumn::make('tenor_months')->label('Tenor'),
                TextColumn::make('akad')
                    ->label('Akad')
                    ->formatStateUsing(fn (Loan $record): string => $record->akad === null ? 'Belum dicatat' : 'Sudah ditandatangani')
                    ->badge()
                    ->color(fn (Loan $record): string => $record->akad === null ? 'warning' : 'success'),
                TextColumn::make('status')->badge(),
            ])
            ->recordActions([
                Action::make('akad')
                    ->label('Catat Akad')
                    ->visible(fn (Loan $record): bool => $record->akad === null)
                    ->form([
                        TextInput::make('signer_name')
                            ->label('Nama Penandatangan')
                            ->required()
                            ->maxLength(255),
                        DateTimePicker::make('signed_at')
                            ->label('Waktu Tanda Tangan')
                            ->required()
                            ->default(now()),
                        Textarea::make('note')
                            ->label('Catatan')
                            ->rows(2),
                    ])
                    ->action(function (Loan $record, array $data): void {
                        app(LnsService::class)->recordAkad($record->id, $data);

                        Notification::make()->success()->title('Akad tercatat.')->send();
                    }),
                Action::make('disburse')
                    ->label('Cairkan')
                    ->visible(fn (Loan $record): bool => $record->akad !== null)
                    ->requiresConfirmation()
                    ->action(function (Loan $record): void {
                        app(LnsService::class)->disburse($record->id);

                        Notification::make()->success()->title('Kredit dicairkan.')->send();
                    }),
            ]);
    }
}
