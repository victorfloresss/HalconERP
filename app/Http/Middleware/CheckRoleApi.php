<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleApi
{
    /**
     * Middleware de verificación de roles para endpoints API.
     * Retorna respuestas JSON en vez de redirects o vistas Blade.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string ...$roles  Lista de slugs de roles permitidos (ej: admin, sales)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'No autenticado.',
            ], 401);
        }

        if ($user->role && in_array($user->role->slug, $roles)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'No tienes permisos para acceder a este recurso.',
        ], 403);
    }
}
