<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table clé/valeur générique pour les réglages globaux de l'application
 * (identité du site, SMTP, réseaux sociaux, maintenance...).
 */
class SystemVariable extends Model
{
    public $timestamps = false;

    protected $table = 'system_variables';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function put(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
