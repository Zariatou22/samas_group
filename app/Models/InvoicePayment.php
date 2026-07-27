<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePayment extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'invoice_payment';

    protected $fillable = [
        'user',
        'invoice',
        'reference',
        'amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }

    /**
     * Nommée différemment de la colonne DB "invoice" avec laquelle elle
     * collisionnerait sinon.
     */
    public function parentInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice');
    }
}
