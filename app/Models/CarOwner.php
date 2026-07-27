<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Transporteur (propriétaire de véhicule(s)).
 */
class CarOwner extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'car_owners';

    protected $fillable = [
        'user',
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

    public function drivers(): HasMany
    {
        return $this->hasMany(CarDriver::class, 'owner');
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class, 'owner');
    }
}
