<?php

namespace App\Http\Controllers;

use App\Filament\Resources\Applications\Actions;
use App\Jobs\RefreshApplications;
use App\Models\Application;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;

class ApplicationController extends Controller
{

    public function refresh()
    {
        $status = Artisan::call('docker refresh');

        if ($status === Command::SUCCESS) {
            Notification::make()
                ->title('Docker containers refreshed successfully.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to refresh Docker containers. Please check the logs for details.')
                ->body(Artisan::output())
                ->danger()
                ->send();
        }

        return redirect()->back();
    }

    public function start(string $id)
    {
        $application = Application::findOrFail($id);

        $status = Artisan::call('docker start ' . $application->name);

        if ($status === Command::SUCCESS) {
            Notification::make()
                ->title('Application started successfully.')
                ->success()
                ->send();
        } else {
            dd(Artisan::output());
            Notification::make()
                ->title('Failed to start the application. Please check the logs for details.')
                ->body(Artisan::output())
                ->danger()
                ->actions([
                    Actions\Logs::make($application->id),
                ])
                ->send();

            RefreshApplications::dispatch();
        }

        return redirect()->back();
    }

    public function stop(string $id)
    {
        $application = Application::findOrFail($id);
        $status = Artisan::call('docker stop ' . $application->name);

        if ($status === Command::SUCCESS) {
            Notification::make()
                ->title('Application stopped successfully.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to stop the application. Please check the logs for details.')
                ->body(Artisan::output())
                ->danger()
                ->actions([
                    Actions\Logs::make($application->id),
                ])
                ->send();

            RefreshApplications::dispatch();
        }

        return redirect()->back();
    }

    public function restart(string $id)
    {
        $application = Application::findOrFail($id);
        $status = Artisan::call('docker restart ' . $application->name);

        if ($status === Command::SUCCESS) {
            Notification::make()
                ->title('Application restarted successfully.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to restart the application. Please check the logs for details.')
                ->body(Artisan::output())
                ->danger()
                ->actions([
                    Actions\Logs::make($application->id),
                ])
                ->send();

            RefreshApplications::dispatch();
        }

        return redirect()->back();
    }

    public function logs(string $id)
    {
        $application = Application::findOrFail($id);
        $status = Artisan::call('docker logs ' . $application->name);
        if ($status === Command::SUCCESS) {
            $logs = Artisan::output();

            return response($logs, 200)
                ->header('Content-Type', 'text/plain');
        } else {
            Notification::make()
                ->title('Failed to retrieve application logs. Please check docker is running and the application exists.')
                ->body(Artisan::output())
                ->danger()
                ->send();
            return redirect()->back();
        }
    }
}
