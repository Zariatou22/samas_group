<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;

/**
 * Libellé réutilisable pour les factures clients (comptables) — distinct de
 * `InvoiceLabel` qui sert aux factures prestataires liées à un BL.
 */
class AccountingInvoiceLabel extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'accounting_invoice_labels';

    protected $fillable = [
        'user',
        'name',
        'unit_price',
        'description',
        'status',
    ];

    protected $attributes = [
        'description' => '',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'float',
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }
}
