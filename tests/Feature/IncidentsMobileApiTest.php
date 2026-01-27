<?php

use App\Models\User;
use App\Models\Incident;
use App\Models\IncidentImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('Get incidents list', function () {
    $response = $this->post('/api/mobile/login', [
        'email' => 'holownik@wp.pl',
        'password' => '1234',
    ]);

    $user = User::where('email', 'holownik@wp.pl')->first();
    $plainToken = $user->personalAccessToken->plain_token;

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
    $response = $this->post('/api/mobile/login', [
        'email' => 'holownik@wp.pl',
        'password' => '1234',
    ]);

    $incidentId = 2;
    $user = User::where('email', 'holownik@wp.pl')->first();
    $plainToken = $user->personalAccessToken->plain_token;

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
    $response = $this->post('/api/mobile/login', [
        'email' => 'holownik@wp.pl',
        'password' => '1234',
    ]);

    $incidentId = 2;
    $user = User::where('email', 'holownik@wp.pl')->first();
    $plainToken = $user->personalAccessToken->plain_token;

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