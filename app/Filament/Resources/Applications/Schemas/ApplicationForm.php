<?php

namespace App\Filament\Resources\Applications\Schemas;

use App\Rules\ComposeFileRule;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Icon;
use Filament\Support\Icons\Heroicon;

class ApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->columnSpanFull()
                    ->label('Application Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('compose_file')
                    ->columnSpanFull()
                    ->belowLabel([
                        Icon::make(Heroicon::InformationCircle),
                        'Absolute path to the [docker-]compose.yml/yaml.',
                    ])
                    ->label('Compose File Path')
                    ->rules([new ComposeFileRule(), 'required'])
                    ->readOnly(fn(string $operation): bool => $operation === 'edit'),
                Repeater::make('services')
                    ->label('Services')
                    ->columnSpanFull()
                    ->visible(fn(string $operation): bool => $operation === 'edit')
                    ->addable(false)
                    ->deletable(false)
                    ->schema([
                        TextInput::make('name')->readOnly(),
                        TextInput::make('image')->readOnly(),
                        TextInput::make('tag')->readOnly(),
                    ])
                    ->columns(3)
            ]);
    }
}
