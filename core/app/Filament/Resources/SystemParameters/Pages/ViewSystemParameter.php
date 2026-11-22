<?php

namespace App\Filament\Resources\SystemParameters\Pages;

use App\Filament\Resources\SystemParameters\SystemParameterResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSystemParameter extends ViewRecord
{
    protected static string $resource = SystemParameterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
