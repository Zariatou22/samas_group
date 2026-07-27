<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ligne "récurrente" réutilisable comme modèle par défaut lors de la création
 * d'une nouvelle facture client (pas liée à une facture précise).
 */
class AccountingInvoiceFieldRegular extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'accounting_invoice_fields_regular';

    protected $fillable = [
        'user',
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
     * Nommée différemment de la colonne DB "label" avec laquelle elle
     * collisionnerait sinon.
     */
    public function accountingLabel(): BelongsTo
    {
        return $this->belongsTo(AccountingInvoiceLabel::class, 'label');
    }
}
