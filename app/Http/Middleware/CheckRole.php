<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string ...$roles  Lista de slugs de roles permitidos (ej: admin, sales)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Verificar si el usuario está logueado
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // 2. Verificar si el rol del usuario está en la lista de permitidos
        // Buscamos el slug del rol (admin, sales, warehouse, route, purchasing)
        if ($user->role && in_array($user->role->slug, $roles)) {
            return $next($request);
        }

        // 3. Si no tiene permiso, abortar con error 403 (Prohibido)
        abort(403, 'No tienes permisos para acceder a esta sección de Halcón ERP.');
    }
}