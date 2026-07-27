<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * "Mandataire" dans le vocabulaire métier : l'agent/déclarant qui représente
 * une ou plusieurs sociétés clientes (customer_companies) pour le dédouanement.
 */
class Customer extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'customers';

    protected $fillable = [
        'user',
        'customer_name',
        'agent_name',
        'customer_contact',
        'agent_contact',
        'email',
        'address',
        'city',
        'country',
        'status',
    ];

    protected $attributes = [
        'city' => '',
        'country' => '',
    ];

    protected function casts(): array
    {
        return [
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }

    public function companies(): HasMany
    {
        return $this->hasMany(CustomerCompany::class, 'customer');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CustomerDocument::class, 'customer');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'customer');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user');
    }

    /**
     * Transfère une société cliente (et toutes les données qui y sont
     * rattachées : BL, conteneurs, factures, déclarations, chargements) vers
     * un autre mandataire, comme Customers::transfert_company_data() en CI.
     */
    public function transferCompanyTo(CustomerCompany $company, self $target): void
    {
        $columns = ['customer' => $target->id];
        $scope = fn ($query) => $query->where('customer', $this->id)->where('customer_company', $company->id);

        $scope(Bl::query())->update($columns);
        $scope(Container::query())->update($columns);
        $scope(Invoice::query())->update($columns);
        $scope(Authorization::query())->update($columns);
        $scope(Loading::query())->update($columns);
        $scope(LoadingContainer::query())->update($columns);

        $company->update(['customer' => $target->id]);
    }

    /**
     * Transfère le mandataire entier (toutes ses sociétés clientes et leurs
     * données) vers un autre mandataire, puis archive le mandataire d'origine,
     * comme Customers::transfert_customer_data() en CI (généralisé à toutes
     * les sociétés du mandataire plutôt qu'une seule).
     */
    public function transferAllTo(self $target): void
    {
        foreach ($this->companies as $company) {
            $this->transferCompanyTo($company, $target);
        }

        $this->documents()->update(['customer' => $target->id]);

        $this->delete();
    }
}
