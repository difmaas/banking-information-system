<?php

namespace App\Filament\Pages;

use App\Modules\Lns\Models\Loan;
use App\Modules\Lns\Services\LnsService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class AnalyzeLoan extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.analyze-loan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static string|\UnitEnum|null $navigationGroup = 'LNS';

    protected static ?string $navigationLabel = 'Analisis Kelayakan';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('lns.analyze') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Loan::query()->where('status', 'Verifikasi')->with('customer'))
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('customer.name')->label('Nasabah')->searchable(),
                TextColumn::make('amount')->label('Nominal')->money('IDR'),
                TextColumn::make('criteria')
                    ->label('4 Kriteria')
                    ->formatStateUsing(fn (Loan $record): string => $this->criteriaLabel($record)),
            ])
            ->recordActions([
                Action::make('analyze')
                    ->label('Input Analisis')
                    ->form([
                        Textarea::make('survey_result')
                            ->label('Hasil Survei')
                            ->required()
                            ->rows(3),
                        Textarea::make('collateral')
                            ->label('Data Jaminan')
                            ->rows(3),
                    ])
                    ->action(function (Loan $record, array $data): void {
                        $result = app(LnsService::class)->analyze($record->id, auth()->id(), $data);

                        Notification::make()
                            ->success()
                            ->title('Analisis tersimpan: '.$result['recommendation'])
                            ->send();
                    }),
            ]);
    }

    private function criteriaLabel(Loan $record): string
    {
        $criteria = app(LnsService::class)->analysisCriteria($record->id);

        $items = [
            'Kolektibilitas' => $criteria['collectibility_ok'],
            'Pekerjaan' => $criteria['employment_ok'],
            'Penghasilan' => $criteria['income_ok'],
            'Riwayat Kredit' => $criteria['credit_history_ok'],
        ];

        return collect($items)
            ->map(fn (bool $ok, string $label): string => $label.': '.($ok ? 'Sesuai' : 'Tidak'))
            ->join(' | ');
    }
}
