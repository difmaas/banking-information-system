<?php

namespace App\Filament\Resources\LoanProducts\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LoanProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('min_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('max_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('min_tenor_months')
                    ->required()
                    ->numeric(),
                TextInput::make('max_tenor_months')
                    ->required()
                    ->numeric(),
                TextInput::make('interest_rate')
                    ->required()
                    ->numeric(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
