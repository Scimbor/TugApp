<?php

namespace App\Filament\Resources\TugUsers\Pages;

use App\Filament\Resources\TugUsers\TugUsersResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateTugUsers extends CreateRecord
{
    protected static string $resource = TugUsersResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = User::TUG_ROLE;
        
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }
}
