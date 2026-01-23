<?php

namespace App\Filament\Resources\Incidents\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use App\Models\Incident;

class IncidentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dane pojazdu')
                    ->schema([
                        TextInput::make('vehicle_number')->required(),
                        TextInput::make('vehicle_vin')->required(),
                        TextInput::make('vehicle_brand')->required(),
                        TextInput::make('vehicle_model')->required(),
                        TextInput::make('vehicle_type')->required(),
                        TextInput::make('description')->required(),
                    ])
                    ->columns(2),
                Section::make('Adres incydentu')
                    ->schema([
                        TextInput::make('address.street')
                            ->label('Ulica'),
                        TextInput::make('address.house_number')
                            ->label('Numer domu')
                            ->nullable(),
                        TextInput::make('address.apartment_number')
                            ->label('Numer mieszkania')
                            ->nullable(),
                        TextInput::make('address.city')
                            ->label('Miasto')
                            ->nullable(),
                        TextInput::make('address.zip')
                            ->label('Kod pocztowy')
                            ->nullable(),
                    ])
                    ->columns(2),
                Section::make('Pozostałe')
                    ->schema([
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
                    ]),
            ]);
    }
}
