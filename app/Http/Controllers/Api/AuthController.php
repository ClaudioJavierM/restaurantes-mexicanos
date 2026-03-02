<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => 'user',
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado exitosamente',
            'data' => [
                'user' => $user->only(['id', 'name', 'email', 'phone', 'role']),
                'token' => $token,
            ]
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales son incorrectas.'],
            ]);
        }

        // Revoke previous tokens for this device
        $deviceName = $request->device_name ?? 'mobile-app';
        $user->tokens()->where('name', $deviceName)->delete();

        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'data' => [
                'user' => $user->only(['id', 'name', 'email', 'phone', 'role', 'avatar']),
                'token' => $token,
            ]
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada exitosamente',
        ]);
    }

    /**
     * Social login (Google, Apple)
     */
    public function socialLogin(Request $request): JsonResponse
    {
        $request->validate([
            'provider' => 'required|in:google,apple',
            'token' => 'required|string',
            'name' => 'nullable|string',
            'email' => 'nullable|email',
        ]);

        $provider = $request->provider;

        try {
            if ($provider === 'google') {
                $socialUser = Socialite::driver('google')->stateless()->userFromToken($request->token);
                $email = $socialUser->getEmail();
                $name = $socialUser->getName();
                $providerId = $socialUser->getId();
            } else {
                // Apple - token contains user info
                $email = $request->email;
                $name = $request->name ?? 'Usuario Apple';
                $providerId = $request->token;
            }

            // Find or create user
            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make(str()->random(24)),
                    'role' => 'user',
                    'email_verified_at' => now(),
                    $provider . '_id' => $providerId,
                ]);
            } else {
                $user->update([$provider . '_id' => $providerId]);
            }

            $token = $user->createToken('mobile-app-' . $provider)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'data' => [
                    'user' => $user->only(['id', 'name', 'email', 'phone', 'role', 'avatar']),
                    'token' => $token,
                    'is_new_user' => $user->wasRecentlyCreated,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en autenticación social: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Se ha enviado un enlace de recuperación a tu correo.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No pudimos enviar el enlace. Verifica tu correo.',
        ], 400);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user()->load(['favorites:id,name,slug,image,average_rating']);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }
}
