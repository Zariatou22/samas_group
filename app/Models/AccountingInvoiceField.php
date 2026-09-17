<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ligne d'une facture client (comptable) : quantité × prix unitaire = montant.
 *
 * Peut exister avant d'être rattachée à une facture (`invoice` NULL) : c'est
 * une "opération" en attente de facturation, rattachée directement à un
 * `customer` (mandataire) et un `bl`, en attendant d'être regroupée sous une
 * référence de facture via le bouton "Facturé" (voir
 * AccountingInvoiceOperationController::markInvoiced()).
 */
class AccountingInvoiceField extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'accounting_invoice_fields';

    protected $fillable = [
        'user',
        'invoice',
        'bl',
        'customer',
        'label',
        'designation',
        'quantity',
        'unit_price',
        'amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'float',
            'unit_price' => 'float',
            'amount' => 'float',
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }

    /**
     * Nommées différemment des colonnes DB ("invoice", "label", "bl") avec
     * lesquelles elles collisionneraient sinon.
     */
    public function accountingInvoice(): BelongsTo
    {
        return $this->belongsTo(AccountingInvoice::class, 'invoice');
    }

    public function accountingLabel(): BelongsTo
    {
        return $this->belongsTo(AccountingInvoiceLabel::class, 'label');
    }

    public function parentBl(): BelongsTo
    {
        return $this->belongsTo(Bl::class, 'bl');
    }

    /**
     * Nommée différemment de la colonne DB "customer" avec laquelle elle
     * collisionnerait sinon. Renseignée sur les opérations en attente de
     * facturation ; une fois facturée, le mandataire se retrouve de toute
     * façon via accountingInvoice->mandataire.
     */
    public function mandataire(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer');
    }

    public function scopeUnbilled(Builder $query): Builder
    {
        return $query->whereNull('invoice');
    }

    public function scopeBilled(Builder $query): Builder
    {
        return $query->whereNotNull('invoice');
    }

    /**
     * Texte affiché : `designation` (copié depuis `invoice_labels` au moment
     * de la création par le pipeline d'opérations) prime sur le libellé lié
     * (`accounting_invoice_labels`, utilisé par le formulaire de facture
     * directe) quand les deux sont possibles.
     */
    public function getDisplayDesignationAttribute(): ?string
    {
        return $this->designation ?: $this->accountingLabel?->name;
    }
}
