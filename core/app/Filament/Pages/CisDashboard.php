<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CisDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Modules';

    protected static ?string $navigationLabel = 'CIS';

    protected static ?string $title = 'Customer Information System';

    protected string $view = 'filament.pages.cis-dashboard';
}