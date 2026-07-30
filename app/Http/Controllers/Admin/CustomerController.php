<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerCompany;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(): View
    {
        return view('admin.customers.index');
    }

    public function data(): JsonResponse
    {
        $customers = Customer::withCount('documents')
            ->with(['companies' => fn ($q) => $q->select('id', 'customer', 'name')])
            ->orderBy('customer_name')
            ->get();

        $data = $customers->map(fn (Customer $c) => [
            'id' => $c->id,
            'customer_name' => $c->customer_name,
            'customer_contact' => $c->customer_contact,
            'email' => $c->email,
            'companies' => $c->companies->map(fn (CustomerCompany $company) => [
                'id' => $company->id,
                'name' => $company->name,
            ]),
            'documents_count' => $c->documents_count,
        ]);

        return response()->json(['data' => $data]);
    }

    public function create(): View
    {
        return view('admin.customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_contact' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'agent_name' => ['required', 'string', 'max:100'],
            'agent_contact' => ['required', 'string', 'max:100'],
        ]);

        $customer = Customer::create($data + ['user' => auth()->id()]);

        return redirect()->route('admin.customers.edit', $customer)->with('success', 'Mandataire enregistré.');
    }

    public function edit(Customer $customer): View
    {
        return view('admin.customers.edit', [
            'customer' => $customer->load(['companies', 'documents']),
        ]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_contact' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'agent_name' => ['required', 'string', 'max:100'],
            'agent_contact' => ['required', 'string', 'max:100'],
        ]);

        $customer->update($data);

        return redirect()->route('admin.customers.edit', $customer)->with('success', 'Mandataire mis à jour.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Mandataire archivé.');
    }

    public function transferForm(Customer $customer): View
    {
        return view('admin.customers.transfer', [
            'customer' => $customer->load('companies'),
            'targets' => Customer::query()->where('id', '!=', $customer->id)->orderBy('customer_name')->get(),
        ]);
    }

    /**
     * Transfert du mandataire entier (toutes ses sociétés) vers un autre
     * mandataire, comme Customer::transfert() en CI.
     */
    public function transfer(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'target' => ['required', 'exists:customers,id', 'not_in:'.$customer->id],
        ]);

        $target = Customer::findOrFail($data['target']);
        $customer->transferAllTo($target);

        return redirect()->route('admin.customers.index')->with('success', "Mandataire transféré vers {$target->customer_name}.");
    }

    /**
     * Transfert d'une seule société cliente vers un autre mandataire, comme
     * Customer::transfert_customer_company() en CI.
     */
    public function transferCompany(Request $request, Customer $customer, CustomerCompany $company): RedirectResponse
    {
        $data = $request->validate([
            'target' => ['required', 'exists:customers,id', 'not_in:'.$customer->id],
        ]);

        $target = Customer::findOrFail($data['target']);
        $customer->transferCompanyTo($company, $target);

        return redirect()->route('admin.customers.index')->with('success', "Société cliente transférée vers {$target->customer_name}.");
    }
}
