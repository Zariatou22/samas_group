<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;

class InvoiceLabel extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'invoice_labels';

    protected $fillable = [
        'user',
        'name',
        'description',
        'status',
    ];

    protected $attributes = [
        'description' => '',
    ];

    protected function casts(): array
    {
        return [
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }
}
