<?php

namespace App\Models;

use App\Models\Concerns\HasStatusLifecycle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    use HasStatusLifecycle;

    public $timestamps = false;

    protected $table = 'invoices';

    protected $fillable = [
        'user',
        'customer',
        'customer_company',
        'bl',
        'label',
        'reference',
        'amount',
        'paid',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'paid' => 'boolean',
            'created' => 'datetime',
            'modified' => 'datetime',
        ];
    }

    /**
     * Nommées différemment des colonnes DB ("customer", "bl", "label") avec
     * lesquelles elles collisionneraient sinon.
     */
    public function mandataire(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer');
    }

    public function customerCompany(): BelongsTo
    {
        return $this->belongsTo(CustomerCompany::class, 'customer_company');
    }

    public function parentBl(): BelongsTo
    {
        return $this->belongsTo(Bl::class, 'bl');
    }

    public function invoiceLabel(): BelongsTo
    {
        return $this->belongsTo(InvoiceLabel::class, 'label');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(InvoicePayment::class, 'invoice');
    }
}
