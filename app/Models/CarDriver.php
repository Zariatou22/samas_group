<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarDriver extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'car_drivers';

    protected $fillable = [
        'user',
        'owner',
        'name',
        'contact',
        'address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }

    /**
     * Nommée différemment de la colonne DB "owner" avec laquelle elle
     * collisionnerait sinon (Eloquent priorise toujours l'attribut brut).
     */
    public function carOwner(): BelongsTo
    {
        return $this->belongsTo(CarOwner::class, 'owner');
    }
}
