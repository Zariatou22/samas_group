<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Document de transit douanier (T1) associé à un chargement. `validate` est
 * la date de validation effective (null tant que non validé), `valid_until`
 * la date limite de validité.
 */
class LoadingT1 extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'loading_t1';

    protected $fillable = [
        'user',
        'loading',
        't1_number',
        'validate',
        'valid_until',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'validate' => 'datetime',
            'valid_until' => 'datetime',
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }

    /**
     * Nommée différemment de la colonne DB "loading" avec laquelle elle
     * collisionnerait sinon.
     */
    public function parentLoading(): BelongsTo
    {
        return $this->belongsTo(Loading::class, 'loading');
    }

    public function isValidated(): bool
    {
        return ! is_null($this->validate);
    }

    public function scopeOngoing(Builder $query): Builder
    {
        return $query->whereDate('valid_until', '>=', now()->toDateString());
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereDate('valid_until', '<', now()->toDateString());
    }
}
