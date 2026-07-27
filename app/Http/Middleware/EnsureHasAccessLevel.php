<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vérifie un niveau hiérarchique éditorial (User::hasAccessLevel()), pour les
 * modules qui ne s'appuient pas sur les permissions opérationnelles nommées
 * (Gestion des utilisateurs, Paramètres).
 */
class EnsureHasAccessLevel
{
    public function handle(Request $request, Closure $next, string $level): Response
    {
        if (! $request->user()?->hasAccessLevel($level)) {
            abort(403);
        }

        return $next($request);
    }
}
