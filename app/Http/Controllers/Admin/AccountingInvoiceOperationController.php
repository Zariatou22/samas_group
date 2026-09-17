<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingInvoice;
use App\Models\AccountingInvoiceField;
use App\Models\Bl;
use App\Models\Customer;
use App\Models\InvoiceLabel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Pipeline "opérations non facturées -> Facturé -> facture" côté factures
 * clients (comptables), repris de Invoice::operation_add()/operations_unbilled()
 * /mark_operations_invoiced() côté CodeIgniter. Une "opération" est une ligne
 * AccountingInvoiceField sans facture (`invoice` NULL) rattachée directement
 * à un `customer` + `bl` ; le bouton "Facturé" la regroupe avec d'autres
 * opérations du même client sous une AccountingInvoice (référence commune).
 */
class AccountingInvoiceOperationController extends Controller
{
    /**
     * Rendu serveur direct (comme Invoice::operations_unbilled() côté
     * CodeIgniter), pas une DataTable : la sélection multiple pour la
     * facturation groupée a besoin de toutes les lignes déjà en mémoire côté
     * client.
     */
    public function unbilled(Request $request): View
    {
        $rm = array_values(array_filter(array_map('intval', explode(',', (string) $request->query('rm', '')))));
        if (! empty($rm)) {
            AccountingInvoiceField::query()->whereIn('id', $rm)->get()->each->delete();
        }

        $groups = AccountingInvoiceField::query()
            ->unbilled()
            ->with(['parentBl', 'mandataire'])
            ->orderBy('bl')
            ->orderBy('id')
            ->get()
            ->groupBy('bl')
            ->map(function ($fields) {
                $first = $fields->first();

                return (object) [
                    'bl' => $first->bl,
                    'bl_name' => $first->parentBl?->bl,
                    'customer_name' => $first->mandataire?->customer_name,
                    'customer' => $first->customer,
                    'amount' => $fields->sum('amount'),
                    'ids' => $fields->pluck('id')->values()->all(),
                    'fields' => $fields->values(),
                ];
            })
            ->values();

        $customersWithUnbilledBls = Customer::query()
            ->whereIn('id', $this->eligibleBlsQuery()->pluck('customer')->unique())
            ->orderBy('customer_name')
            ->get();

        return view('admin.accounting-invoices.operations-unbilled', [
            'title' => 'Opérations non facturées',
            'operations' => $groups,
            'customers' => $customersWithUnbilledBls,
        ]);
    }

    /**
     * BL du client donné n'ayant encore aucune opération (facturée ou non) —
     * reprend Invoices::list_customer_unbilled_bls(), pour la section
     * "Choisir un client" de la page et le préremplissage de operation-add.
     */
    public function customerEligibleBls(Customer $customer): JsonResponse
    {
        $bls = $this->eligibleBlsQuery()->where('customer', $customer->id)->get(['id', 'bl', 'description']);

        return response()->json(['data' => $bls]);
    }

    private function eligibleBlsQuery()
    {
        $usedBlIds = AccountingInvoiceField::query()->whereNotNull('bl')->distinct()->pluck('bl');

        return Bl::query()->whereNotIn('id', $usedBlIds);
    }

    /**
     * BL éligibles pour une nouvelle opération : appartiennent au client
     * choisi et n'ont encore aucune opération (facturée ou non) associée —
     * reprend Invoices::list_customer_unbilled_bls().
     */
    public function create(Request $request): View
    {
        $eligibleBls = $this->eligibleBlsQuery()->orderByDesc('id')->get(['id', 'bl', 'customer', 'description']);

        $customers = Customer::query()
            ->whereIn('id', $eligibleBls->pluck('customer')->unique())
            ->orderBy('customer_name')
            ->get();

        return view('admin.accounting-invoices.operation-add', [
            'title' => 'Nouvelle opération',
            'customers' => $customers,
            'bls' => $eligibleBls,
            'labels' => InvoiceLabel::query()->orderBy('name')->get(),
            'prefillCustomer' => (int) $request->query('customer'),
            'prefillBls' => array_values(array_filter(array_map('intval', (array) $request->query('bl', [])))),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer' => ['required', 'exists:customers,id'],
            'bl' => ['required', 'array', 'min:1'],
            'bl.*' => ['exists:bl,id'],
            'designation_id' => ['nullable', 'array'],
            'designation_id.*' => ['nullable', 'exists:invoice_labels,id'],
            'quantity' => ['nullable', 'array'],
            'unit_price' => ['nullable', 'array'],
        ]);

        $labels = InvoiceLabel::query()->whereIn('id', array_filter($data['designation_id'] ?? []))->get()->keyBy('id');
        $quantities = $data['quantity'] ?? [];
        $unitPrices = $data['unit_price'] ?? [];
        $created = 0;

        foreach ($data['bl'] as $blId) {
            foreach (($data['designation_id'] ?? []) as $key => $labelId) {
                $label = $labels->get($labelId);
                if (! $label) {
                    continue;
                }
                $quantity = (float) ($quantities[$key] ?? 1);
                $unitPrice = (float) ($unitPrices[$key] ?? 0);

                AccountingInvoiceField::create([
                    'user' => auth()->id(),
                    'invoice' => null,
                    'bl' => $blId,
                    'customer' => $data['customer'],
                    'label' => null,
                    'designation' => $label->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'amount' => $quantity * $unitPrice,
                ]);
                $created++;
            }
        }

        if ($created === 0) {
            return back()->withInput()->with('error', 'Veuillez sélectionner au moins un BL et une désignation.');
        }

        return redirect()->route('admin.accounting-invoices.operations.unbilled')
            ->with('success', $created > 1 ? "{$created} opérations enregistrées" : 'Opération enregistrée');
    }

