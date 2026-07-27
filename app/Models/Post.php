<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;

/**
 * Reliquat du template de départ ("admin5") : un système d'articles/pages
 * complet existe en base (post, category, media, comments, product...) mais
 * n'a jamais servi pour SAMAS, à une exception près — cette table contient la
 * page statique des Conditions d'utilisation de l'application mobile,
 * exposée par la route /conditions. Seul ce cas d'usage a été reconstruit ;
 * le reste du CMS (articles, catégories, médiathèque, produits, commentaires)
 * a été volontairement laissé de côté faute de données réelles.
 */
class Post extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'post';

    protected $fillable = [
        'user',
        'title',
        'link',
        'type',
        'content',
        'online',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'online' => 'boolean',
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }
}
