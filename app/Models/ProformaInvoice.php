<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Facture Pro Forma envoyée au client avant la facture définitive
 * (App\Models\AccountingInvoice) : mêmes informations d'en-tête
 * (référence/client/date) mais pas de ligne de prix/quantité, remplacées
 * par des informations logistiques (conteneurs, marchandise).
 */
class ProformaInvoice extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'proforma_invoices';

    protected $fillable = [
        'user',
        'customer',
        'reference',
        'date_issued',
        'consignee_house',
        'container_type',
        'container_count',
        'goods_nature',
        'weight_value',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'container_count' => 'integer',
            'date_issued' => 'datetime',
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }

    /**
     * Nommée différemment de la colonne DB "customer" avec laquelle elle
     * collisionnerait sinon.
     */
    public function mandataire(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer');
    }

    /**
     * Prochaine référence au format NNNN/PROFORMA/SAMASGROUPE/ANNÉE,
     * numérotation réinitialisée chaque année civile (même logique que
     * App\Models\AccountingInvoice::nextReference()).
     */
    public static function nextReference(): string
    {
        $year = now()->year;

        $last = static::query()
            ->whereYear('date_issued', $year)
            ->orderByDesc('id')
            ->first();

        if (! $last) {
            return "0001/PROFORMA/SAMASGROUPE/{$year}";
        }

        $number = (int) explode('/', $last->reference)[0] + 1;

        return str_pad((string) $number, 4, '0', STR_PAD_LEFT)."/PROFORMA/SAMASGROUPE/{$year}";
    }
}
