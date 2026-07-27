<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * "BAD" (Bon À Délivrer) dans la table legacy `bad` : le document de la
 * compagnie de transport qui autorise la livraison de la marchandise d'un
 * BL, avec sa date de réception et sa date de validité.
 */
class DeliveryNote extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'bad';

    protected $fillable = [
        'user',
        'bl',
        'date_valid',
        'date_received',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_valid' => 'date',
            'date_received' => 'datetime',
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }

    /**
     * Nommée différemment de la colonne DB "bl" avec laquelle elle
     * collisionnerait sinon.
     */
    public function parentBl(): BelongsTo
    {
        return $this->belongsTo(Bl::class, 'bl');
    }
}
