<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingInvoiceFieldRegular;
use App\Models\AccountingInvoiceLabel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountingInvoiceFieldRegularController extends Controller
{
    public function index(): View
    {
        return view('admin.accounting-invoice-field-regulars.index', [
            'labels' => AccountingInvoiceLabel::query()->orderBy('name')->get(),
        ]);
    }

    public function data(): JsonResponse
    {
        $fields = AccountingInvoiceFieldRegular::with('accountingLabel')->orderBy('id')->get();

        $data = $fields->map(fn (AccountingInvoiceFieldRegular $f) => [
            'id' => $f->id,
            'label' => $f->label,
            'label_name' => $f->accountingLabel?->name,
            'unit_price' => $f->unit_price,
            'quantity' => $f->quantity,
            'amount' => $f->amount,
        ]);

        return response()->json(['data' => $data]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['nullable', 'exists:accounting_invoice_labels,id'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'numeric', 'min:1'],
        ]);

        $data['amount'] = $data['unit_price'] * $data['quantity'];

        AccountingInvoiceFieldRegular::create($data + ['user' => auth()->id()]);

        return back()->with('success', 'Champ récurrent enregistré.');
    }

    public function update(Request $request, AccountingInvoiceFieldRegular $accountingInvoiceFieldRegular): RedirectResponse
    {
        $data = $request->validate([
            'label' => ['nullable', 'exists:accounting_invoice_labels,id'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'numeric', 'min:1'],
        ]);

        $data['amount'] = $data['unit_price'] * $data['quantity'];

        $accountingInvoiceFieldRegular->update($data);

        return back()->with('success', 'Champ récurrent mis à jour.');
    }

    public function destroy(AccountingInvoiceFieldRegular $accountingInvoiceFieldRegular): RedirectResponse
    {
        $accountingInvoiceFieldRegular->delete();

        return back()->with('success', 'Champ récurrent supprimé.');
    }
}
