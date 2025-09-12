<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;
use Illuminate\Console\Command;

class ContainerController extends Controller
{

    public function refresh()
    {
        $result = Artisan::call('docker ps');

        if ($result !== Command::SUCCESS) {
            Notification::make()
                ->title('Failed to refresh containers. Please check the logs for more details.')
                ->danger()
                ->send();
            return redirect()->back();
        }

        Notification::make()
            ->title('Containers refreshed successfully.')
            ->success()
            ->send();

        return redirect()->back();
    }
}
