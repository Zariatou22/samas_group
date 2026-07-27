<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceLabel;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function getInvoices()
    {
        return response()->json(['data' => Invoice::with(['mandataire', 'customerCompany', 'parentBl', 'invoiceLabel', 'payment'])->get()]);
    }

    public function getInvoiceCustomers()
    {
        $customers = Customer::query()
            ->whereHas('invoices')
            ->addSelect([
                'invoiced_total' => Invoice::query()->selectRaw('COALESCE(SUM(amount), 0)')->whereColumn('customer', 'customers.id'),
            ])
            ->get();

        return response()->json(['data' => $customers]);
    }

    public function getCustomerInvoices(Request $request)
    {
        $customerId = $request->integer('customer');

        $invoices = Invoice::where('customer', $customerId)
            ->with(['parentBl', 'invoiceLabel', 'payment'])
            ->get();

        return response()->json(['data' => $invoices]);
    }

    public function getBlInvoices(Request $request)
    {
        $blId = $request->integer('bl_id');

        $invoices = Invoice::where('bl', $blId)
            ->with(['mandataire', 'customerCompany', 'invoiceLabel', 'payment'])
            ->get();

        return response()->json(['data' => $invoices]);
    }

    public function getLabels()
    {
        return response()->json(['items' => InvoiceLabel::all()]);
    }

    public function setInvoiceLabel(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit_price' => ['nullable', 'numeric'],
        ]);

        $label = InvoiceLabel::create([
            'user' => $request->user()->id,
            'name' => $data['name'],
            'description' => '',
        ]);

        return response()->json(['error' => false, 'data' => $label]);
    }
}
