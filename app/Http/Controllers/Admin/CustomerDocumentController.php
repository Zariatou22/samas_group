<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerCompany;
use App\Models\CustomerDocument;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerDocumentController extends Controller
{
    /**
     * Liste des documents du mandataire, filtrable par client et/ou type
     * (utilisé par les liens N° RCCM / N° NIF-IFU / N° CNI-Passeport de la
     * fiche client, comme customer/documents en CI).
     */
    public function index(Request $request, Customer $customer): View
    {
        $companyId = $request->integer('company') ?: null;
        $type = $request->string('type')->toString() ?: null;

        $documents = $customer->documents()
            ->with('clientCompany')
            ->when($companyId, fn ($q) => $q->where('company', $companyId))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->orderByDesc('id')
            ->get();

        return view('admin.customers.documents', [
            'customer' => $customer,
            'documents' => $documents,
            'types' => CustomerDocument::types(),
            'type' => $type,
            'companyId' => $companyId,
            'companyObj' => $companyId ? CustomerCompany::find($companyId) : null,
        ]);
    }

    public function store(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'integer', 'exists:customer_companies,id'],
            'file' => [
                'required', 'file', 'max:2048',
                'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
            ],
        ]);

        $filename = Storage::disk('public')->putFile('customers', $data['file']);

        $customer->documents()->create([
            'name' => $data['name'],
            'type' => $data['type'] ?? null,
            'company' => $data['company'] ?? null,
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
            'type' => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'integer', 'exists:customer_companies,id'],
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
