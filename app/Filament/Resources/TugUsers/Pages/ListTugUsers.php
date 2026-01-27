<?php

namespace App\Filament\Resources\TugUsers\Pages;

use App\Filament\Resources\TugUsers\TugUsersResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTugUsers extends ListRecords
{
    protected static string $resource = TugUsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
