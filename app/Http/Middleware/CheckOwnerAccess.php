<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckOwnerAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // If not logged in, let Filament handle redirect to login
        if (!$user) {
            return $next($request);
        }

        // If user has restaurants but role is still customer, promote to owner
        if ($user->role === 'customer' && $user->restaurants()->exists()) {
            $user->role = 'owner';
            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
            }
            $user->save();
        }

        // Check if user is an active team member of any restaurant
        $isTeamMember = $user->activeTeamMemberships()->exists();

        // If user is a team member but role is customer, allow access
        if ($isTeamMember && !in_array($user->role, ['owner', 'admin'])) {
            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
                $user->save();
            }
            return $next($request);
        }

        // If user still cannot access owner panel, redirect friendly
        if (!$user->hasVerifiedEmail() ||
            !in_array($user->role, ['owner', 'admin']) ||
            !$user->restaurants()->exists()) {
            return redirect('/for-owners')->with('info', 'Necesitas reclamar un restaurante para acceder al dashboard.');
        }

        return $next($request);
    }
}