<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to provider for authentication
     */
    public function redirect(string $provider)
    {
        $this->validateProvider($provider);
        
        // Generate a unique state key for this OAuth flow
        $stateKey = Str::random(40);
        
        // Store the intended URL in cache (survives external redirects)
        $redirectUrl = request()->get('redirect') ?? request()->headers->get('referer');
        if ($redirectUrl && str_contains($redirectUrl, request()->getHost())) {
            Cache::put('oauth_redirect_' . $stateKey, $redirectUrl, now()->addMinutes(10));
        }
        
        // Store state key in session
        session()->put('oauth_state_key', $stateKey);
        
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle callback from provider
     */
    public function callback(string $provider)
    {
        $this->validateProvider($provider);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Social auth callback error', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('login')
                ->with('error', __('Error al conectar con :provider. Por favor intenta de nuevo.', ['provider' => ucfirst($provider)]));
        }

        // Check if user exists with this provider ID
        $providerIdColumn = $provider . '_id';
        $user = User::where($providerIdColumn, $socialUser->getId())->first();

        if (!$user) {
            // Check if user exists with same email
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Link social account to existing user
                $user->update([
                    $providerIdColumn => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                    'provider' => $provider,
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'Usuario',
                    'email' => $socialUser->getEmail(),
                    'email_verified_at' => now(),
                    $providerIdColumn => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                    'provider' => $provider,
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'user',
                ]);
            }
        } else {
            // Update avatar if changed
            $user->update([
                'avatar' => $socialUser->getAvatar(),
            ]);
        }

        Auth::login($user, true);

        // Get the redirect URL from cache
        $stateKey = session()->pull('oauth_state_key');
        $redirectUrl = $stateKey ? Cache::pull('oauth_redirect_' . $stateKey) : null;
        
        if ($redirectUrl) {
            return redirect($redirectUrl);
        }

        return redirect('/');
    }

    /**
     * Validate provider is supported
     */
    protected function validateProvider(string $provider): void
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404, 'Proveedor no soportado');
        }
    }
}
