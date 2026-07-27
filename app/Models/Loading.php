<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Un chargement = un voyage de camion venant enlever une partie de la
 * marchandise autorisée sur un BL (cf. Authorization). Peut être rattaché à
 * des conteneurs précis (loading_containers) pour les BL en dépotage.
 */
class Loading extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'loading';

    protected $fillable = [
        'user',
        'customer',
        'customer_company',
        'bl',
        'nb_package',
        'authorization',
        'quantity',
        'car',
        'owner',
        'driver',
        'source',
        'status',
        'loading_date',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'float',
            'loading_date' => 'datetime',
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }

    /**
     * Nommées différemment des colonnes DB avec lesquelles elles
     * collisionneraient sinon (Eloquent priorise toujours l'attribut brut).
     */
    public function mandataire(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer');
    }

    public function customerCompany(): BelongsTo
    {
        return $this->belongsTo(CustomerCompany::class, 'customer_company');
    }

    public function parentBl(): BelongsTo
    {
        return $this->belongsTo(Bl::class, 'bl');
    }

    public function declaration(): BelongsTo
    {
        return $this->belongsTo(Authorization::class, 'authorization');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car');
    }

    public function carOwner(): BelongsTo
    {
        return $this->belongsTo(CarOwner::class, 'owner');
    }

    public function carDriver(): BelongsTo
    {
        return $this->belongsTo(CarDriver::class, 'driver');
    }

    public function pickupSource(): BelongsTo
    {
        return $this->belongsTo(Source::class, 'source');
    }

    public function loadedContainers(): HasMany
    {
        return $this->hasMany(LoadingContainer::class, 'loading');
    }

    public function t1(): HasOne
    {
        return $this->hasOne(LoadingT1::class, 'loading');
    }
}
