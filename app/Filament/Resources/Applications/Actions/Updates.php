<?php

namespace App\Filament\Resources\Applications\Actions;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class Updates
{
    public static function make(): Action
    {
        return Action::make('Updates')
            ->label('Look for Image Updates')
            ->icon(Heroicon::CloudArrowDown)
            ->outlined()
            ->tooltip('Use a headless browser to check if there are updates available for the application images.')
            ->url(route('applications.updates'))
            ->postToUrl();
    }
}
