<?php

namespace App\Filament\Pages;

use App\Modules\Lns\Models\Loan;
use App\Modules\Lns\Services\LnsService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class VerifyLoan extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.verify-loan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'LNS';

    protected static ?string $navigationLabel = 'Verifikasi Awal';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('lns.verify') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Loan::query()->where('status', 'Diajukan')->with('customer'))
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('customer.name')->label('Nasabah')->searchable(),
                TextColumn::make('amount')->label('Nominal')->money('IDR'),
                TextColumn::make('tenor_months')->label('Tenor'),
                TextColumn::make('status')->badge(),
            ])
            ->recordActions([
                Action::make('verify')
                    ->label('Periksa Berkas')
                    ->form([
                        Select::make('decision')
                            ->label('Keputusan')
                            ->options([
                                'Diteruskan' => 'Diteruskan ke Analis',
                                'Tolak' => 'Tolak',
                            ])
                            ->required(),
                        Textarea::make('reason')
                            ->label('Alasan')
                            ->rows(3),
                    ])
                    ->action(function (Loan $record, array $data): void {
                        app(LnsService::class)->verify(
                            $record->id,
                            auth()->id(),
                            $data['decision'],
                            $data['reason'] ?? null,
                        );

                        Notification::make()->success()->title('Verifikasi tersimpan.')->send();
                    }),
            ]);
    }
}
