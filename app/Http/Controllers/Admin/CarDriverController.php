<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarDriver;
use App\Models\CarOwner;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CarDriverController extends Controller
{
    public function index(): View
    {
        return view('admin.car-drivers.index', [
            'owners' => CarOwner::query()->orderBy('name')->get(),
        ]);
    }

    public function data(): JsonResponse
    {
        $drivers = CarDriver::with('carOwner')->orderBy('name')->get();

        $data = $drivers->map(fn (CarDriver $d) => [
            'id' => $d->id,
            'name' => $d->name,
            'contact' => $d->contact,
            'owner' => $d->owner,
            'owner_name' => $d->carOwner?->name,
        ]);

        return response()->json(['data' => $data]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:100'],
            'owner' => ['required', 'exists:car_owners,id'],
        ]);

        // Dédoublonnage par nom+contact (pas par transporteur), comme Drivers::set() en CI.
        $driver = CarDriver::query()->where('name', $data['name'])->where('contact', $data['contact'] ?? null)->first();

        if ($driver) {
            $driver->update($data);
        } else {
            $driver = CarDriver::create($data + ['user' => auth()->id()]);
        }

        return back()->with('success', 'Chauffeur ajouté.')->with('newDriverId', $driver->id);
    }

    public function update(Request $request, CarDriver $carDriver): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:100'],
            'owner' => ['required', 'exists:car_owners,id'],
        ]);

        $carDriver->update($data);

        return back()->with('success', 'Chauffeur mis à jour.');
    }

    public function destroy(CarDriver $carDriver): RedirectResponse
    {
        $carDriver->delete();

        return back()->with('success', 'Chauffeur archivé.');
    }
}
