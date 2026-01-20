<?php

namespace App\Filament\Resources\Incidents\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
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
                Repeater::make('images')
                    ->relationship()
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Zdjęcie')
                            ->image()
                            ->directory(fn ($get) =>
                                'incidents_images/' . $get('../../id')
                            )
                            ->required()
                            ->deletable(true),
                ])
                ->label('Zdjęcia incydentu')
                ->columns(1)
                ->addActionLabel('Dodaj zdjęcie'),
            ]);
    }
}
