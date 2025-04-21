<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyIdentityDocument
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Debes iniciar sesión.');
        }

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Validar si tiene un documento cargado y aprobado
        if (!$user->document || !$user->document_verified) {
            return redirect('/my-profile')->with('error', 'Debes cargar tu documento de identidad y esperar la validación del administrador.');
        }

        return $next($request);
    }
}
