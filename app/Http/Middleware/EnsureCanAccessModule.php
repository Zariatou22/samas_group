<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Miroir du `if (! $this->control->is_redacteur()) redirect();` de chaque
 * contrôleur métier de l'ancienne appli CodeIgniter.
 */
class EnsureCanAccessModule
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->canAccessModule()) {
            abort(403);
        }

        return $next($request);
    }
}