    public function edit(AccountingInvoiceField $accountingInvoiceField): View
    {
        abort_if($accountingInvoiceField->invoice !== null, 404);

        return view('admin.accounting-invoices.operation-edit', [
            'operation' => $accountingInvoiceField->load('parentBl'),
        ]);
    }

    public function update(Request $request, AccountingInvoiceField $accountingInvoiceField): RedirectResponse
    {
        abort_if($accountingInvoiceField->invoice !== null, 404);

        $data = $request->validate([
            'designation' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $accountingInvoiceField->update([
            'designation' => $data['designation'],
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'amount' => (float) $data['quantity'] * (float) $data['unit_price'],
        ]);

        return redirect()->route('admin.accounting-invoices.operations.unbilled')->with('success', 'Opération mise à jour.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $ids = array_values(array_filter(array_map('intval', (array) $request->input('ids', []))));

        AccountingInvoiceField::query()->whereIn('id', $ids)->get()->each->delete();

        return back()->with('success', count($ids) > 1 ? 'Opérations supprimées' : 'Opération supprimée');
    }

    public function nextReference(): JsonResponse
    {
        return response()->json(['next_reference' => AccountingInvoice::nextReference()]);
    }

    /**
     * Désignations non facturées d'un BL donné, pour la boîte de dialogue
     * "Facturé" — reprend Invoices::list_bl_unbilled_operations().
     */
    public function blUnbilled(Bl $bl): JsonResponse
    {
        $fields = AccountingInvoiceField::query()
            ->unbilled()
            ->where('bl', $bl->id)
            ->orderBy('id')
            ->get(['id', 'designation', 'label', 'amount']);

        return response()->json([
            'data' => $fields->map(fn (AccountingInvoiceField $f) => [
                'id' => $f->id,
                'designation' => $f->display_designation,
                'amount' => $f->amount,
            ]),
            'next_reference' => AccountingInvoice::nextReference(),
        ]);
    }

    /**
     * Regroupe les opérations sélectionnées sous une référence de facture
     * commune : crée (ou réutilise) l'en-tête AccountingInvoice et y
     * rattache les lignes — reprend Invoices::mark_operations_invoiced().
     */
    public function markInvoiced(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
            'reference' => ['required', 'string', 'max:100'],
        ]);

        $fields = AccountingInvoiceField::query()->unbilled()->whereIn('id', $data['ids'])->get();

        if ($fields->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Aucune opération valide sélectionnée.']);
        }

        if ($fields->pluck('customer')->unique()->count() > 1) {
            return response()->json(['success' => false, 'message' => 'Les opérations sélectionnées doivent appartenir au même client.']);
        }

        $customerId = $fields->first()->customer;
        $amount = $fields->sum('amount');

        $invoice = AccountingInvoice::query()->firstOrNew(['reference' => $data['reference']]);
        $isNew = ! $invoice->exists;

        $invoice->fill([
            'user' => $invoice->user ?? auth()->id(),
            'customer' => $invoice->exists ? $invoice->customer : $customerId,
            'date_issued' => $invoice->date_issued ?? now(),
            'fees' => $invoice->fees ?? 0,
            'vat' => $invoice->vat ?? 0,
        ]);

        if (! $isNew && (int) $invoice->customer !== (int) $customerId) {
            return response()->json(['success' => false, 'message' => 'Cette référence de facture est déjà utilisée pour un autre client.']);
        }

        $invoice->amount = ($isNew ? 0 : (float) $invoice->amount) + $amount;
        $invoice->amount_ttc = $invoice->amount - $invoice->fees + $invoice->vat;
        $invoice->save();

        AccountingInvoiceField::query()->whereIn('id', $fields->pluck('id'))->update(['invoice' => $invoice->id]);

        return response()->json(['success' => true, 'invoice_id' => $invoice->id]);
    }
}
