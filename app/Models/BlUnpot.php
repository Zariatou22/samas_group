<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Dépotage d'un conteneur d'un BL de type DEPOTAGE : marque un conteneur
 * comme dépoté à une date donnée. Équivalent de la table bl_unpot / de la
 * library Unpot en CI.
 */
class BlUnpot extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'bl_unpot';

    protected $fillable = [
        'user',
        'customer',
        'bl',
        'container',
        'date_unpot',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_unpot' => 'date',
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }

    public function parentBl(): BelongsTo
    {
        return $this->belongsTo(Bl::class, 'bl');
    }

    public function parentContainer(): BelongsTo
    {
        return $this->belongsTo(Container::class, 'container');
    }
}
