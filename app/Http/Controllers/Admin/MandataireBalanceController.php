<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MandataireBalanceController extends Controller
{
    public function index(): View
    {
        // Filtres Mandataire / Client, comme sur la page "Tous les B/L"
        $customers = Customer::query()->whereHas('invoices')->orderBy('customer_name')->get();

        return view('admin.mandataire-balances.index', ['customers' => $customers]);
    }

    public function data(Request $request): JsonResponse
    {
        $customerId = $request->integer('customer') ?: null;
        $customerCompanyId = $request->integer('customer_company') ?: null;

        $customers = Customer::query()
            ->whereHas('invoices', function ($query) use ($customerCompanyId) {
                if ($customerCompanyId) {
                    $query->where('customer_company', $customerCompanyId);
                }
            })
            ->when($customerId, fn ($query) => $query->where('id', $customerId))
            ->addSelect([
                'invoiced_total' => Invoice::query()
                    ->selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('customer', 'customers.id')
                    ->when($customerCompanyId, fn ($query) => $query->where('customer_company', $customerCompanyId)),
                'paid_total' => InvoicePayment::query()
                    ->join('invoices', 'invoices.id', '=', 'invoice_payment.invoice')
                    ->selectRaw('COALESCE(SUM(invoice_payment.amount), 0)')
                    ->whereColumn('invoices.customer', 'customers.id')
                    ->when($customerCompanyId, fn ($query) => $query->where('invoices.customer_company', $customerCompanyId)),
            ])
            ->orderBy('customer_name')
            ->get();

        $data = $customers->map(fn (Customer $c) => [
            'id' => $c->id,
            'customer_name' => $c->customer_name,
            'invoiced_total' => (float) $c->invoiced_total,
            'paid_total' => (float) $c->paid_total,
            'remaining' => (float) $c->invoiced_total - (float) $c->paid_total,
        ]);

        return response()->json(['data' => $data]);
    }
}
