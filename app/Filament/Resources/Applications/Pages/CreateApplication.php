<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Filament\Resources\Applications\ApplicationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateApplication extends CreateRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function afterSave(): void
    {
        $this->record->update([
            'services' => $this->record->parsed_yaml_services,
        ]);
    }
}
