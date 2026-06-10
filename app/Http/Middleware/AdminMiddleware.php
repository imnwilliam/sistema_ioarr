<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verificamos si el usuario está logueado y si su rol es 1 (Administrador)
        if (auth()->check() && auth()->user()->id_rol == 1) {
            return $next($request);
        }

        // Si es rol 2 (Consulta) o intentó forzar la URL, lo pateamos al dashboard
        return redirect('/dashboard')->with('error', 'Acceso denegado: Esta acción requiere permisos de Administrador.');
    }
}