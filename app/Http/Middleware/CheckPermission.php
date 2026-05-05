<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RolePermission;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission  Permission slug to check (e.g., 'programs.view')
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Verificar si el rol tiene el permiso
        if (!RolePermission::hasPermission($user->role, $permission)) {
            // Si es una petición AJAX, devolver JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para realizar esta accion.'
                ], 403);
            }
            
            // Si no, redirigir con mensaje de error para SweetAlert
            return redirect()->back()->with('permission_denied', 'No tienes permiso para acceder a esta seccion.');
        }

        return $next($request);
    }
}
