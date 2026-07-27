<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Suivi du transfert d'un conteneur vers un terminal, pour les BL de type
 * "TRANSFERT MAD" (mise à disposition).
 */
class Transfert extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'transfert';

    protected $fillable = [
        'user',
        'bl',
        'container',
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
     * Nommées différemment des colonnes DB ("bl", "container") avec
     * lesquelles elles collisionneraient sinon.
     */
    public function parentBl(): BelongsTo
    {
        return $this->belongsTo(Bl::class, 'bl');
    }

    public function parentContainer(): BelongsTo
    {
        return $this->belongsTo(Container::class, 'container');
    }
}
