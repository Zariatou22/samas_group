<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;

/**
 * Type de marchandise transportée (référencé sur les BL).
 */
class ProductType extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'product_type';

    protected $fillable = [
        'user',
        'name',
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
