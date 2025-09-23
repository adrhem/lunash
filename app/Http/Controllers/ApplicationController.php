<?php

namespace App\Http\Controllers;

use App\Filament\Resources\Applications\Actions;
use App\Jobs\RefreshApplications;
use App\Models\Application;
use App\Models\Service;
use Exception;
use Filament\Notifications\Notification;
use HeadlessChromium\Browser\ProcessAwareBrowser;
use HeadlessChromium\Exception\BrowserConnectionFailed;
use HeadlessChromium\BrowserFactory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;
use RuntimeException;

class ApplicationController extends Controller
{

    public function updates()
    {
        $applications = Application::all();

        if ($applications->isEmpty()) {
            Notification::make()
                ->title('No applications found to check for updates.')
                ->warning()
                ->send();

            return redirect()->back();
        }

        try {
            $browserFactory = new BrowserFactory();
            $browser = $browserFactory->createBrowser();
            $updates = [];
            foreach ($applications as $application) {
                $hasUpdates = false;
                foreach ($application->services as $service) {

                    if (empty($service->repository_url) || empty($service->repository)) {
                        continue;
                    }

                    $hasUpdates = $this->checkForUpdates($browser, $service);

                    if ($hasUpdates) {
                        $updates[] = $application->name;
                        break; // No need to check other services if one has updates
                    }
                }
                if ($hasUpdates) {
                    $application->update(['has_updates' => true]);
                }
            }
        } catch (RuntimeException $e) {
            Notification::make()
                ->title('Failed to launch headless browser. Please ensure that Chrome or Chromium is installed on the server.')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } catch (BrowserConnectionFailed $e) {
            Notification::make()
                ->title('Failed to connect to the headless browser. Please ensure that Chrome or Chromium is installed on the server.')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } catch (Exception $e) {
            Notification::make()
                ->title('Error occurred while checking for updates.')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            if (isset($browser)) {
                $browser->close();
            }
        }

        return redirect()->back();
    }

    private function checkForUpdates(ProcessAwareBrowser $browser, Service $service): bool
    {
        $page = $browser->createPage();
        $page->navigate($service->repository_url)->waitForNavigation();

        $script = $page->evaluate('document.querySelector("[data-testid=\'layerDetailHeader-digest\']").innerText');
        $sha256 = $script?->getReturnValue() ?? null;

        $page->close();

        return !empty($sha256) && $sha256 !== $service->image_id;
    }

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

    public function pull(string $id)
    {
        $application = Application::findOrFail($id);
        $status = Artisan::call('docker pull ' . $application->name);

        if ($status === Command::SUCCESS) {
            Notification::make()
                ->title('Application images pulled successfully.')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to pull application images. Please check the logs for details.')
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

    public function pullAndUp(string $id)
    {
        $application = Application::findOrFail($id);
        $status = Artisan::call('docker pull-and-up ' . $application->name);

        if ($status === Command::SUCCESS) {
            Notification::make()
                ->title('Application pulled and started successfully.')
                ->success()
                ->send();
            $application->update(['has_updates' => false]);
            Artisan::call('docker', ['action' => 'images', 'name' => $application->name]);
        } else {
            Notification::make()
                ->title('Failed to pull and start the application. Please check the logs for details.')
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
}
