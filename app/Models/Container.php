<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Container extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'containers';

    protected $fillable = [
        'user',
        'bl',
        'customer',
        'customer_company',
        'type_tc',
        'numero',
        'lead_number',
        'ship',
        'eta',
        'product_type',
        'nb_package',
        'quantity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'eta' => 'date',
            'quantity' => 'float',
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }

    /**
     * Nommées différemment des colonnes DB ("bl", "customer") avec lesquelles
     * elles collisionneraient sinon (Eloquent priorise toujours l'attribut brut).
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

    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ContainerDocument::class, 'container');
    }

    public function loadingLinks(): HasMany
    {
        return $this->hasMany(LoadingContainer::class, 'container');
    }
}
