<?php

namespace App\Filament\Resources\Applications\Actions;

use App\Models\Application;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class Stop
{
    public static function make(): Action
    {
        return
            Action::make('Stop')
            ->label('Stop')
            ->icon(Heroicon::StopCircle)
            ->url(fn(Application $record): string => route('applications.stop', ['id' => $record->id]))
            ->postToUrl()
            ->visible(fn($record) => in_array($record->status, ['running']));
    }
}
