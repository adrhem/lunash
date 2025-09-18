<?php

namespace App\Filament\Resources\Applications\Actions;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class Refresh
{
    public static function make(): Action
    {
        return Action::make('Refresh')
            ->label('Refresh Applications')
            ->icon(Heroicon::ArrowPathRoundedSquare)
            ->url(route('applications.refresh'))
            ->postToUrl();
    }
}
