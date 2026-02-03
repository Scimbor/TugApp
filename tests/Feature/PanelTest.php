<?php

use App\Models\User;
use App\Models\Incident;
use App\Filament\Resources\Incidents\Pages\ListIncidents;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\WithFaker;
use \App\Filament\Resources\Incidents\Modals\Actions\IncidentCreator;
use Livewire\TemporaryUploadedFile;
use Illuminate\Http\Testing\File;
use App\Models\IncidentImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\Incidents\Modals\Actions\IncidentUpdater;

uses(WithFaker::class);

beforeEach(function () {
    $this->tempUser = User::factory()->create([
        'role' => 'user',
        'email' => fake()->unique()->safeEmail(),
        'password' => Hash::make('secret123'),
        'is_active' => true,
    ]);

    $this->actingAs($this->tempUser);
});

it('Creates incident with address', function () {
    $photoName1 = md5('photo1').'.jpg';
    $photoName2 = md5('photo2').'.jpg';

    $files = [
        UploadedFile::fake()->image($photoName1),
        UploadedFile::fake()->image($photoName2),
    ];

    $incident = IncidentCreator::createFromFormData([
        'user_panel_id' => $this->tempUser->id,
        'vehicle_number' => fake()->bothify('KR###??'),
        'vehicle_vin' => fake()->bothify('#################'),
        'vehicle_brand' => fake()->company(),
        'vehicle_model' => fake()->word(),
        'vehicle_type' => 'car',
        'description' => fake()->sentence(),
        'status' => Incident::STATUS_OPEN,
        'address' => [
            'street' => 'Prosta',
            'house_number' => '10A',
            'apartment_number' => '5',
            'city' => 'Warszawa',
            'zip' => '00-001',
        ],
        'images' => array_map(fn($file) => ['image_path' => IncidentImage::IMAGES_DIRECTORY . '/' . $file->getClientOriginalName()], $files),
    ]);

    foreach ($files as $file) {
        $file->storeAs(IncidentImage::IMAGES_DIRECTORY . '/' . $incident->id, $file->getClientOriginalName());
    }

    expect($incident)->not->toBeNull();
    expect($incident->address->street)->toBe('Prosta');
    expect($incident->address->city)->toBe('Warszawa');

    foreach ($files as $file) {
        $imageName = $file->getClientOriginalName();
        Storage::assertExists(
            IncidentImage::IMAGES_DIRECTORY . '/' . $incident->id . '/' . $imageName
        );
    }
});

it('Edits incident with faker data', function () {
    $incident = IncidentCreator::createFromFormData([
        'vehicle_number' => fake()->bothify('KR###??'),
        'vehicle_vin' => fake()->bothify('#################'),
        'vehicle_brand' => fake()->company(),
        'vehicle_model' => fake()->word(),
        'vehicle_type' => 'car',
        'description' => fake()->sentence(),
        'status' => Incident::STATUS_OPEN,
        'address' => [
            'street' => fake()->streetName(),
            'house_number' => fake()->buildingNumber(),
            'apartment_number' => fake()->randomDigit(),
            'city' => fake()->city(),
            'zip' => fake()->postcode(),
        ],
    ]);

    $updatedData = [
        'vehicle_number' => fake()->bothify('KR###??'),
        'vehicle_vin' => fake()->bothify('#################'),
        'vehicle_brand' => fake()->company(),
        'vehicle_model' => fake()->word(),
        'vehicle_type' => 'car',
        'description' => fake()->sentence(),
        'status' => Incident::STATUS_PENDING,
        'address' => [
            'street' => fake()->streetName(),
            'house_number' => fake()->buildingNumber(),
            'apartment_number' => fake()->randomDigit(),
            'city' => fake()->city(),
            'zip' => fake()->postcode(),
        ],
    ];
    
    IncidentUpdater::updateFromFormData($incident,
    $updatedData);

    $editIncident = Incident::find($incident->id);

    expect($editIncident->vehicle_number)->toBe($updatedData['vehicle_number']);
    expect($editIncident->vehicle_vin)->toBe($updatedData['vehicle_vin']);
    expect($editIncident->vehicle_brand)->toBe($updatedData['vehicle_brand']);
    expect($editIncident->vehicle_model)->toBe($updatedData['vehicle_model']);
    expect($editIncident->description)->toBe($updatedData['description']);
    expect($editIncident->status)->toBe(Incident::STATUS_PENDING);

    expect($editIncident->address->street)->toBe($updatedData['address']['street']);
    expect($editIncident->address->house_number)->toBe($updatedData['address']['house_number']);
    expect($editIncident->address->apartment_number)->toEqual($updatedData['address']['apartment_number']);
    expect($editIncident->address->city)->toBe($updatedData['address']['city']);
    expect($editIncident->address->zip)->toBe($updatedData['address']['zip']);
});


it('Deletes an incident via filament delete action', function () {
    $incident = Incident::whereNotIn('status', Incident::CLOSED_MODIFICATION_ROW_STATUSES)->latest()->first();

    Livewire::test(ListIncidents::class)
    ->callTableAction('delete', $incident)
    ->assertHasNoActionErrors();

    $this->assertDatabaseMissing('incidents', ['id' => $incident->id]);
    $this->assertDatabaseMissing('incidents_address', ['incident_id' => $incident->id]);
    $this->assertDatabaseMissing('incidents_images', ['incident_id' => $incident->id]);

    Storage::assertMissing(IncidentImage::IMAGES_DIRECTORY . '/' . $incident->id);
});