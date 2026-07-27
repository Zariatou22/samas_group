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

    public function hasGroup(string|array $groupName): bool
    {
        $names = (array) $groupName;

        return $this->groups()->whereIn('name', $names)->exists();
    }

    /**
     * Reproduit fidèlement la cascade de rôles de Control.php (app CI
     * d'origine) : chaque rôle est vérifié par permission nommée OU
     * appartenance à un groupe homonyme, avec repli en cascade vers un rôle
     * parent — repli qui diffère d'un rôle à l'autre (ce n'est PAS une
     * simple hiérarchie linéaire : "Direction" et "Modération" sont deux
     * branches parallèles qui replient toutes deux sur "Edition", pas l'une
     * sur l'autre ; les rôles transverses (Douane, Shipping, Caisse, Kanis,
     * Crossing, Dg transit, DRH, DACS, CA, BEN) répliquent chacun sur une
     * cible différente).
     */
    public function isAdmin(): bool
    {
        return $this->hasPermission('Administration') || $this->hasGroup('Administrateur') || (int) $this->id === 1;
    }

    public function isEditeur(): bool
    {
        return $this->hasPermission('Edition') || $this->hasGroup('Editeur') || $this->isAdmin();
    }

    public function isDirecteur(): bool
    {
        return $this->hasPermission('Direction') || $this->hasGroup('Directeur') || $this->isEditeur();
    }

    public function isModerateur(): bool
    {
        return $this->hasPermission('Modération') || $this->hasGroup('Modérateur') || $this->isEditeur();
    }

    public function isRedacteur(): bool
    {
        return $this->hasPermission('Rédaction') || $this->hasGroup('Rédacteur') || $this->isModerateur();
    }

    public function isDrh(): bool
    {
        return $this->hasPermission('DRH') || $this->isRedacteur();
    }

    public function isDacs(): bool
    {
        return $this->hasPermission('DACS') || $this->isRedacteur();
    }

    public function isCa(): bool
    {
        return $this->hasPermission('CA') || $this->isRedacteur();
    }

    public function isBen(): bool
    {
        return $this->hasGroup('BEN') || $this->isRedacteur();
    }

    public function isDouane(): bool
    {
        return $this->hasPermission('Douane') || $this->hasGroup('Douane') || $this->isDgTransit();
    }

    public function isShipping(): bool
    {
        return $this->hasPermission('Shipping') || $this->hasGroup('Shipping') || $this->isDirecteur();
    }

    public function isCaisse(): bool
    {
        return $this->hasPermission('Caisse') || $this->hasGroup('Caisse') || $this->isEditeur();
    }

    public function isKanis(): bool
    {
        return $this->hasPermission('Kanis') || $this->hasGroup('Kanis') || $this->isDirecteur();
    }

    public function isCrossing(): bool
    {
        return $this->hasPermission('Crossing') || $this->hasGroup('Crossing') || $this->isDirecteur();
    }

    public function isDgTransit(): bool
    {
        return $this->hasPermission('Dg transit') || $this->hasGroup('Dg transit') || $this->isDirecteur();
    }

    /**
     * Vérification générique par nom de service (permission ou groupe
     * homonyme), avec repli sur "Editeur" — comme Control::is_allowed() en CI.
     */
    public function isAllowedTo(string $service): bool
    {
        return $this->hasPermission($service) || $this->hasGroup($service) || $this->isEditeur();
    }

    /**
     * Niveaux éditoriaux utilisés pour restreindre certains écrans
     * d'administration (gestion des utilisateurs, paramètres) à un niveau
     * plus élevé que le simple accès métier — décision produit assumée pour
     * la réécriture Laravel, l'app CI d'origine ne gate en réalité ces
     * écrans qu'à "Rédaction" comme tout le reste.
     */
    public function hasAccessLevel(string $level): bool
    {
        return match ($level) {
            'Rédaction' => $this->isRedacteur(),
            'Modération' => $this->isModerateur(),
            'Direction' => $this->isDirecteur(),
            'Edition' => $this->isEditeur(),
            'Administration' => $this->isAdmin(),
            default => $this->hasPermission($level),
        };
    }

    /**
     * Accès aux modules métier (BL, clients, chargements, factures...) :
     * gate unique sur "Rédaction", comme
     * `if (! $this->control->is_redacteur()) redirect();` dans chaque
     * contrôleur métier de l'app CI d'origine (il n'existe pas de
     * permission par module distincte type "Saisie"/"Chargement" côté CI).
     */
    public function canAccessModule(): bool
    {
        return $this->isRedacteur();
    }

    public function isAdministrator(): bool
    {
        return $this->hasGroup('Administrateur');
    }
}
