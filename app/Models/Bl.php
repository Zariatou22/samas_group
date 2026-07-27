<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Bon de Livraison / Bill of Lading — le cœur du système. Chaque BL passe par
 * un cycle de vie : en attente d'arrivée -> en attente d'opération -> en
 * cours d'opération (is_started) -> clôturé (is_completed).
 */
class Bl extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'bl';

    protected $attributes = [
        'movements' => '',
        'is_urgent' => false,
    ];

    protected $fillable = [
        'user',
        'telex',
        'bl',
        'customer',
        'customer_company',
        'company',
        'description',
        'type_operation',
        'movements',
        'product_type',
        'quantity',
        'nb_package',
        'shipper',
        'vessel',
        'loading_date',
        'port_of_load',
        'port_of_discharge',
        'eta_date',
        'consignee',
        'notify',
        'product_value',
        'tariff',
        'route',
        'observation',
        'agent',
        'is_urgent',
        'is_started',
        'is_completed',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'telex' => 'boolean',
            'is_urgent' => 'boolean',
            'is_started' => 'boolean',
            'is_completed' => 'boolean',
            'loading_date' => 'date',
            'eta_date' => 'date',
            'quantity' => 'float',
            'product_value' => 'float',
            'tariff' => 'float',
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }

    /**
     * Nommées différemment des colonnes DB ("customer", "company") avec
     * lesquelles elles collisionneraient sinon : Eloquent donne toujours la
     * priorité à l'attribut brut sur une relation de même nom.
     */
    public function mandataire(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer');
    }

    public function customerCompany(): BelongsTo
    {
        return $this->belongsTo(CustomerCompany::class, 'customer_company');
    }

    public function shippingCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company');
    }

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type');
    }

    public function containers(): HasMany
    {
        return $this->hasMany(Container::class, 'bl');
    }

    public function authorizations(): HasMany
    {
        return $this->hasMany(Authorization::class, 'bl');
    }

    public function loadings(): HasMany
    {
        return $this->hasMany(Loading::class, 'bl');
    }

    public function exchange(): HasOne
    {
        return $this->hasOne(Exchange::class, 'bl');
    }

    public function deliveryNote(): HasOne
    {
        return $this->hasOne(DeliveryNote::class, 'bl');
    }

    /**
     * Un BL "TRANSFERT MAD" peut avoir un transfert par conteneur — la date
     * affichée dans les listes est la plus récente (MAX), comme dans
     * l'ancienne appli (Bls::list_arrived()/list_ongoing()).
     */
    public function transferts(): HasMany
    {
        return $this->hasMany(Transfert::class, 'bl');
    }

    /**
     * Ce qui manque avant de pouvoir démarrer l'opération (passage à "en
     * cours") : échange BL et BAD reçus, transfert vers terminal enregistré
     * pour les BL "TRANSFERT MAD". Liste vide = le BL peut démarrer.
     *
     * @return array<int, string>
     */
    public function missingStartRequirements(): array
    {
        $missing = [];

        if (! $this->exchange) {
            $missing[] = 'Échange BL';
        }

        if (! $this->deliveryNote) {
            $missing[] = 'BAD';
        }

        if ($this->transferts()->doesntExist()) {
            $missing[] = 'Date de transfert';
        }

        return $missing;
    }

    /**
     * Quantité déjà couverte par des déclarations d'autorisation actives.
     */
    public function getAuthorizedQuantityAttribute(): float
    {
        return (float) $this->authorizations()->sum('quantity');
    }

    public function getAvailableQuantityAttribute(): float
    {
        return max(0, $this->quantity - $this->authorized_quantity);
    }

    /**
     * Quantité déjà couverte par des chargements enregistrés.
     */
    public function getLoadedQuantityAttribute(): float
    {
        return (float) $this->loadings()->sum('quantity');
    }

    public function getAvailableForLoadingAttribute(): float
    {
        return max(0, $this->quantity - $this->loaded_quantity);
    }

    /**
     * En attente d'arrivée : aucun conteneur, ou au moins un conteneur dont
     * l'ETA n'est pas encore passée.
     */
    public function scopeWaiting(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->doesntHave('containers')
                ->orWhereHas('containers', function (Builder $c) {
                    $c->whereNull('eta')->orWhereDate('eta', '>=', now()->toDateString());
                });
        });
    }

    /**
     * Arrivé, en attente que l'opération soit démarrée : au moins un
     * conteneur dont l'ETA est passée, opération pas encore commencée, et
     * l'échange de documents pas encore reçu.
     */
    public function scopeArrived(Builder $query): Builder
    {
        return $query->where('is_started', false)
            ->doesntHave('exchange')
            ->whereHas('containers', function (Builder $c) {
                $c->whereDate('eta', '<', now()->toDateString());
            });
    }

    public function scopeOngoing(Builder $query): Builder
    {
        return $query->where('is_started', true)->where('is_completed', false);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('is_completed', true);
    }
}
