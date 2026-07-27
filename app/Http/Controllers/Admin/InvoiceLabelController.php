<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvoiceLabel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InvoiceLabelController extends Controller
{
    public function index(): View
    {
        return view('admin.invoice-labels.index');
    }

    public function data(): JsonResponse
    {
        return response()->json(['data' => InvoiceLabel::query()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        InvoiceLabel::create($data + ['user' => auth()->id()]);

        return back()->with('success', 'Libellé enregistré.');
    }

    public function update(Request $request, InvoiceLabel $invoiceLabel): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $invoiceLabel->update($data);

        return back()->with('success', 'Libellé mis à jour.');
    }

    public function destroy(InvoiceLabel $invoiceLabel): RedirectResponse
    {
        $invoiceLabel->delete();

        return back()->with('success', 'Libellé archivé.');
    }
}
