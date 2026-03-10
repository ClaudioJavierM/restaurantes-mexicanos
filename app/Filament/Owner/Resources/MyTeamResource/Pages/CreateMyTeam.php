<?php

namespace App\Filament\Owner\Resources\MyTeamResource\Pages;

use App\Filament\Owner\Resources\MyTeamResource;
use App\Models\RestaurantTeamMember;
use App\Models\User;
use App\Mail\TeamInvitation;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateMyTeam extends CreateRecord
{
    protected static string $resource = MyTeamResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Get the email from the form
        $email = $data['email'] ?? null;

        if (!$email) {
            Notification::make()
                ->title('Error')
                ->body('Se requiere un email valido')
                ->danger()
                ->send();
            $this->halt();
        }

        // Check if user already exists
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Create a new user with a random password
            $user = User::create([
                'name' => explode('@', $email)[0],
                'email' => $email,
                'password' => Hash::make(Str::random(16)),
                'role' => 'customer',
            ]);
        }

        // Set restaurant_id if not provided (single restaurant owner)
        if (empty($data['restaurant_id'])) {
            $data['restaurant_id'] = Auth::user()->restaurants()->first()->id;
        }

        // Check if this user is already a team member
        $existingMember = RestaurantTeamMember::where('restaurant_id', $data['restaurant_id'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'active'])
            ->first();

        if ($existingMember) {
            Notification::make()
                ->title('Error')
                ->body('Este usuario ya es miembro del equipo o tiene una invitacion pendiente')
                ->danger()
                ->send();
            $this->halt();
        }

        // Set required fields
        $data['user_id'] = $user->id;
        $data['invited_by'] = Auth::id();
        $data['status'] = RestaurantTeamMember::STATUS_PENDING;
        $data["invitation_token"] = RestaurantTeamMember::generateInvitationToken();
        $data["invitation_expires_at"] = now()->addDays(7);

        // Remove email as it's not a field in the model
        unset($data['email']);

        return $data;
    }

    protected function afterCreate(): void
    {
        // Send invitation email
        try {
            $member = $this->record;
            $member->load(['user', 'restaurant', 'inviter']);
            if ($member->user) {
                Mail::to($member->user->email)->send(new TeamInvitation($member));
            }

            Notification::make()
                ->title('Invitacion enviada')
                ->body('Se ha enviado una invitacion a ' . $member->user->email)
                ->success()
                ->send();
        } catch (\Exception $e) {
            \Log::error('Team invitation email failed: ' . $e->getMessage());
            Notification::make()
                ->title('Miembro creado')
                ->body('El miembro fue creado pero hubo un problema enviando el email de invitacion')
                ->warning()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
