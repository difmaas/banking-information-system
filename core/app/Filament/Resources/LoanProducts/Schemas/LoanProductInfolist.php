<?php

namespace App\Filament\Resources\LoanProducts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LoanProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('code'),
                TextEntry::make('name'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('min_amount')
                    ->numeric(),
                TextEntry::make('max_amount')
                    ->numeric(),
                TextEntry::make('min_tenor_months')
                    ->numeric(),
                TextEntry::make('max_tenor_months')
                    ->numeric(),
                TextEntry::make('interest_rate')
                    ->numeric(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
