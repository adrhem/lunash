<?php

namespace App\Filament\Resources\Containers;

use App\Filament\Resources\Containers\Pages\RefreshContainer;
use App\Filament\Resources\Containers\Pages\ListContainers;
use App\Filament\Resources\Containers\Schemas\ContainerForm;
use App\Filament\Resources\Containers\Tables\ContainersTable;
use App\Models\Container;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ContainerResource extends Resource
{
    protected static ?string $model = Container::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServer;

    protected static ?string $recordTitleAttribute = 'Container';

    public static function form(Schema $schema): Schema
    {
        return ContainerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContainersTable::configure($table);
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
            'index' => ListContainers::route('/'),
            'refresh' => RefreshContainer::route('/refresh'),
        ];
    }
}
