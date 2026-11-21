<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

/**
 * Filament page that serves as the entry point for the GLS module.
 */
class GlsDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'Modules';

    protected static ?string $navigationLabel = 'GLS';

    protected static ?string $title = 'General Ledger System';

    protected string $view = 'filament.pages.gls-dashboard';
}
