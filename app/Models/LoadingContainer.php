<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Rattache un conteneur précis (Container) à un chargement (Loading), avec la
 * part de colis/poids de ce conteneur couverte par ce chargement.
 */
class LoadingContainer extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'loading_containers';

    protected $fillable = [
        'user',
        'loading',
        'customer',
        'customer_company',
        'bl',
        'authorization',
        'container',
        'nb_package',
        'quantity',
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
     * Nommées différemment des colonnes DB avec lesquelles elles
     * collisionneraient sinon (Eloquent priorise toujours l'attribut brut).
     */
    public function parentLoading(): BelongsTo
    {
        return $this->belongsTo(Loading::class, 'loading');
    }

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

    public function parentContainer(): BelongsTo
    {
        return $this->belongsTo(Container::class, 'container');
    }
}
