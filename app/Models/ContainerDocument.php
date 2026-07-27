<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ContainerDocument extends Model
{
    public $timestamps = false;

    protected $table = 'container_docs';

    protected $fillable = [
        'user',
        'container',
        'name',
        'description',
        'filename',
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
            if (empty($model->created)) {
                $model->created = now();
            }
            $model->modified = now();
        });

        static::updating(fn (self $model) => $model->modified = now());

        static::deleted(function (self $model) {
            if ($model->filename) {
                Storage::disk('public')->delete('containers/'.$model->filename);
            }
        });
    }

    /**
     * Nommée différemment de la colonne DB "container" avec laquelle elle
     * collisionnerait sinon (Eloquent priorise toujours l'attribut brut).
     */
    public function parentContainer(): BelongsTo
    {
        return $this->belongsTo(Container::class, 'container');
    }
}
