<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Facture émise par SAMAS à un client (comptabilité clients) — distincte des
 * factures prestataires liées à un BL (voir App\Models\Invoice). `fees` porte
 * le libellé "Réduction" côté formulaire ; `amount` est la somme des lignes
 * (App\Models\AccountingInvoiceField), `amount_ttc` = amount - fees + vat.
 */
class AccountingInvoice extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'accounting_invoices';

    protected $fillable = [
        'user',
        'customer',
        'reference',
        'amount',
        'fees',
        'vat',
        'amount_ttc',
        'filename',
        'date_issued',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'fees' => 'float',
            'vat' => 'float',
            'amount_ttc' => 'float',
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

    public function fields(): HasMany
    {
        return $this->hasMany(AccountingInvoiceField::class, 'invoice');
    }

    /**
     * Prochaine référence au format NNNN/FACTURE/SAMASGROUPE/ANNÉE, reprenant
     * exactement la logique de l'ancienne appli
     * (Invoices::get_last_invoice_reference()) : numérotation réinitialisée
     * chaque année civile.
     */
    public static function nextReference(): string
    {
        $year = now()->year;

        $last = static::query()
            ->whereYear('date_issued', $year)
            ->orderByDesc('id')
            ->first();

        if (! $last) {
            return "0001/FACTURE/SAMASGROUPE/{$year}";
        }

        $number = (int) explode('/', $last->reference)[0] + 1;

        return str_pad((string) $number, 4, '0', STR_PAD_LEFT)."/FACTURE/SAMASGROUPE/{$year}";
    }
}
