<?php

namespace App\Filament\Resources\Containers\Pages;

use App\Filament\Resources\Containers\ContainerResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListContainers extends ListRecords
{
    protected static string $resource = ContainerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->button()
                ->label('Refresh Containers')
                ->name('Refresh Containers')
                ->icon(Heroicon::ArrowPath)
                ->url(fn(): string => route('containers.refresh'))
                ->postToUrl(),
        ];
    }
}
