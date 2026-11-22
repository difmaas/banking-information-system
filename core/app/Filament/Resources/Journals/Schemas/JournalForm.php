<?php

namespace App\Filament\Resources\Journals\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class JournalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->required(),
                TextInput::make('reference')
                    ->required(),
                TextInput::make('description')
                    ->required(),
                TextInput::make('source_type'),
                TextInput::make('source_id')
                    ->numeric(),
            ]);
    }
}
