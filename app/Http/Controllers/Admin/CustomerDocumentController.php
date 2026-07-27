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

    public function destroy(Customer $customer, CustomerDocument $document): RedirectResponse
    {
        $document->delete();

        return back()->with('success', 'Document supprimé.');
    }
}
