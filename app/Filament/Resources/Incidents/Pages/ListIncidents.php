<?php

namespace App\Filament\Resources\Incidents\Pages;

use App\Filament\Resources\Incidents\IncidentResource;
use App\Filament\Resources\Incidents\Modals\Actions\IncidentCreator;
use App\Filament\Resources\Incidents\Modals\Schema\IncidentModalFormSchema;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListIncidents extends ListRecords
{
    protected static string $resource = IncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->newIncidentAction(),
        ];
    }

    public function newIncidentAction(): Action
    {
        return Action::make('NewIncident')
            ->label('Nowe zgłoszenie')
            ->modalHeading('Nowe zdarzenie')
            ->modalSubmitActionLabel('Utwórz')
            ->schema(IncidentModalFormSchema::components(true))
            ->action(function (array $arguments, array $data): void {
                IncidentCreator::createFromFormData($data);
                Notification::make()
                    ->success()
                    ->title(__('filament-panels::resources/pages/create-record.notifications.created.title'))
                    ->send();
                $this->redirect(IncidentResource::getUrl('index'));
            });
    }
}
