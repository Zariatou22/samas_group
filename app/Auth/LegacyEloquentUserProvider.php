<?php

namespace App\Auth;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

/**
 * L'ancienne application (CodeIgniter) stockait les mots de passe en
 * sha256(md5($id) . $password) — cf. app/helpers/assets_helper.php::hash_password()
 * du projet d'origine. Ce provider vérifie d'abord ce format legacy et,
 * en cas de succès, réenregistre le mot de passe avec le hasher Laravel
 * (bcrypt) pour que les connexions suivantes passent par le chemin standard.
 * Les comptes déjà migrés (hash bcrypt) sont vérifiés normalement.
 */
class LegacyEloquentUserProvider extends EloquentUserProvider
{
    public function validateCredentials(AuthenticatableContract $user, array $credentials): bool
    {
        $plain = $credentials['password'] ?? '';
        $stored = $user->getAuthPassword();

        if ($this->isLegacyHash($stored)) {
            $legacy = $this->legacyHash($plain, $user->getAuthIdentifier());

            if (hash_equals($stored, $legacy)) {
                $this->rehashToBcrypt($user, $plain);

                return true;
            }

            return false;
        }

        return $this->hasher->check($plain, $stored);
    }

    protected function isLegacyHash(string $hash): bool
    {
        // sha256 hex = 64 caractères ; bcrypt commence toujours par "$2y$".
        return strlen($hash) === 64 && ! str_starts_with($hash, '$2y$');
    }

    protected function legacyHash(string $plain, int|string $userId): string
    {
        return hash('sha256', md5((string) $userId).$plain);
    }

    protected function rehashToBcrypt(AuthenticatableContract $user, string $plain): void
    {
        /** @var User $user */
        $user->forceFill(['pass' => $this->hasher->make($plain)])->save();
    }
}
