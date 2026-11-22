<?php

namespace App\Filament\Resources\SystemParameters;

use App\Filament\Resources\SystemParameters\Pages\EditSystemParameter;
use App\Filament\Resources\SystemParameters\Pages\ListSystemParameters;
use App\Filament\Resources\SystemParameters\Schemas\SystemParameterForm;
use App\Filament\Resources\SystemParameters\Schemas\SystemParameterInfolist;
use App\Filament\Resources\SystemParameters\Tables\SystemParametersTable;
use App\Models\SystemParameter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SystemParameterResource extends Resource
{
    protected static ?string $model = SystemParameter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;

    protected static string|\UnitEnum|null $navigationGroup = 'Administrasi';

    protected static ?string $navigationLabel = 'Parameter Sistem';

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('parameters.manage') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('parameters.manage') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return SystemParameterForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SystemParameterInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SystemParametersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSystemParameters::route('/'),
            'edit' => EditSystemParameter::route('/{record}/edit'),
        ];
    }
}
