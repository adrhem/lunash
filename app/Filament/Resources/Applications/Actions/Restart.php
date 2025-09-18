<?php

namespace App\Filament\Resources\Applications\Actions;

use App\Models\Application;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class Restart
{
    public static function make(): Action
    {
        return Action::make('Restart')
            ->label('Restart')
            ->icon(Heroicon::ArrowPathRoundedSquare)
            ->url(fn(Application $record): string => route('applications.restart', ['id' => $record->id]))
            ->postToUrl()
            ->visible(fn($record) => in_array($record->status, ['running']));
    }
}
