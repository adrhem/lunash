<?php

namespace App\Filament\Resources\Applications\Actions;

use App\Models\Application;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class Start
{
    public static function make(): Action
    {
        return Action::make('Start')
            ->label('Start')
            ->icon(Heroicon::PlayCircle)
            ->url(fn(Application $record): string => route('applications.start', ['id' => $record->id]))
            ->postToUrl()
            ->visible(fn($record) => in_array($record->status, ['created', 'exited']));
    }
}
