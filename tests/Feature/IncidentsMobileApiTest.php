<?php

use App\Models\User;
use App\Models\Incident;
use App\Models\IncidentImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->tempUser = User::factory()->create([
        'role' => 'tug',
        'email' => time().'_tempuser@test.com',
        'password' => Hash::make('secret123'),
    ]);

    $loginResponse = $this->post('/api/mobile/login', [
        'email' => $this->tempUser->email,
        'password' => 'secret123',
    ]);
});

afterEach(function () {
    $this->tempUser->delete();
});

it('Get incidents list', function () {
    $plainToken = $this->tempUser->personalAccessToken->plain_token;

    $response = $this
        ->withHeader('Authorization', 'Bearer ' . $plainToken)
        ->get('/api/mobile/incidents/get');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'incidents' => [
            '*' => [
                'id',
                'vehicle_number',
                'vehicle_vin',
                'vehicle_brand',
                'vehicle_model',
                'vehicle_type',
                'description',
                'status',
                'created_at',
                'updated_at',
                'address' => [
                    'id',
                    'incident_id',
                    'street',
                    'house_number',
                    'apartment_number',
                    'city',
                    'zip',
                    'created_at',
                    'updated_at',
                ],
            ],
        ],
    ]);
});

it('Update incident status', function () {
    $incidentId = 1;
    $plainToken =$this->tempUser->personalAccessToken->plain_token;

    $response = $this
        ->withHeader('Authorization', 'Bearer ' . $plainToken)
        ->post('/api/mobile/incidents/update/status/'.$incidentId, [
            'status' => Incident::STATUS_IN_PROGRESS,
        ]);

    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Status zaktualizowany',
    ]);

    $incident = Incident::find($incidentId);

    expect($incident->status)->toBe(Incident::STATUS_IN_PROGRESS);
});

it('Uploads incident images', function () {
    $incidentId = 1;
    $plainToken = $this->tempUser->personalAccessToken->plain_token;

    $photoName1 = md5('photo1').'.jpg';
    $photoName2 = md5('photo2').'.jpg';

    $files = [
        UploadedFile::fake()->image($photoName1),
        UploadedFile::fake()->image($photoName2),
    ];

    $response = $this->withHeader('Authorization', 'Bearer ' . $plainToken)
        ->post('/api/mobile/incidents/save/images/'.$incidentId, [
            'images' => $files,
        ]);

    $response->assertStatus(200);

    foreach ($files as $file) {
        $imageName = $file->getClientOriginalName() . '.' . $file->getClientOriginalExtension();
        Storage::assertExists(
            IncidentImage::IMAGES_DIRECTORY . '/' . $incidentId . '/' . $imageName
        );
    }

    $response->assertJson([
        'message' => 'Zdjęcia zapisane',
    ]);
});