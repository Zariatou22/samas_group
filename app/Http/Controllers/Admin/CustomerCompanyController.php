<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerCompany;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerCompanyController extends Controller
{
    /**
     * Liste des clients d'un mandataire (bouton "Voir" de la colonne CLIENTS
     * dans la liste des mandataires), comme customer/companies en CI.
     */
    public function index(Customer $customer): View
    {
        return view('admin.customers.companies', [
            'customer' => $customer,
            'companies' => $customer->companies,
        ]);
    }

    /**
     * Fiche client : les documents RCCM/NIF/CNI déjà uploadés pour ce client
     * sont listés en ligne (repliables) sous chaque numéro, comme
     * customer/company en CI (Customer::company()).
     */
    public function show(Customer $customer, CustomerCompany $company): View
    {
        return view('admin.customers.company', [
            'customer' => $customer,
            'company' => $company,
            'files' => $customer->documents()->where('company', $company->id)->orderByDesc('id')->get(),
        ]);
    }

    public function store(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'owner_contact' => ['nullable', 'string', 'max:255'],
            'rccm' => ['nullable', 'string', 'max:100'],
            'nif' => ['nullable', 'string', 'max:100'],
            'cni' => ['nullable', 'string', 'max:100'],
        ]);

        // `name` et `contact` sont NOT NULL en base (colonnes legacy) mais
        // ne doivent plus être obligatoires cote formulaire.
        $data['name'] ??= '';
        $data['contact'] ??= '';

        $customer->companies()->create($data + ['user' => auth()->id()]);

        return back()->with('success', 'Client ajouté.');
    }

    public function update(Request $request, Customer $customer, CustomerCompany $company): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'owner_contact' => ['nullable', 'string', 'max:255'],
            'rccm' => ['nullable', 'string', 'max:100'],
            'nif' => ['nullable', 'string', 'max:100'],
            'cni' => ['nullable', 'string', 'max:100'],
        ]);

        // `name` et `contact` sont NOT NULL en base (colonnes legacy) mais
        // ne doivent plus être obligatoires cote formulaire.
        $data['name'] ??= '';
        $data['contact'] ??= '';

        $company->update($data);

        return back()->with('success', 'Client mis à jour.');
    }

    public function destroy(Customer $customer, CustomerCompany $company): RedirectResponse
    {
        $company->delete();

        return back()->with('success', 'Client archivé.');
    }
}
