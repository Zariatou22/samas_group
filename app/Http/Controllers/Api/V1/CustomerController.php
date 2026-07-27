<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerCompany;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function search(Request $request)
    {
        $q = $request->input('q', '');

        $customers = Customer::query()
            ->when($q, fn ($query) => $query->where('customer_name', 'like', "%{$q}%"))
            ->with('companies')
            ->get();

        return response()->json(['data' => $customers]);
    }

    public function searchCompanies(Request $request)
    {
        $q = $request->input('q', '');

        $companies = CustomerCompany::query()
            ->when($q, fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->get();

        return response()->json(['items' => $companies]);
    }

    public function searchCompaniesWithCommands(Request $request)
    {
        $q = $request->input('q', '');

        $companies = CustomerCompany::query()
            ->whereHas('bls')
            ->when($q, fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->get();

        return response()->json(['items' => $companies]);
    }

    public function users()
    {
        return response()->json(['data' => Customer::with('companies')->get()]);
    }

    public function companies(Request $request)
    {
        $customerId = $request->integer('id');

        if (! $customerId) {
            return response()->json(['success' => false, 'message' => 'Paramètres insuffisants']);
        }

        $companies = CustomerCompany::where('customer', $customerId)->get();

        if ($companies->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Aucune société trouvée pour ce client']);
        }

        return response()->json(['success' => true, 'message' => $companies]);
    }

    public function getCustomerCompany(Request $request)
    {
        $customerId = $request->integer('id');
        $name = $request->input('name');

        if (! $customerId || ! $name) {
            return response()->json(['success' => false, 'message' => 'Paramètres insuffisants']);
        }

        $company = CustomerCompany::where('customer', $customerId)->where('name', $name)->first();

        if (! $company) {
            return response()->json(['success' => false, 'message' => 'vide']);
        }

        return response()->json(['success' => true, 'message' => $company]);
    }
}
