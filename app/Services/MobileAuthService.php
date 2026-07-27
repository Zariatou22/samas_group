<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserVariable;
use DateTimeImmutable;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Tokenly\TokenGenerator\TokenGenerator;

/**
 * Reproduit le schéma d'authentification JWT de l'ancienne appli (cf.
 * app/libraries/Auth.php::set_user_auth() du projet CodeIgniter d'origine) :
 * chaque utilisateur a sa propre clé secrète (stockée dans user_variables),
 * utilisée pour signer/valider ses jetons HS512. Ce choix préserve la
 * compatibilité avec l'application mobile existante, qui envoie l'id de
 * l'utilisateur avec chaque appel.
 */
class MobileAuthService
{
    public function issueToken(User $user, string $issuer): string
    {
        $secretKey = (new TokenGenerator)->generateToken(80, 'TK');
        $now = new DateTimeImmutable;

        $token = JWT::encode([
            'iat' => $now->getTimestamp(),
            'iss' => $issuer,
            'nbf' => $now->getTimestamp(),
            'exp' => $now->modify('+1 month')->getTimestamp(),
            'userName' => $user->name,
        ], $secretKey, 'HS512');

        UserVariable::put($user->id, 'user_key', $secretKey);
        UserVariable::put($user->id, 'user_auth', $token);

        return $token;
    }

    public function verify(int $userId, string $jwt): bool
    {
        $secretKey = UserVariable::get($userId, 'user_key');

        if (blank($secretKey)) {
            return false;
        }

        try {
            $now = new DateTimeImmutable;
            $decoded = JWT::decode($jwt, new Key($secretKey, 'HS512'));

            return $decoded->nbf <= $now->getTimestamp() && $decoded->exp >= $now->getTimestamp();
        } catch (ExpiredException|SignatureInvalidException) {
            return false;
        } catch (\Exception) {
            return false;
        }
    }
}
