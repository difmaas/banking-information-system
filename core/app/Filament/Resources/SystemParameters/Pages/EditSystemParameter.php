<?php

namespace App\Filament\Resources\SystemParameters\Pages;

use App\Filament\Resources\SystemParameters\SystemParameterResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSystemParameter extends EditRecord
{
    protected static string $resource = SystemParameterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
