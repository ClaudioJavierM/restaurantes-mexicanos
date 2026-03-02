<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SSOController extends Controller
{
    public function callback(Request $request)
    {
        $token = $request->query('token');
        
        if (!$token) {
            return redirect('/admin/login')->with('error', 'Token SSO no proporcionado');
        }

        try {
            $secret = env('SSO_SECRET', 'MfGroupSSO2024SecretKey!@#');
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            
            $user = User::where('email', $decoded->email)->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $decoded->name,
                    'email' => $decoded->email,
                    'password' => bcrypt(str()->random(32)),
                ]);
            }
            
            Auth::login($user, true);
            
            return redirect('/admin')->with('success', 'Sesion iniciada via SSO');
                
        } catch (\Exception $e) {
            return redirect('/admin/login')->with('error', 'Error SSO: ' . $e->getMessage());
        }
    }
}
