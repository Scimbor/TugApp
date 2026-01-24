<?php

namespace App\Filament\Resources\Incidents\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use App\Models\Incident;
use App\Models\IncidentDepositFee;

class IncidentForm
{
    public static function configure(Schema $schema): Schema
    {
        $isClosed = fn ($get) => in_array($get('status') ?? null, Incident::CLOSED_MODIFICATION_ROW_STATUSES);
        $isCompleted = fn ($get) => ($get('status') ?? null) === Incident::STATUS_COMPLETED;
        $isFeeLocked = fn ($get) => in_array($get('depositFee.status') ?? null, IncidentDepositFee::CLOSED_MODIFICATION_ROW_STATUSES);

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
                    ->columns(2)
                    ->disabled($isClosed),
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
                    ->columns(2)
                    ->disabled($isClosed),
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
                    ])
                    ->disabled($isClosed),
                Section::make('Opłata za depozyt')
                    ->schema([
                        TextInput::make('depositFee.fee')
                            ->label('Kwota')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->required(),
                        TextInput::make('depositFee.description')
                            ->label('Opis')
                            ->nullable(),
                        Select::make('depositFee.status')
                            ->label('Status opłaty')
                            ->options(IncidentDepositFee::PAYMENT_STATUS_OPTIONS)
                            ->nullable(),
                        TextInput::make('depositFee.payment_method')
                            ->label('Sposób płatności')
                            ->nullable(),
                    ])
                    ->columns(2)
                    ->visible($isCompleted)
                    ->disabled($isFeeLocked),
            ]);
    }
}
