<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
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
        $customers = Customer::withCount(['companies', 'documents'])->orderBy('customer_name')->get();

        $data = $customers->map(fn (Customer $c) => [
            'id' => $c->id,
            'customer_name' => $c->customer_name,
            'customer_contact' => $c->customer_contact,
            'email' => $c->email,
            'companies_count' => $c->companies_count,
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
}
