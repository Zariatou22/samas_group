<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarDriver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CarDriverController extends Controller
{
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
}
