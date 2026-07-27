<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Table clé/valeur par utilisateur (préférences, tokens d'auth mobile...).
 */
class UserVariable extends Model
{
    public $timestamps = false;

    protected $table = 'user_variables';

    protected $fillable = [
        'user_id',
        'key',
        'value',
    ];

    public static function get(int $userId, string $key, ?string $default = null): ?string
    {
        return static::where('user_id', $userId)->where('key', $key)->value('value') ?? $default;
    }

    public static function put(int $userId, string $key, ?string $value): void
    {
        static::updateOrCreate(['user_id' => $userId, 'key' => $key], ['value' => $value]);
    }
}
