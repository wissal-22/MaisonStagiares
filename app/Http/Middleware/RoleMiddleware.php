<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('warning', 'Veuillez vous connecter pour accéder à cette page.');
        }

        $user = Auth::user();

        // Admin has superuser access to operational and reporting modules
        if ($user->role === 'admin') {
            return $next($request);
        }

        if (! empty($roles) && ! in_array($user->role, $roles, true)) {
            if ($user->isCandidat()) {
                return redirect()->route('candidat.dashboard')->with('error', 'Accès non autorisé à cet espace de gestion.');
            }

            return redirect()->route('dashboard')->with('error', 'Vous ne disposez pas des privilèges requis pour ce module.');
        }

        return $next($request);
    }
}
