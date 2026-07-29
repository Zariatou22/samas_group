<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ligne (désignation + prix total) d'un reçu d'avance transport.
 */
class InvoiceAdvanceLine extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'invoice_advance_lines';

    protected $fillable = [
        'user',
        'receipt',
        'designation',
        'quantity',
        'unit_price',
        'amount',
        'position',
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

    public function advanceReceipt(): BelongsTo
    {
        return $this->belongsTo(InvoiceAdvanceReceipt::class, 'receipt');
    }
}
