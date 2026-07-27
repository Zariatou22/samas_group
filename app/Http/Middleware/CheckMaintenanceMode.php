<?php

namespace App\Http\Middleware;

use App\Models\SystemVariable;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloque les visiteurs non connectés derrière une page de maintenance quand
 * le réglage général "maintenance" est actif — équivalent du hook
 * User_auth::check_user() en CI (qui ne gate que les utilisateurs non
 * connectés, pas le staff déjà en session).
 */
class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // Comparaison par chemin (et non par nom de route) car la route POST
        // de connexion n'est pas nommée.
        $exempt = $request->is('maintenance', 'login', 'logout');

        if (! $exempt && ! $request->user() && SystemVariable::get('maintenance') === '1') {
            return response()->view('maintenance', status: 503);
        }

        return $next($request);
    }
}
