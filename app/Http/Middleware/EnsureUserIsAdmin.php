<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloque le back-office à tout compte qui n'est pas administrateur.
 * À placer après le middleware `auth` (qui gère les visiteurs non connectés).
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isAdmin()) {
            abort(403, 'Accès réservé aux administrateurs JeCMAP.');
        }

        return $next($request);
    }
}
