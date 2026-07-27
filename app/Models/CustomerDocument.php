<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CustomerDocument extends Model
{
    public $timestamps = false;

    protected $table = 'customer_docs';

    protected $fillable = [
        'user',
        'customer',
        'name',
        'filename',
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
        static::creating(function (self $model) {
            if (is_null($model->status)) {
                $model->status = 0;
            }
            if (empty($model->created)) {
                $model->created = now();
            }
            $model->modified = now();
        });

        static::updating(fn (self $model) => $model->modified = now());

        static::deleted(function (self $model) {
            Storage::disk('public')->delete('customers/'.$model->filename);
        });
    }

    /**
     * Nommé "mandataire" (pas "customer") pour ne pas entrer en collision avec
     * la colonne DB "customer" — cf. commentaire équivalent sur CustomerCompany.
     */
    public function mandataire(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer');
    }
}
