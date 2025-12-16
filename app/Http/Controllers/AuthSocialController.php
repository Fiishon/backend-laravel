<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;

class AuthSocialController extends Controller
{
    // El nombre debe coincidir con lo que pusimos en api.php
    public function LoginGoogle(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        try {
            // Usa directamente tu ID de cliente web aquí para evitar problemas con .env si no está configurado
            $client = new GoogleClient(['client_id' => '397207120128-ulevn3m2ph1190r935d2qu7jqnptqvls.apps.googleusercontent.com']);
            $payload = $client->verifyIdToken($request->id_token);

            if (!$payload) {
                return response()->json(['message' => 'Token de Google inválido'], 401);
            }

            $googleId = $payload['sub'];
            $email = $payload['email'];
            $nombre = $payload['given_name'] ?? 'Usuario';
            $apellido = $payload['family_name'] ?? '';
            // Capturamos la foto también
            $foto = $payload['picture'] ?? null;

            $user = User::where('google_id', $googleId)->orWhere('correo', $email)->first();

            if (!$user) {
                $user = User::create([
                    'nombre' => $nombre,
                    'apellido_paterno' => $apellido ?: 'Google',
                    'apellido_materno' => '',
                    'correo' => $email,
                    'google_id' => $googleId,
                    'contraseña' => null,
                    'rol' => User::ROL_ESTUDIANTE,
                    'foto_perfil' => $foto // Guardamos la foto de Google
                ]);
            } else {
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleId]);
                }
            }

            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'mensaje' => 'Bienvenido vía OpenID',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            Log::error('Error OpenID: ' . $e->getMessage());
            return response()->json(['message' => 'Error validando con Google: ' . $e->getMessage()], 500);
        }
    }
}