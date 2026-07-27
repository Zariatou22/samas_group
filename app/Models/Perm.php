<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Perm extends Model
{
    public $timestamps = false;

    protected $table = 'perms';

    protected $fillable = [
        'name',
        'definition',
    ];

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'perm_to_group', 'perm_id', 'group_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'perm_to_user', 'perm_id', 'user_id');
    }
}
