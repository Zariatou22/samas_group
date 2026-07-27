<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingInvoiceLabel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountingInvoiceLabelController extends Controller
{
    public function index(): View
    {
        return view('admin.accounting-invoice-labels.index');
    }

    public function data(): JsonResponse
    {
        return response()->json(['data' => AccountingInvoiceLabel::query()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        AccountingInvoiceLabel::create($data + ['user' => auth()->id()]);

        return back()->with('success', 'Libellé enregistré.');
    }

    public function update(Request $request, AccountingInvoiceLabel $accountingInvoiceLabel): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ]);

        $accountingInvoiceLabel->update($data);

        return back()->with('success', 'Libellé mis à jour.');
    }

    public function destroy(AccountingInvoiceLabel $accountingInvoiceLabel): RedirectResponse
    {
        $accountingInvoiceLabel->delete();

        return back()->with('success', 'Libellé archivé.');
    }
}
