<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bl;
use App\Models\Customer;
use App\Models\CustomerCompany;
use App\Models\Invoice;
use App\Models\InvoiceLabel;
use App\Models\InvoicePayment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.invoices.index', ['customer' => $request->query('customer')]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = Invoice::with(['mandataire', 'parentBl', 'invoiceLabel', 'payment']);

        if ($customer = $request->query('customer')) {
            $query->where('customer', $customer);
        }

        $invoices = $query->orderByDesc('created')->get();

        $data = $invoices->map(fn (Invoice $i) => [
            'id' => $i->id,
            'reference' => $i->reference,
            'bl' => $i->parentBl?->bl,
            'bl_id' => $i->bl,
            'customer_name' => $i->mandataire?->customer_name,
            'label_name' => $i->invoiceLabel?->name,
            'amount' => $i->amount,
            'paid' => $i->paid,
        ]);

        return response()->json(['data' => $data]);
    }

    public function blInvoices(Bl $bl): View
    {
        return view('admin.invoices.bl', ['bl' => $bl->load('mandataire')]);
    }

    public function blData(Bl $bl): JsonResponse
    {
        $invoices = Invoice::where('bl', $bl->id)
            ->with(['customerCompany', 'invoiceLabel', 'payment'])
            ->orderByDesc('created')
            ->get();

        $data = $invoices->map(fn (Invoice $i) => [
            'id' => $i->id,
            'customer_company_name' => $i->customerCompany?->name,
            'label_name' => $i->invoiceLabel?->name,
            'reference' => $i->reference,
            'amount' => $i->amount,
            'created' => $i->created,
            'payment_reference' => $i->payment?->reference,
            'paid_amount' => $i->payment?->amount,
            'paid_date' => $i->payment?->modified,
        ]);

        return response()->json(['data' => $data]);
    }

    public function create(): View
    {
        return view('admin.invoices.create', [
            'customers' => Customer::query()->orderBy('customer_name')->get(),
            'bls' => Bl::query()->orderByDesc('created')->get(),
            'labels' => InvoiceLabel::query()->orderBy('name')->get(),
            'companiesByCustomer' => CustomerCompany::query()->get()
                ->groupBy('customer')
                ->map(fn ($companies) => $companies->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer' => ['required', 'exists:customers,id'],
            'customer_company' => ['nullable', 'exists:customer_companies,id'],
            'bl' => ['required', 'exists:bl,id'],
            'label' => ['required', 'exists:invoice_labels,id'],
            'reference' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric'],
            'paid' => ['nullable', 'boolean'],
            'paid_amount' => ['nullable', 'numeric'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
        ]);

        $paid = (bool) ($data['paid'] ?? false);
        $paidAmount = $data['paid_amount'] ?? null;
        $paymentReference = $data['payment_reference'] ?? null;
        unset($data['paid'], $data['paid_amount'], $data['payment_reference']);
        $data['customer_company'] = $data['customer_company'] ?? 0;

        // Dédoublonnage (mandataire, BL, référence), comme Invoices::check_invoice() en CI.
        $exists = Invoice::query()
            ->where('customer', $data['customer'])
            ->where('bl', $data['bl'])
            ->where('reference', $data['reference'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['reference' => 'Une facture avec ce mandataire, ce BL et cette référence existe déjà.']);
        }

        $invoice = Invoice::create($data + ['user' => auth()->id(), 'paid' => $paid]);

        if ($paid) {
            InvoicePayment::create([
                'user' => auth()->id(),
                'invoice' => $invoice->id,
                'reference' => $paymentReference ?? '',
                'amount' => $paidAmount ?? $invoice->amount,
            ]);
        }

        return redirect()->route('admin.invoices.index')->with('success', 'Facture enregistrée.');
    }

    public function edit(Invoice $invoice): View
    {
        return view('admin.invoices.edit', [
            'invoice' => $invoice->load(['payment', 'mandataire']),
            'customers' => Customer::query()->orderBy('customer_name')->get(),
            'bls' => Bl::query()->orderByDesc('created')->get(),
            'labels' => InvoiceLabel::query()->orderBy('name')->get(),
            'companies' => CustomerCompany::query()->where('customer', $invoice->customer)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $data = $request->validate([
            'customer_company' => ['nullable', 'exists:customer_companies,id'],
            'label' => ['required', 'exists:invoice_labels,id'],
            'reference' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'numeric'],
            'paid' => ['nullable', 'boolean'],
            'paid_amount' => ['nullable', 'numeric'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
        ]);

        $paid = (bool) ($data['paid'] ?? false);
        $paidAmount = $data['paid_amount'] ?? null;
        $paymentReference = $data['payment_reference'] ?? null;
        unset($data['paid'], $data['paid_amount'], $data['payment_reference']);

        $exists = Invoice::query()
            ->where('customer', $invoice->customer)
            ->where('bl', $invoice->bl)
            ->where('reference', $data['reference'])
            ->where('id', '!=', $invoice->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['reference' => 'Une facture avec ce mandataire, ce BL et cette référence existe déjà.']);
        }

        $invoice->update($data + ['paid' => $paid]);

        if ($paid) {
            InvoicePayment::updateOrCreate(
                ['invoice' => $invoice->id],
                [
                    'user' => auth()->id(),
                    'reference' => $paymentReference ?? '',
                    'amount' => $paidAmount ?? $invoice->amount,
                ],
            );
        } else {
            $invoice->payment?->delete();
        }

        return redirect()->route('admin.invoices.index')->with('success', 'Facture mise à jour.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        // La cascade sur le paiement associé évite de fausser les soldes
        // mandataires (MandataireBalanceController), comme
        // Invoices::delete_invoice() en CI.
        $invoice->payment?->delete();
        $invoice->delete();

        return back()->with('success', 'Facture archivée.');
    }
}
