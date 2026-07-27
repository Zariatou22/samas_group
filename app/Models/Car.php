<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Car extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'cars';

    protected $fillable = [
        'user',
        'owner',
        'driver',
        'front_registration',
        'back_registration',
        'full_registration',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $model) {
            $model->full_registration = $model->front_registration.'/'.$model->back_registration;
        });
    }

    /**
     * Nommées différemment des colonnes DB ("owner", "driver") avec
     * lesquelles elles collisionneraient sinon.
     */
    public function carOwner(): BelongsTo
    {
        return $this->belongsTo(CarOwner::class, 'owner');
    }

    public function carDriver(): BelongsTo
    {
        return $this->belongsTo(CarDriver::class, 'driver');
    }
}
