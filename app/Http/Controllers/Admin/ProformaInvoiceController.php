<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ProformaInvoice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProformaInvoiceController extends Controller
{
    public function index(): View
    {
        return view('admin.proforma-invoices.index');
    }

    public function data(): JsonResponse
    {
        $invoices = ProformaInvoice::with('mandataire')->orderByDesc('date_issued')->get();

        $data = $invoices->map(fn (ProformaInvoice $i) => [
            'id' => $i->id,
            'reference' => $i->reference,
            'customer_name' => $i->mandataire?->customer_name,
            'date_issued' => optional($i->date_issued)->format('d/m/Y'),
            'consignee_house' => $i->consignee_house,
            'container_type' => $i->container_type,
            'container_count' => $i->container_count,
        ]);

        return response()->json(['data' => $data]);
    }

    public function create(): View
    {
        return view('admin.proforma-invoices.create', [
            'customers' => Customer::query()->orderBy('customer_name')->get(),
            'nextReference' => ProformaInvoice::nextReference(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:100', 'unique:proforma_invoices,reference'],
            'customer' => ['required', 'exists:customers,id'],
            'date_issued' => ['required', 'date'],
            'consignee_house' => ['nullable', 'string', 'max:255'],
            'container_type' => ['nullable', 'string', 'max:255'],
            'container_count' => ['nullable', 'integer', 'min:0'],
            'goods_nature' => ['nullable', 'string', 'max:255'],
            'weight_value' => ['nullable', 'string', 'max:255'],
        ]);

        ProformaInvoice::create($data + [
            'user' => auth()->id(),
        ]);

        return redirect()->route('admin.proforma-invoices.index')->with('success', 'Facture pro forma enregistrée.');
    }

    public function edit(ProformaInvoice $proformaInvoice): View
    {
        return view('admin.proforma-invoices.edit', [
            'invoice' => $proformaInvoice,
            'customers' => Customer::query()->orderBy('customer_name')->get(),
        ]);
    }

    public function update(Request $request, ProformaInvoice $proformaInvoice): RedirectResponse
    {
        $data = $request->validate([
            'reference' => ['required', 'string', 'max:100', 'unique:proforma_invoices,reference,'.$proformaInvoice->id],
            'customer' => ['required', 'exists:customers,id'],
            'date_issued' => ['required', 'date'],
            'consignee_house' => ['nullable', 'string', 'max:255'],
            'container_type' => ['nullable', 'string', 'max:255'],
            'container_count' => ['nullable', 'integer', 'min:0'],
            'goods_nature' => ['nullable', 'string', 'max:255'],
            'weight_value' => ['nullable', 'string', 'max:255'],
        ]);

        $proformaInvoice->update($data);

        return redirect()->route('admin.proforma-invoices.index')->with('success', 'Facture pro forma mise à jour.');
    }

    public function destroy(ProformaInvoice $proformaInvoice): RedirectResponse
    {
        $proformaInvoice->delete();

        return back()->with('success', 'Facture pro forma archivée.');
    }
}
