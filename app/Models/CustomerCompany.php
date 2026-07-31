<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * "Client" dans le vocabulaire métier : une société rattachée à un mandataire
 * (Customer). C'est cette entité qui apparaît dans les BL, factures, etc.
 */
class CustomerCompany extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'customer_companies';

    protected $fillable = [
        'user',
        'customer',
        'name',
        'contact',
        'address',
        'rccm',
        'nif',
        'cni',
        'owner_name',
        'owner_contact',
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
     * Nommé "mandataire" et non "customer" : la colonne DB s'appelle "customer",
     * un nom de méthode identique masquerait l'accès à la valeur brute de la
     * colonne (Eloquent donne toujours priorité à l'attribut sur la relation
     * quand les deux portent le même nom).
     */
    public function mandataire(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user');
    }

    public function bls(): HasMany
    {
        return $this->hasMany(Bl::class, 'customer_company');
    }
}
