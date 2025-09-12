<?php

namespace App\Filament\Resources\Containers\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ContainersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('container_id')
                    ->label('Container ID')
                    ->searchable()
                    ->sortable()
                    ->limit(15, end: '...')
                    ->copyable()
                    ->copyMessage('Container ID copied to clipboard'),
                TextColumn::make('image')->label('Image')->searchable()->sortable(),
                TextColumn::make('name')->label('Name'),
                TextColumn::make('state')->label('State')->badge()
                    ->color(fn(string $state): string => match ($state) {
                        "created" => "gray",
                        "running" => "success",
                        "paused" => "warning",
                        "restarting" => "yellow",
                        "exited" => "danger",
                        "removing" => "purple",
                        "dead" => "black",
                    }),
                TextColumn::make('created_at')->label('Created At')->dateTime(),
            ]);
    }
}
