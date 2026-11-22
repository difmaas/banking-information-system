<?php

namespace App\Filament\Pages;

use App\Modules\Lns\Models\Loan;
use App\Modules\Lns\Services\LnsService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Approval extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.approval';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHandThumbUp;

    protected static string|\UnitEnum|null $navigationGroup = 'LNS';

    protected static ?string $navigationLabel = 'Persetujuan';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('lns.approve') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Loan::query()->where('status', 'Analisis')->with('customer'))
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('customer.name')->label('Nasabah')->searchable(),
                TextColumn::make('amount')->label('Nominal')->money('IDR'),
                TextColumn::make('analyst')->label('Analis')->getStateUsing(
                    fn (Loan $record): string => $record->analyses()->latest('id')->first()?->analyst?->name ?? '-',
                ),
                TextColumn::make('status')->badge(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Putuskan')
                    ->visible(fn (Loan $record): bool => $record->analyses()->latest('id')->first()?->analyst_id !== auth()->id())
                    ->form([
                        Select::make('decision')
                            ->label('Keputusan')
                            ->options([
                                'Setuju' => 'Setuju',
                                'Tolak' => 'Tolak',
                            ])
                            ->required(),
                    ])
                    ->action(function (Loan $record, array $data): void {
                        $result = app(LnsService::class)->approve($record->id, auth()->id(), $data['decision']);

                        Notification::make()
                            ->success()
                            ->title('Keputusan tersimpan: '.$result['status'])
                            ->send();
                    }),
            ]);
    }
}
