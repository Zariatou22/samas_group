<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarOwner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CarOwnerController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:100'],
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
}
