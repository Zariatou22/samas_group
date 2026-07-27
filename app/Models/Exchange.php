<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Suivi de la réception de l'échange (jeu de documents) d'un BL — une des
 * étapes qui fait passer un BL de "en attente d'arrivée" à "en attente
 * d'opération" (cf. App\Models\Bl::scopeArrived()).
 */
class Exchange extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'exchange';

    protected $fillable = [
        'user',
        'bl',
        'date_received',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_received' => 'date',
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
