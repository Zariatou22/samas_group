<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Miroir du `if (! $this->control->is_redacteur()) redirect();` de chaque
 * contrôleur de l'ancienne appli CodeIgniter — réutilise directement
 * App\Models\User::canAccessModule() (déjà écrit pour Filament).
 */
class EnsureCanAccessModule
{
    public function handle(Request $request, Closure $next, string ...$perms): Response
    {
        if (! $request->user()?->canAccessModule($perms)) {
            abort(403);
        }

        return $next($request);
    }
}
