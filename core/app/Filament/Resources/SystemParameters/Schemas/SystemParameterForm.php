<?php

namespace App\Filament\Resources\SystemParameters\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class SystemParameterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn (?Model $record): bool => $record !== null),
                TextInput::make('value')
                    ->required()
                    ->maxLength(500),
                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }
}
