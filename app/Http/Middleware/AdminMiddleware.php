<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
{
    // Verificamos si el usuario es admin (ajusta 'rol' o 'role' según tu DB)
    if ($request->user() && $request->user()->rol === 'admin') {
        return $next($request);
    }

    return response()->json(['message' => 'No autorizado. Se requiere rol de administrador.'], 403);
}
}
