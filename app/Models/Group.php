<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    public $timestamps = false;

    protected $table = 'groups';

    protected $fillable = [
        'name',
        'definition',
    ];

    public function perms(): BelongsToMany
    {
        return $this->belongsToMany(Perm::class, 'perm_to_group', 'group_id', 'perm_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_to_group', 'group_id', 'user_id');
    }
}
