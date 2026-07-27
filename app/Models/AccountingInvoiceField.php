<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ligne d'une facture client (comptable) : quantité × prix unitaire = montant.
 */
class AccountingInvoiceField extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'accounting_invoice_fields';

    protected $fillable = [
        'user',
        'invoice',
        'label',
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
     * Nommées différemment des colonnes DB ("invoice", "label") avec
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
}
