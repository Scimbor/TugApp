<?php

namespace App\Filament\Resources\Incidents\Modals\Schema;

use App\Models\Incident;
use App\Models\IncidentDepositFee;
use App\Models\IncidentImage;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class IncidentModalFormSchema
{
    /**
     * Returns incident form components for use in the modal (create/edit).
     *
     * @param  bool  $forCreate  true = new incident (default status, images=[]), false = edit
     * @return array<int, Section>
     */
    public static function components(bool $forCreate): array
    {
        $isClosed = fn ($get) => in_array($get('status') ?? null, Incident::CLOSED_MODIFICATION_ROW_STATUSES);
        $isCompleted = fn ($get) => ($get('status') ?? null) === Incident::STATUS_COMPLETED;
        $isFeeLocked = fn ($get) => in_array($get('depositFee.status') ?? null, IncidentDepositFee::CLOSED_MODIFICATION_ROW_STATUSES);

        $statusSelect = Select::make('status')
            ->options(Incident::STATUS_OPTIONS)
            ->label('Status');

        $imagesRepeater = Repeater::make('images')
            ->schema([
                FileUpload::make('image_path')
                    ->label('Zdjęcie')
                    ->image()
                    ->directory(fn ($get) => IncidentImage::IMAGES_DIRECTORY . '/' . ($get('../../id') ?? 'temp'))
                    ->required()
                    ->deletable(true),
            ])
            ->label('Zdjęcia incydentu')
            ->columns(1)
            ->addActionLabel('Dodaj zdjęcie')
            ->disabled($isClosed);

        if ($forCreate) {
            $statusSelect = $statusSelect->default(Incident::STATUS_OPEN);
            $imagesRepeater = $imagesRepeater->default([]);
        }

        return [
            Section::make('Dane pojazdu')
                ->schema([
                    TextInput::make('vehicle_number')->required()->label('Numer rejestracyjny'),
                    TextInput::make('vehicle_vin')->required()->label('VIN'),
                    TextInput::make('vehicle_brand')->required()->label('Marka'),
                    TextInput::make('vehicle_model')->required()->label('Model'),
                    TextInput::make('vehicle_type')->required()->label('Typ'),
                    TextInput::make('description')->required()->label('Opis'),
                ])
                ->columns(2)
                ->disabled($isClosed),
            Section::make('Adres incydentu')
                ->schema([
                    TextInput::make('address.street')
                        ->label('Ulica')
                        ->required()
                        ->validationAttribute('ulica')
                        ->validationMessages([
                            'required' => 'Adres jest wymagany. Proszę podać ulicę.',
                        ]),
                    TextInput::make('address.house_number')->label('Numer domu')->nullable(),
                    TextInput::make('address.apartment_number')->label('Numer mieszkania')->nullable(),
                    TextInput::make('address.city')->label('Miasto')->nullable(),
                    TextInput::make('address.zip')->label('Kod pocztowy')->nullable(),
                ])
                ->columns(2)
                ->disabled($isClosed),
            Section::make('Pozostałe')
                ->schema([
                    $statusSelect,
                    $imagesRepeater,
                ]),
            Section::make('Opłata za depozyt')
                ->schema([
                    TextInput::make('depositFee.fee')->label('Kwota')->numeric()->minValue(0)->step(0.01)->nullable(),
                    TextInput::make('depositFee.description')->label('Opis')->nullable(),
                    Select::make('depositFee.status')->label('Status opłaty')->options(IncidentDepositFee::PAYMENT_STATUS_OPTIONS)->nullable(),
                    TextInput::make('depositFee.payment_method')->label('Sposób płatności')->nullable(),
                ])
                ->columns(2)
                ->visible($isCompleted)
                ->disabled($isFeeLocked),
        ];
    }
}
