<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingInvoice;
use App\Models\AccountingInvoiceField;
use App\Models\AccountingInvoiceFieldRegular;
use App\Models\AccountingInvoiceLabel;
use App\Models\Bl;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountingInvoiceController extends Controller
{
    public function index(): View
    {
        return view('admin.accounting-invoices.index');
    }

    public function data(): JsonResponse
    {
        $invoices = AccountingInvoice::with('mandataire')->orderByDesc('date_issued')->get();

        $data = $invoices->map(fn (AccountingInvoice $i) => [
            'id' => $i->id,
            'reference' => $i->reference,
            'customer_name' => $i->mandataire?->customer_name,
            'date_issued' => optional($i->date_issued)->format('d/m/Y'),
            'amount' => $i->amount,
            'amount_ttc' => $i->amount_ttc,
        ]);

        return response()->json(['data' => $data]);
    }

    public function create(): View
    {
        return view('admin.accounting-invoices.create', [
            'customers' => Customer::query()->orderBy('customer_name')->get(),
            'labels' => AccountingInvoiceLabel::query()->orderBy('name')->get(),
            'nextReference' => AccountingInvoice::nextReference(),
            'regularFields' => AccountingInvoiceFieldRegular::query()->get(),
            'bls' => Bl::query()->orderByDesc('id')->get(['id', 'bl', 'customer']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:100', 'unique:accounting_invoices,reference'],
            'customer' => ['required', 'exists:customers,id'],
            'date_issued' => ['required', 'date'],
            'fees' => ['nullable', 'numeric', 'min:0'],
            'vat' => ['nullable', 'numeric', 'min:0'],
            'fields' => ['nullable', 'array'],
            'fields.*.bl' => ['nullable', 'exists:bl,id'],
            'fields.*.label' => ['nullable', 'exists:accounting_invoice_labels,id'],
            'fields.*.unit_price' => ['required', 'numeric', 'min:0'],
            'fields.*.quantity' => ['required', 'numeric', 'min:1'],
        ]);

        $fields = $data['fields'] ?? [];
        unset($data['fields']);

        $fees = (float) ($data['fees'] ?? 0);
        $vat = (float) ($data['vat'] ?? 0);
        $amount = collect($fields)->sum(fn ($f) => (float) $f['unit_price'] * (float) $f['quantity']);

        $invoice = AccountingInvoice::create($data + [
            'user' => auth()->id(),
            'fees' => $fees,
            'vat' => $vat,
            'amount' => $amount,
            'amount_ttc' => $amount - $fees + $vat,
        ]);

        $this->syncFields($invoice, $fields);

        return redirect()->route('admin.accounting-invoices.index')->with('success', 'Facture client enregistrée.');
    }

    public function edit(AccountingInvoice $accountingInvoice): View
    {
        return view('admin.accounting-invoices.edit', [
            'invoice' => $accountingInvoice->load('fields'),
            'customers' => Customer::query()->orderBy('customer_name')->get(),
            'labels' => AccountingInvoiceLabel::query()->orderBy('name')->get(),
            'bls' => Bl::query()->orderByDesc('id')->get(['id', 'bl', 'customer']),
        ]);
    }

    public function update(Request $request, AccountingInvoice $accountingInvoice): RedirectResponse
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:100', 'unique:accounting_invoices,reference,'.$accountingInvoice->id],
            'customer' => ['required', 'exists:customers,id'],
            'date_issued' => ['required', 'date'],
            'fees' => ['nullable', 'numeric', 'min:0'],
            'vat' => ['nullable', 'numeric', 'min:0'],
            'fields' => ['nullable', 'array'],
            'fields.*.bl' => ['nullable', 'exists:bl,id'],
            'fields.*.label' => ['nullable', 'exists:accounting_invoice_labels,id'],
            'fields.*.unit_price' => ['required', 'numeric', 'min:0'],
            'fields.*.quantity' => ['required', 'numeric', 'min:1'],
        ]);

        $fields = $data['fields'] ?? [];
        unset($data['fields']);

        $fees = (float) ($data['fees'] ?? 0);
        $vat = (float) ($data['vat'] ?? 0);
        $amount = collect($fields)->sum(fn ($f) => (float) $f['unit_price'] * (float) $f['quantity']);

        $accountingInvoice->update($data + [
            'fees' => $fees,
            'vat' => $vat,
            'amount' => $amount,
            'amount_ttc' => $amount - $fees + $vat,
        ]);

        $this->syncFields($accountingInvoice, $fields);

        return redirect()->route('admin.accounting-invoices.index')->with('success', 'Facture client mise à jour.');
    }

    public function destroy(AccountingInvoice $accountingInvoice): RedirectResponse
    {
        $accountingInvoice->delete();

        return back()->with('success', 'Facture client archivée.');
    }

    /**
     * Impression de la facture : regroupe les lignes par BL (sous-total par
     * BL affiché dans la colonne TOTAL de l'en-tête de groupe) quand la
     * facture couvre plusieurs BL, comme côté CodeIgniter
     * (Invoice::operation_print()).
     */
    public function print(AccountingInvoice $accountingInvoice): View
    {
        $accountingInvoice->load(['mandataire', 'fields.parentBl.customerCompany', 'fields.accountingLabel']);

        $blGroups = $accountingInvoice->fields
            ->groupBy(fn (AccountingInvoiceField $field) => $field->bl ?? 0)
            ->map(function ($fields) {
                $firstBl = $fields->first()->parentBl;

                return (object) [
                    'bl_name' => $firstBl?->bl,
                    'fields' => $fields,
                    'subtotal' => $fields->sum('amount'),
                ];
            })
            ->values();

        $firstCompanyName = $accountingInvoice->fields
            ->map(fn (AccountingInvoiceField $field) => $field->parentBl?->customerCompany?->name)
            ->filter()
            ->first();

        $formatter = new \NumberFormatter('fr', \NumberFormatter::SPELLOUT);

        $moisFr = [1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril', 5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août', 9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre'];
        $dateIssued = $accountingInvoice->date_issued;
        $dateFacture = $dateIssued ? $dateIssued->day.' '.$moisFr[$dateIssued->month].' '.$dateIssued->year : '';

        return view('admin.accounting-invoices.print', [
            'invoice' => $accountingInvoice,
            'bl_groups' => $blGroups,
            'customer_name' => $firstCompanyName ?: $accountingInvoice->mandataire?->customer_name,
            'mandataire_name' => $accountingInvoice->mandataire?->customer_name,
            'date_facture' => $dateFacture,
            'amount_words' => ucfirst(trim($formatter->format((int) $accountingInvoice->amount_ttc))),
        ]);
    }

    private function syncFields(AccountingInvoice $invoice, array $fields): void
    {
        $invoice->fields()->delete();

        foreach ($fields as $field) {
            AccountingInvoiceField::create([
                'user' => auth()->id(),
                'invoice' => $invoice->id,
                'bl' => $field['bl'] ?: null,
                'label' => $field['label'] ?: null,
                'unit_price' => $field['unit_price'],
                'quantity' => $field['quantity'],
                'amount' => (float) $field['unit_price'] * (float) $field['quantity'],
            ]);
        }
    }
}
