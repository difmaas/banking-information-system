<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

/**
 * Filament page that serves as the entry point for the CIS module.
 */
class CisDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Modules';

    protected static ?string $navigationLabel = 'CIS';

    protected static ?string $title = 'Customer Information System';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.cis-dashboard';
}
