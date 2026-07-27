<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * "Mandataire" dans le vocabulaire métier : l'agent/déclarant qui représente
 * une ou plusieurs sociétés clientes (customer_companies) pour le dédouanement.
 */
class Customer extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'customers';

    protected $fillable = [
        'user',
        'customer_name',
        'agent_name',
        'customer_contact',
        'agent_contact',
        'email',
        'address',
        'city',
        'country',
        'status',
    ];

    protected $attributes = [
        'city' => '',
        'country' => '',
    ];

    protected function casts(): array
    {
        return [
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }

    public function companies(): HasMany
    {
        return $this->hasMany(CustomerCompany::class, 'customer');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CustomerDocument::class, 'customer');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'customer');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user');
    }
}
