<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('channel_user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('nik')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('occupation'),
                TextInput::make('monthly_income')
                    ->numeric(),
                TextInput::make('collectibility')
                    ->required()
                    ->default('Lancar'),
            ]);
    }
}
