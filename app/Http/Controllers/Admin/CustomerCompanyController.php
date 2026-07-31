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
    public function show(Customer $customer, CustomerCompany $company): View
    {
        return view('admin.customers.company', [
            'customer' => $customer,
            'company' => $company,
        ]);
    }

    public function store(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'owner_contact' => ['nullable', 'string', 'max:255'],
            'rccm' => ['nullable', 'string', 'max:100'],
            'nif' => ['nullable', 'string', 'max:100'],
            'cni' => ['nullable', 'string', 'max:100'],
        ]);

        $customer->companies()->create($data + ['user' => auth()->id()]);

        return back()->with('success', 'Client ajouté.');
    }

    public function update(Request $request, Customer $customer, CustomerCompany $company): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'owner_contact' => ['nullable', 'string', 'max:255'],
            'rccm' => ['nullable', 'string', 'max:100'],
            'nif' => ['nullable', 'string', 'max:100'],
            'cni' => ['nullable', 'string', 'max:100'],
        ]);

        $company->update($data);

        return back()->with('success', 'Client mis à jour.');
    }

    public function destroy(Customer $customer, CustomerCompany $company): RedirectResponse
    {
        $company->delete();

        return back()->with('success', 'Client archivé.');
    }
}
