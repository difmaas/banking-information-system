<?php

namespace App\Filament\Resources\Loans\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LoanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->required(),
                TextInput::make('loan_product_id')
                    ->required()
                    ->numeric(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                TextInput::make('tenor_months')
                    ->required()
                    ->numeric(),
                TextInput::make('interest_rate')
                    ->required()
                    ->numeric(),
                TextInput::make('status')
                    ->required()
                    ->default('Diajukan'),
                TextInput::make('outstanding')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('applied_at'),
                DateTimePicker::make('approved_at'),
                DateTimePicker::make('disbursed_at'),
            ]);
    }
}
