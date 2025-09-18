<?php

namespace App\Filament\Resources\Applications\Tables;

use App\Filament\Resources\Applications\Actions;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;


class ApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Application Name')->searchable()->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'running' => 'success',
                        'stopped' => 'danger',
                        'exited' => 'warning',
                        'created' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('services_count')->label('Services'),
                TextColumn::make('created_at')->label('Created')->dateTime('Y-m-d H:i:s'),
                TextColumn::make('updated_at')->label('Last Updated')->dateTime('Y-m-d H:i:s'),
            ])->recordActions([
                Actions\Start::make(),
                Actions\Stop::make(),
                Actions\Restart::make(),
            ])
            ->defaultGroup('status')
            ->emptyStateHeading('No applications! Click on the refresh button to load them.');
    }
}
