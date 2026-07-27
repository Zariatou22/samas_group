<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\Bl;
use App\Models\Source;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuthorizationController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.authorizations.index', ['activeTab' => $request->query('activeTab', 'all')]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = match ($request->query('activeTab', 'all')) {
            'ongoing' => Authorization::ongoing(),
            'settled' => Authorization::settled(),
            default => Authorization::query(),
        };

        $authorizations = $query->with(['parentBl', 'mandataire', 'customerCompany', 'pickupSource'])->orderByDesc('created')->get();

        $data = $authorizations->map(fn (Authorization $a) => [
            'id' => $a->id,
            'auth_number' => $a->auth_number,
            'bl' => $a->parentBl?->bl,
            'customer_name' => $a->mandataire?->customer_name,
            'customer_company_name' => $a->customerCompany?->name,
            'source_name' => $a->pickupSource?->name,
            'nb_container' => $a->nb_container,
            'nb_package' => $a->nb_package,
            'quantity' => $a->quantity,
            'is_settled' => $a->isSettled(),
        ]);

        return response()->json(['data' => $data]);
    }

    public function create(): View
    {
        return view('admin.authorizations.create', [
            'bls' => Bl::query()->orderByDesc('created')->get()->filter(fn (Bl $bl) => $bl->available_quantity > 0)->values(),
            'sources' => Source::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bl' => ['required', 'exists:bl,id'],
            'auth_number' => ['required', 'string', 'max:45', 'unique:authorization,auth_number'],
            'source' => ['required', 'exists:sources,id'],
            'nb_container' => ['required', 'numeric'],
            'nb_package' => ['required', 'numeric'],
            'quantity' => ['required', 'numeric'],
        ]);

        $bl = Bl::findOrFail($data['bl']);

        if ($bl->available_quantity <= 0) {
            return back()->withInput()->withErrors(['bl' => 'Ce BL ne dispose plus de capacité non déclarée.']);
        }

        Authorization::create($data + [
            'user' => auth()->id(),
            'customer' => $bl->customer,
            'customer_company' => $bl->customer_company,
        ]);

        return redirect()->route('admin.authorizations.index')->with('success', 'Déclaration enregistrée.');
    }

    public function edit(Authorization $authorization): View
    {
        return view('admin.authorizations.edit', [
            'authorization' => $authorization,
            'sources' => Source::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Authorization $authorization): RedirectResponse
    {
        $data = $request->validate([
            'auth_number' => ['required', 'string', 'max:45', 'unique:authorization,auth_number,'.$authorization->id],
            'source' => ['required', 'exists:sources,id'],
            'nb_container' => ['required', 'numeric'],
            'nb_package' => ['required', 'numeric'],
            'quantity' => ['required', 'numeric'],
        ]);

        $authorization->update($data);

        return redirect()->route('admin.authorizations.index')->with('success', 'Déclaration mise à jour.');
    }

    public function destroy(Authorization $authorization): RedirectResponse
    {
        $authorization->delete();

        return back()->with('success', 'Déclaration archivée.');
    }
}
