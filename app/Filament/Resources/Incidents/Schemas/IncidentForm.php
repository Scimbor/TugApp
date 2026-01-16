<?php

namespace App\Filament\Resources\Incidents\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Models\Incident;

class IncidentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('vehicle_number')->required(),
                TextInput::make('vehicle_vin')->required(),
                TextInput::make('vehicle_brand')->required(),
                TextInput::make('vehicle_model')->required(),
                TextInput::make('vehicle_type')->required(),
                TextInput::make('description')->required(),
                Select::make('status')->options(Incident::STATUS_OPTIONS),
            ]);
    }
}
