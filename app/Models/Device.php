<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Appareil mobile enregistré à la connexion (pour les notifications push,
 * jamais mis en service dans l'ancienne appli — table restée vide).
 */
class Device extends Model
{
    public $timestamps = false;

    protected $table = 'device';

    protected $fillable = [
        'user',
        'device',
        'type',
        'uuid',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->created)) {
                $model->created = now();
            }
            $model->modified = now();
        });

        static::updating(fn (self $model) => $model->modified = now());
    }
}
