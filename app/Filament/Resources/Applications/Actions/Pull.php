<?php

namespace App\Filament\Resources\Applications\Actions;

use App\Models\Application;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class Pull
{
    public static function make(): Action
    {
        return
            Action::make('Pull')
            ->label('Pull')
            ->icon(Heroicon::ArrowDownCircle)
            ->url(fn(Application $record): string => route('applications.pull', ['id' => $record->id]))
            ->postToUrl()
            ->visible(fn($record) => in_array($record->status, ['running', 'stopped', 'exited', 'created']));
    }
}
