<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    public function index(): View
    {
        return view('admin.product-types.index');
    }

    public function data(): JsonResponse
    {
        return response()->json(['data' => ProductType::query()->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        ProductType::create($data + ['user' => auth()->id()]);

        return back()->with('success', 'Type de produit enregistré.');
    }

    public function update(Request $request, ProductType $productType): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $productType->update($data);

        return back()->with('success', 'Type de produit mis à jour.');
    }

    public function destroy(ProductType $productType): RedirectResponse
    {
        $productType->delete();

        return back()->with('success', 'Type de produit archivé.');
    }
}
