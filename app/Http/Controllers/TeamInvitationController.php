<?php

namespace App\Http\Controllers;

use App\Models\RestaurantTeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TeamInvitationController extends Controller
{
    /**
     * Show the invitation acceptance page
     */
    public function show(string $token)
    {
        $member = RestaurantTeamMember::where('invitation_token', $token)
            ->with(['restaurant', 'user', 'inviter'])
            ->first();

        if (!$member) {
            return view('team.invitation-invalid', [
                'reason' => 'not_found',
                'message' => 'Esta invitacion no existe o ya fue utilizada.',
            ]);
        }

        if ($member->status !== 'pending') {
            return view('team.invitation-invalid', [
                'reason' => 'already_used',
                'message' => 'Esta invitacion ya fue aceptada o revocada.',
            ]);
        }

        if ($member->invitation_expires_at && $member->invitation_expires_at->isPast()) {
            return view('team.invitation-invalid', [
                'reason' => 'expired',
                'message' => 'Esta invitacion ha expirado. Solicita una nueva invitacion al propietario.',
            ]);
        }

        // Check if user needs to set password (new user)
        // User needs password if they never logged in (created via team invitation)
        $needsPassword = !$member->user->last_login_at;

        return view('team.accept-invitation', [
            'member' => $member,
            'token' => $token,
            'needsPassword' => $needsPassword,
        ]);
    }

    /**
     * Accept the invitation
     */
    public function accept(Request $request, string $token)
    {
        $member = RestaurantTeamMember::where('invitation_token', $token)
            ->with(['restaurant', 'user'])
            ->first();

        if (!$member || $member->status !== 'pending') {
            return redirect()->route('home')
                ->with('error', 'La invitacion no es valida.');
        }

        if ($member->invitation_expires_at && $member->invitation_expires_at->isPast()) {
            return redirect()->route('home')
                ->with('error', 'Esta invitacion ha expirado.');
        }

        $user = $member->user;

        // If user needs to set a password
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|min:8|confirmed',
            ], [
                'password.required' => 'La contrasena es requerida.',
                'password.min' => 'La contrasena debe tener al menos 8 caracteres.',
                'password.confirmed' => 'Las contrasenas no coinciden.',
            ]);

            $user->password = Hash::make($request->password);
        }

        // Always ensure email is verified for team members
        $user->email_verified_at = $user->email_verified_at ?? now();
        $user->last_login_at = now();
        $user->save();

        // Accept the invitation
        $member->accept();

        \Log::info('Team invitation accepted', [
            'user_id' => $user->id,
            'email' => $user->email,
            'member_id' => $member->id,
            'restaurant' => $member->restaurant->name,
            'email_verified' => $user->email_verified_at,
        ]);

        // Log in the user
        Auth::login($user);

        return redirect()->route('filament.owner.pages.dashboard')
            ->with('success', "Bienvenido al equipo de {$member->restaurant->name}!");
    }

        /**
     * Decline the invitation
     */
    public function decline(string $token)
    {
        $member = RestaurantTeamMember::where('invitation_token', $token)->first();

        if ($member && $member->status === 'pending') {
            $member->update([
                'status' => 'revoked',
                'revoked_at' => now(),
                'revoke_reason' => 'Declinada por el usuario',
            ]);
        }

        return redirect()->route('home')
            ->with('info', 'Has declinado la invitacion.');
    }
}
