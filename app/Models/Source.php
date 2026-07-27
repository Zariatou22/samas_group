<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;

/**
 * Lieu d'enlèvement de la marchandise, utilisé par les déclarations d'autorisation.
 */
class Source extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'sources';

    protected $fillable = [
        'user',
        'name',
        'code',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }
}
