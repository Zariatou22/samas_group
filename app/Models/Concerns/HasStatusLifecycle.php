<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * L'application legacy n'utilise jamais de vraies suppressions pour ces tables :
 * une ligne "supprimée" passe simplement à status=1 et reste en base (les autres
 * modules y font encore référence par clé étrangère applicative). On reproduit
 * ce comportement ici plutôt que d'utiliser les SoftDeletes standards de Laravel
 * (qui reposent sur une colonne deleted_at qui n'existe pas dans ce schéma).
 * Gère aussi le remplissage de `created` / `modified`, absents des timestamps
 * standards de Laravel mais présents sur toutes ces tables.
 */
trait HasStatusLifecycle
{
    protected static function bootHasStatusLifecycle(): void
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where($builder->getModel()->getTable().'.status', 0);
        });

        static::creating(function ($model) {
            if (is_null($model->status)) {
                $model->status = 0;
            }
            if (empty($model->created)) {
                $model->created = now();
            }
            $model->modified = now();
        });

        static::updating(function ($model) {
            $model->modified = now();
        });
    }

    public function delete(): bool
    {
        if ($this->fireModelEvent('deleting') === false) {
            return false;
        }

        $this->status = 1;
        $saved = $this->save();

        $this->fireModelEvent('deleted', false);

        return $saved;
    }

    public function restore(): bool
    {
        $this->status = 0;

        return $this->save();
    }

    public function scopeWithTrashed(Builder $query): Builder
    {
        return $query->withoutGlobalScope('active');
    }

    public function scopeOnlyTrashed(Builder $query): Builder
    {
        return $query->withoutGlobalScope('active')->where($this->getTable().'.status', 1);
    }
}
