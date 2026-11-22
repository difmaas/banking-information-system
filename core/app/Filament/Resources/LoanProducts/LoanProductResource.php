<?php

namespace App\Filament\Resources\LoanProducts;

use App\Filament\Resources\LoanProducts\Pages\CreateLoanProduct;
use App\Filament\Resources\LoanProducts\Pages\EditLoanProduct;
use App\Filament\Resources\LoanProducts\Pages\ListLoanProducts;
use App\Filament\Resources\LoanProducts\Pages\ViewLoanProduct;
use App\Filament\Resources\LoanProducts\Schemas\LoanProductForm;
use App\Filament\Resources\LoanProducts\Schemas\LoanProductInfolist;
use App\Filament\Resources\LoanProducts\Tables\LoanProductsTable;
use App\Modules\Lns\Models\LoanProduct;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class LoanProductResource extends Resource
{
    protected static ?string $model = LoanProduct::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'LNS';

    protected static ?string $navigationLabel = 'Produk Kredit';

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('lns.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('lns.manage') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->can('lns.manage') ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return LoanProductForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LoanProductInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LoanProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoanProducts::route('/'),
            'create' => CreateLoanProduct::route('/create'),
            'view' => ViewLoanProduct::route('/{record}'),
            'edit' => EditLoanProduct::route('/{record}/edit'),
        ];
    }
}
