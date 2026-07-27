<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public $timestamps = false;

    protected $fillable = [
        'email',
        'pass',
        'name',
        'nom',
        'prenoms',
        'fullname',
        'sexe',
        'banned',
    ];

    protected $hidden = [
        'pass',
        'secret',
        'verification_code',
        'forgot_exp',
        'remember_exp',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'banned' => 'boolean',
            'last_login' => 'datetime',
            'last_activity' => 'datetime',
            'last_login_attempt' => 'datetime',
            'remember_time' => 'datetime',
        ];
    }

    public function displayName(): string
    {
        return $this->fullname ?: trim("{$this->prenoms} {$this->nom}") ?: $this->name ?: $this->email;
    }

    /**
     * Laravel s'attend à une colonne "password" ; la table legacy utilise "pass".
     */
    public function getAuthPassword(): string
    {
        return $this->pass;
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'user_to_group', 'user_id', 'group_id');
    }

    public function directPerms(): BelongsToMany
    {
        return $this->belongsToMany(Perm::class, 'perm_to_user', 'user_id', 'perm_id');
    }

    /**
     * Permissions effectives : celles des groupes de l'utilisateur + ses permissions directes.
     */
    public function allPermissions(): \Illuminate\Support\Collection
    {
        return once(function () {
            $viaGroups = Perm::query()
                ->whereHas('groups', fn ($q) => $q->whereIn('groups.id', $this->groups()->pluck('groups.id')))
                ->pluck('name');

            return $viaGroups->merge($this->directPerms()->pluck('name'))->unique()->values();
        });
    }

    public function hasPermission(string $permName): bool
    {
        return $this->allPermissions()->contains($permName);
    }

    /**
     * Les permissions "éditoriales" sont hiérarchiques dans l'app d'origine :
     * Administration > Edition > Modération > Rédaction. Avoir un niveau
     * donne accès à tout ce qui est en dessous (ex: un Editeur a aussi
     * accès à ce qui nécessite seulement "Rédaction").
     */
    public function hasAccessLevel(string $minimumPermission): bool
    {
        $hierarchy = ['Rédaction', 'Modération', 'Edition', 'Administration'];
        $index = array_search($minimumPermission, $hierarchy, true);

        if ($index === false) {
            return $this->hasPermission($minimumPermission);
        }

        $accepted = array_slice($hierarchy, $index);

        return $this->allPermissions()->intersect($accepted)->isNotEmpty();
    }

    public function hasGroup(string|array $groupName): bool
    {
        $names = (array) $groupName;

        return $this->groups()->whereIn('name', $names)->exists();
    }

    /**
     * Accès à un module métier : soit via la hiérarchie éditoriale (à partir
     * de "Rédaction" — Administration y a donc toujours accès), soit via une
     * des permissions opérationnelles propres à ce module (Chargement,
     * Saisie, Compatibilité, etc.).
     */
    public function canAccessModule(array $operationalPerms = []): bool
    {
        if ($this->hasAccessLevel('Rédaction')) {
            return true;
        }

        return $this->allPermissions()->intersect($operationalPerms)->isNotEmpty();
    }

    public function isAdministrator(): bool
    {
        return $this->hasGroup('Administrateur');
    }
}
