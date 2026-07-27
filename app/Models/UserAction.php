<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Journal des connexions / actions de session (lecture seule) — utilisé par
 * la table legacy `useraction`.
 */
class UserAction extends Model
{
    public $timestamps = false;

    protected $table = 'useraction';

    protected $primaryKey = 'idsessinfo';

    protected $fillable = [
        'user',
        'action',
        'platform',
        'device',
        'browser',
        'ip',
    ];

    protected function casts(): array
    {
        return [
            'date_session' => 'datetime',
        ];
    }

    /**
     * Nommée différemment de la colonne DB "user" avec laquelle elle
     * collisionnerait sinon.
     */
    public function accountUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user');
    }
}
