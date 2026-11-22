<?php

namespace App\Filament\Pages;

use App\Models\AuditLog;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class AuditTrail extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.audit-trail';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|\UnitEnum|null $navigationGroup = 'Administrasi';

    protected static ?string $navigationLabel = 'Audit Trail';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('audit.view') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(AuditLog::query()->with('user')->latest('created_at'))
            ->columns([
                TextColumn::make('created_at')->label('Waktu')->dateTime()->sortable(),
                TextColumn::make('user.name')->label('Pengguna')->default('-'),
                TextColumn::make('action')->label('Aksi')->searchable(),
                TextColumn::make('subject_type')->label('Subjek')->searchable(),
                TextColumn::make('subject_id')->label('ID Subjek')->sortable(),
            ]);
    }
}
