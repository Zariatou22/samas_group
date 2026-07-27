<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarOwner;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CarOwnerController extends Controller
{
    public function index(): View
    {
        return view('admin.car-owners.index');
    }

    public function data(): JsonResponse
    {
        $owners = CarOwner::withCount(['cars', 'drivers'])->orderBy('name')->get();

        $data = $owners->map(fn (CarOwner $o) => [
            'id' => $o->id,
            'name' => $o->name,
            'contact' => $o->contact,
            'address' => $o->address,
            'cars_count' => $o->cars_count,
            'drivers_count' => $o->drivers_count,
        ]);

        return response()->json(['data' => $data]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        // Dédoublonnage par nom+contact, comme Owners::set() en CI.
        $owner = CarOwner::query()->where('name', $data['name'])->where('contact', $data['contact'] ?? null)->first();

        if ($owner) {
            $owner->update($data);
        } else {
            $owner = CarOwner::create($data + ['user' => auth()->id()]);
        }

        return back()->with('success', 'Transporteur ajouté.')->with('newOwnerId', $owner->id);
    }

    public function update(Request $request, CarOwner $carOwner): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $carOwner->update($data);

        return back()->with('success', 'Transporteur mis à jour.');
    }

    public function destroy(CarOwner $carOwner): RedirectResponse
    {
        $carOwner->delete();

        return back()->with('success', 'Transporteur archivé.');
    }
}
