<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * "Autorisation" (déclaration d'autorisation d'enlèvement) : couvre une partie
 * (ou la totalité) de la marchandise d'un BL, pour un lieu d'enlèvement donné.
 * Le module Chargement (à venir) viendra consommer ces autorisations au fur
 * et à mesure du chargement réel.
 */
class Authorization extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'authorization';

    protected $fillable = [
        'user',
        'bl',
        'customer',
        'customer_company',
        'auth_number',
        'nb_package',
        'nb_container',
        'quantity',
        'source',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'float',
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }

    /**
     * Nommées différemment des colonnes DB ("bl", "customer", "source") avec
     * lesquelles elles collisionneraient sinon (Eloquent priorise toujours
     * l'attribut brut sur une relation de même nom).
     */
    public function parentBl(): BelongsTo
    {
        return $this->belongsTo(Bl::class, 'bl');
    }

    public function mandataire(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer');
    }

    public function customerCompany(): BelongsTo
    {
        return $this->belongsTo(CustomerCompany::class, 'customer_company');
    }

    public function pickupSource(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'source');
    }

    public function loadings(): HasMany
    {
        return $this->hasMany(Loading::class, 'authorization');
    }

    public function getLoadedQuantityAttribute(): float
    {
        return (float) $this->loadings()->sum('quantity');
    }

    public function isSettled(): bool
    {
        return $this->loaded_quantity >= $this->quantity;
    }

    /**
     * Déclarations pas encore entièrement chargées.
     */
    public function scopeOngoing(Builder $query): Builder
    {
        return $query->whereRaw(
            '(select coalesce(sum(quantity), 0) from loading where loading.authorization = authorization.id) < authorization.quantity'
        );
    }

    /**
     * Déclarations "soldées" : entièrement chargées.
     */
    public function scopeSettled(Builder $query): Builder
    {
        return $query->whereRaw(
            '(select coalesce(sum(quantity), 0) from loading where loading.authorization = authorization.id) >= authorization.quantity'
        );
    }
}
