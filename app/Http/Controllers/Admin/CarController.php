<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarDriver;
use App\Models\CarOwner;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index(): View
    {
        return view('admin.cars.index');
    }

    public function data(): JsonResponse
    {
        $cars = Car::with(['carOwner', 'carDriver'])->orderBy('full_registration')->get();

        $data = $cars->map(fn (Car $c) => [
            'id' => $c->id,
            'full_registration' => $c->full_registration,
            'owner_name' => $c->carOwner?->name,
            'driver_name' => $c->carDriver?->name,
            'driver_contact' => $c->carDriver?->contact,
        ]);

        return response()->json(['data' => $data]);
    }

    public function create(): View
    {
        return view('admin.cars.create', [
            'owners' => CarOwner::query()->orderBy('name')->get(),
            'drivers' => CarDriver::query()->orderBy('name')->get(['id', 'name', 'owner']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'front_registration' => ['required', 'string', 'max:50'],
            'back_registration' => ['required', 'string', 'max:50'],
            'owner' => ['required', 'exists:car_owners,id'],
            'driver' => ['required', 'exists:car_drivers,id'],
        ]);

        // Dédoublonnage par immatriculation complète, comme Cars::set() en CI.
        $car = Car::query()
            ->where('front_registration', $data['front_registration'])
            ->where('back_registration', $data['back_registration'])
            ->first();

        if ($car) {
            $car->update($data);
        } else {
            Car::create($data + ['user' => auth()->id()]);
        }

        return redirect()->route('admin.cars.index')->with('success', 'Véhicule enregistré.');
    }

    public function edit(Car $car): View
    {
        return view('admin.cars.edit', [
            'car' => $car,
            'owners' => CarOwner::query()->orderBy('name')->get(),
            'drivers' => CarDriver::query()->orderBy('name')->get(['id', 'name', 'owner']),
        ]);
    }

    public function update(Request $request, Car $car): RedirectResponse
    {
        $data = $request->validate([
            'front_registration' => ['required', 'string', 'max:50'],
            'back_registration' => ['required', 'string', 'max:50'],
            'owner' => ['required', 'exists:car_owners,id'],
            'driver' => ['required', 'exists:car_drivers,id'],
        ]);

        $car->update($data);

        return redirect()->route('admin.cars.index')->with('success', 'Véhicule mis à jour.');
    }

    public function destroy(Car $car): RedirectResponse
    {
        $car->delete();

        return back()->with('success', 'Véhicule archivé.');
    }
}
