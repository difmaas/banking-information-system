<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

/**
 * Filament page that serves as the entry point for the LNS module.
 */
class LnsDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Modules';

    protected static ?string $navigationLabel = 'LNS';

    protected static ?string $title = 'Loan Management System';

    protected string $view = 'filament.pages.lns-dashboard';
}
