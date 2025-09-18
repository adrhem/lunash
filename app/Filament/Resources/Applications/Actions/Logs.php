<?php

namespace App\Filament\Resources\Applications\Actions;

use Filament\Actions\Action;

class Logs
{
    public static function make(string $id): Action
    {
        return Action::make('view')
            ->button()
            ->url(route('applications.logs', $id), shouldOpenInNewTab: true);
    }
}
