<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;

/**
 * Compagnie de transport (armateur / ligne maritime) référencée sur les BL.
 */
class Company extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'companies';

    protected $fillable = [
        'user',
        'name',
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
}
