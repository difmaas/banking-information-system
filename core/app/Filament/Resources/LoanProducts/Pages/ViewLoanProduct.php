<?php

namespace App\Filament\Resources\LoanProducts\Pages;

use App\Filament\Resources\LoanProducts\LoanProductResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLoanProduct extends ViewRecord
{
    protected static string $resource = LoanProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
