<?php

namespace App\Filament\Resources\ActivityLogs\Pages;

use App\Filament\Resources\ActivityLogs\ActivityLogsResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewActivityLogs extends ViewRecord
{
    protected static string $resource = ActivityLogsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
