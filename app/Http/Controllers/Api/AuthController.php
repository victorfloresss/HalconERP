<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login: emite un token Bearer para la sesión de la API.
     *
     * POST /api/login
     * Body: { email, password }
     * Response: { token, user: { id, name, email, role } }
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::with('role')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Revocar tokens anteriores para evitar acumulación
        $user->tokens()->delete();

        // Crear nuevo token con el nombre del dispositivo/app
        $token = $user->createToken(
            $request->device_name ?? 'nextjs-app'
        )->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => [
                    'id'   => $user->role->id,
                    'name' => $user->role->name,
                    'slug' => $user->role->slug,
                ],
            ],
        ]);
    }

    /**
     * Logout: revoca el token actual.
     *
     * POST /api/logout
     * Header: Authorization: Bearer {token}
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente.',
        ]);
    }

    /**
     * Retorna los datos del usuario autenticado.
     *
     * GET /api/user
     * Header: Authorization: Bearer {token}
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('role');

        return response()->json([
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => [
                    'id'   => $user->role->id,
                    'name' => $user->role->name,
                    'slug' => $user->role->slug,
                ],
            ],
        ]);
    }
}
