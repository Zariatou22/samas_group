<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\MobileAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vérifie le jeton Bearer JWT propre à l'utilisateur (cf. MobileAuthService).
 * Le client doit envoyer son "user" (id) avec chaque requête, comme le
 * faisait l'application mobile existante.
 */
class AuthenticateMobileToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $userId = (int) $request->input('user');
        $header = $request->header('Authorization', '');

        if (! $userId || ! preg_match('/Bearer\s+(\S+)/', $header, $matches)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $user = User::find($userId);

        if (! $user || $user->banned) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        if (! app(MobileAuthService::class)->verify($userId, trim($matches[1]))) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        auth()->setUser($user);

        return $next($request);
    }
}
