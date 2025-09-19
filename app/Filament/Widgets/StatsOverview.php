<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{

    protected ?string $heading = 'Applications Overview';

    protected function getStats(): array
    {
        $stats = [];
        foreach (Application::all()->select(['name', 'status', 'services_count']) as $application) {
            $stats[] = Stat::make(
                value: $application['name'],
                label: "Application ({$application['services_count']} services)",
            )->description($application['status'])
                ->descriptionIcon($application['status'] === 'running' ? Heroicon::CheckCircle : Heroicon::XCircle)
                ->color($application['status'] === 'running' ? 'success' : 'gray')
                ->columnSpan([
                    'default' => 3,
                    'xl' => 1,
                ])
                ->chart([7, 2, 10, 3, 15, 4, 17]);
        }

        return $stats;
    }
}
