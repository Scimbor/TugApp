<?php

namespace App\Filament\Resources\TugUsers\Pages;

use App\Filament\Resources\TugUsers\TugUsersResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Laravel\Sanctum\PersonalAccessToken;

class EditTugUsers extends EditRecord
{
    protected static string $resource = TugUsersResource::class;

    protected function getHeaderActions(): array
    {
        $hasToken = $this->record && $this->record->exists && PersonalAccessToken::where('user_id', $this->record->id)->whereNotNull('plain_token')->exists();
        
        return [
            Action::make('generateToken')
                ->label('Generuj token API')
                ->icon('heroicon-o-key')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Generuj nowy token API')
                ->modalDescription('Czy na pewno chcesz wygenerować nowy token? Stary token zostanie usunięty.')
                ->modalSubmitActionLabel('Generuj')
                ->action(function () {
                    $user = $this->record;
                    
                    // Delete all existing tokens before creating a new one
                    $user->tokens()->delete();
                    
                    $token = $user->createToken('mobile-app-token');
                    
                    PersonalAccessToken::where([
                        ['id', $token->accessToken->id],
                    ])->update([
                        'user_id' => $user->id,
                        'plain_token' => $token->plainTextToken,
                    ]);
                    
                    Notification::make()
                        ->title('Token wygenerowany')
                        ->body('Token został wygenerowany. Skopiuj go z formularza.')
                        ->success()
                        ->persistent()
                        ->send();
                    
                    // Refresh form to show the new token, preserving existing form data
                    $currentFormData = $this->form->getState();
                    $this->form->fill(array_merge($currentFormData, [
                        'api_token' => $token->plainTextToken,
                    ]));
                }),
            Action::make('deleteToken')
                ->label('Usuń token')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Usuń token API')
                ->modalDescription('Czy na pewno chcesz usunąć token? Użytkownik nie będzie mógł korzystać z API.')
                ->modalSubmitActionLabel('Usuń')
                ->action(function () {
                    $this->deleteToken();
                })
                ->visible($hasToken),
        ];
    }

    public function deleteToken(): void
    {
        $user = $this->record;
        $user->tokens()->delete();
        
        Notification::make()
            ->title('Token usunięty')
            ->body('Token został pomyślnie usunięty.')
            ->success()
            ->send();
        
        // Update form to clear token, preserving existing form data
        $currentFormData = $this->form->getState();
        $this->form->fill(array_merge($currentFormData, [
            'api_token' => null,
        ]));
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load plain token from personal_access_tokens table by user_id
        $token = PersonalAccessToken::where('user_id', $this->record->id)
            ->whereNotNull('plain_token')
            ->first();
        $data['api_token'] = $token ? $token->plain_token : null;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }

    public static function canEdit($record): bool
    {
        if ($record instanceof User && !$record->is_active) {
            return false;
        }
        
        return parent::canEdit($record) ?? true;
    }
}
