<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerDocumentController extends Controller
{
    public function store(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'file' => [
                'required', 'file', 'max:2048',
                'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
            ],
        ]);

        $filename = Storage::disk('public')->putFile('customers', $data['file']);

        $customer->documents()->create([
            'name' => $data['name'],
            'filename' => basename($filename),
            'user' => auth()->id(),
        ]);

        return back()->with('success', 'Document ajouté.');
    }

    /**
     * Corrige le nom/date d'un document existant sans le réuploader, comme
     * Customers::update_file() en CI.
     */
    public function update(Request $request, Customer $customer, CustomerDocument $document): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'created' => ['nullable', 'date'],
        ]);

        $document->update($data);

        return back()->with('success', 'Document mis à jour.');
    }

    public function destroy(Customer $customer, CustomerDocument $document): RedirectResponse
    {
        $document->delete();

        return back()->with('success', 'Document supprimé.');
    }
}
