<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Nombres de los roles permitidos (ej: 'ciudadano', 'administrador')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Verificar autenticación y que el usuario tenga un rol asociado directamente
        if (! $user || ! $user->role) {
            return response()->json([
                'message' => 'Acceso denegado: El usuario no posee un rol asignado.',
            ], Response::HTTP_FORBIDDEN);
        }

        $userRole = strtolower(trim($user->role->nombre));
        $allowedRoles = array_map(fn ($role) => strtolower(trim($role)), $roles);

        if (! in_array($userRole, $allowedRoles, true)) {
            return response()->json([
                'message' => 'Acceso denegado: No tienes los permisos necesarios para realizar esta acción.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}