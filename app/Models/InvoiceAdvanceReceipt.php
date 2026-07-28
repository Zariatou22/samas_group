<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Reçu d'avance transport remis à un chauffeur (document imprimable),
 * rattaché au libellé de facturation "AVANCE TRANSPORT" mais géré à part :
 * c'est une sortie d'argent vers un transporteur, pas une facture émise à
 * un client.
 */
class InvoiceAdvanceReceipt extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'invoice_advance_receipts';

    protected $fillable = [
        'user',
        'date_issued',
        'driver',
        'car',
        'bl',
        'contact_client',
        'contact_transitaire',
        'destination',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_issued' => 'date',
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }

    /**
     * "N° 0000062" façon carnet à souche, dérivé de l'id.
     */
    public function getReferenceAttribute(): string
    {
        return str_pad((string) $this->id, 7, '0', STR_PAD_LEFT);
    }

    public function carDriver(): BelongsTo
    {
        return $this->belongsTo(CarDriver::class, 'driver');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car');
    }

    public function parentBl(): BelongsTo
    {
        return $this->belongsTo(Bl::class, 'bl');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceAdvanceLine::class, 'receipt')->orderBy('position');
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->lines()->sum('amount');
    }
}
