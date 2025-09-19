<?php

namespace App\Filament\Resources\Applications\Actions;

use App\Models\Application;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class PullAndUp
{
    public static function make(): Action
    {
        return
            Action::make('PullAndUp')
            ->label('Pull and Up')
            ->icon(Heroicon::ArrowUpCircle)
            ->url(fn(Application $record): string => route('applications.pull-and-up', ['id' => $record->id]))
            ->postToUrl()
            ->visible(fn($record) => in_array($record->status, ['running', 'stopped', 'exited', 'created']));
    }
}
